<?php                         
    session_start();
    if(!empty($_SESSION['idUsuario']) AND in_array("cadastrarAgendamentoLigacao",$_SESSION['allowPages'])){
        include_once("../connection/connection.php");
        include_once("../phpFunctions/verificarSessao.php");
        $pdo=conectar();
        function limpaAcentos($valor){
            $valor = trim($valor);
            $valor = str_replace(".", "", $valor);
            $valor = str_replace("-", "", $valor);
            $valor = str_replace("(", "", $valor);
            $valor = str_replace(")", "", $valor);
            $valor = str_replace("/", "", $valor);
            return $valor;
        };        

        $idfuncionario = $_SESSION['idUsuario'];
        $unidade = $_SESSION['unidadeUsuario'];

        if(isset($_GET['idficha'])){
            $idficha = $_GET['idficha'];
        }

        $nome_modelo = $_POST['nomecliente'];
        $idade = $_POST['idade_cliente'];
        $telefone_principal = limpaAcentos($_POST['telefoneprincipal']);
        $telefone_secundario = limpaAcentos($_POST['telefonesecundario']);
        $nome_responsavel = $_POST['responsavel'];
        $data_agendamento = $_POST['data_agendado'];
        $hora_agendamento = $_POST['hora_agendado'];
        $select_unidade = $_POST['select_unidade'];
        $dataAtual = date("Y-m-d");
        $diasemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabado');
        $diasemanaNumber = date('w', strtotime($data_agendamento));

        //Verifica se a data agendada é anterior a atual
        if($data_agendamento >= $dataAtual){
            //Verifica se o agendamento é algum domingo
            if($diasemanaNumber <> 0){
                if($diasemanaNumber <> 6){
                    $horaAbertura = "10:00:00";
                    $horaFechamento= "18:00:00";
                }else{
                    $horaAbertura = "10:00:00";
                    $horaFechamento = "17:00:00";
                };

                if($hora_agendamento >= $horaAbertura && $hora_agendamento <= $horaFechamento){
                    //VERIFICA DUPLICIDADE NO NÚMERO DE TELEFONE
                    $duplicidadeNumero=$pdo->prepare("SELECT * FROM agendamentos ag INNER JOIN cliente cli ON ag.id_cliente = cli.id_cliente WHERE DATEDIFF(DATE(NOW()), DATE(cli.data_cadastro_cliente)) <= 90 AND cli.telefone_cliente = :telefonePesquisa AND ag.id_unidade = :selectUnidade");
                    $duplicidadeNumero->bindValue(":telefonePesquisa", $telefone_principal);
                    $duplicidadeNumero->bindValue(":selectUnidade", $unidade);
                    $duplicidadeNumero->execute();
                    $qtdRetornoDuplicidade=$duplicidadeNumero->rowCount();
                    if($cont_linha_result > 0){
                        //TEM DUPLICIDADE EM RELAÇÃO AO NÚMERO
                        $_SESSION['msgcadLigacao'] = "<div class='alert alert-warning' role='alert'>Cliente já cadastrado em sua unidade em um período menor do estipulado. Entre em contato com o seu supervisor(a), apenas ele(a) poderá lhe ajudar. #14006</div>";
                        header("Location: ".$_SERVER['HTTP_REFERER']."");
                    }else{ 
                        //Insere no LOG apenas de ligação que a ficha foi agendada
                        $numeroFeedback=$pdo->prepare("SELECT * FROM controle_fb_ligacao WHERE id_ficha = :idFicha");
                        $numeroFeedback->bindValue(":idFicha", $idficha);
                        $numeroFeedback->execute();
                        $qtdRetornoFeedback=$numeroFeedback->rowCount();
                        $qtdFinalRetornoFeedback=$qtdRetornoFeedback + 1;
                        
                        $inserirLogAgendado=$pdo->prepare("INSERT controle_fb_ligacao(id_func, num_fedback, id_unidade, hora_ligacao, id_ficha,status) VALUES (:idFuncionario,:qtdFinalFeedback, :unidade, NOW(), :idFicha, 2)");
                        $inserirLogAgendado->bindValue(":idFuncionario", $idfuncionario);
                        $inserirLogAgendado->bindValue(":qtdFinalFeedback", $qtdFinalRetornoFeedback);
                        $inserirLogAgendado->bindValue(":unidade", $unidade);
                        $inserirLogAgendado->bindValue(":idFicha", $idficha);
                        $inserirLogAgendado->execute();

                        //Desabilitar Ficha do Sistema
                        $desabilitaFicha=$pdo->prepare("UPDATE controle_ligacao SET id_status_sistema = 0, id_extracao = 1, id_func = '$idfuncionario', data_extracao = NOW(), ultimo_fedback = NOW(), qtd_feedback = :qtdFinalFeedback WHERE id_controle = :idFicha");
                        $desabilitaFicha->bindValue(":qtdFinalFeedback", $qtdRetornoFeedback);
                        $desabilitaFicha->bindValue(":idFicha", $idficha);
                        $desabilitaFicha->execute();

                        // Cadastrar o Cliente
                        $insereCliente=$pdo->prepare("INSERT INTO cliente (nome_cliente, telefone_cliente,telefone2_cliente,idade_cliente,nome_responsavel_cliente, id_meio_captado, data_cadastro_cliente,id_func) VALUES (:nomeModelo,:telefonePrinc,:telefoneSec,:idade ,:nomeRespons,'3',NOW(),:idFuncionario)");
                        $insereCliente->bindValue(":nomeModelo", $nome_modelo);
                        $insereCliente->bindValue(":telefonePrinc", $telefone_principal);
                        $insereCliente->bindValue(":telefoneSec", $telefonesecundario);
                        $insereCliente->bindValue(":idade", $idade);
                        $insereCliente->bindValue(":nomeRespons", $nome_responsavel);
                        $insereCliente->bindValue(":idFuncionario", $idfuncionario);
                        
                        if($insereCliente->execute()){                            
                            // Procurar Novo Cliente
                            $novoCliente=$pdo->prepare("SELECT * FROM cliente WHERE nome_cliente = :nomeModelo AND telefone_cliente = :telefoneCliente AND date(data_cadastro_cliente) = date(NOW())");
                            $novoCliente->bindValue(":nomeModelo", $nome_modelo);
                            $novoCliente->bindValue(":telefoneCliente", $telefone_principal);
                            $novoCliente->execute();
                            $rowCliente=$novoCliente->fetch(PDO::FETCH_OBJ);
                            $idCliente=$rowCliente->id_cliente;

                            //Cadastrar Agendamento Com Novo Cliente
                            $data_agendamento_ajuste = date('Y-m-d', strtotime($data_agendamento));
                            $insereAgendamento=$pdo->prepare("INSERT INTO agendamentos (id_agendamentos,data_agendada_agendamento,hora_agendada_agendamento,data_cadastro_agendamento,id_conta_utilizada,id_cliente, id_meio_captado, id_status_auditoria,id_status_sistema, id_func, id_comparecimento, id_unidade, confirmado, id_ficha) VALUES (NULL, :dataAjustada, :horaAgendamento, NOW(), NULL, :idNovoCliente, '3', '2', '1', :idFuncionario, '3', :selectUnidade, '0', :idFicha)");
                            $insereAgendamento->bindValue(":dataAjustada", $data_agendamento_ajuste);
                            $insereAgendamento->bindValue(":horaAgendamento", $hora_agendamento);
                            $insereAgendamento->bindValue(":idNovoCliente", $idCliente);
                            $insereAgendamento->bindValue(":idFuncionario", $idfuncionario);
                            $insereAgendamento->bindValue(":selectUnidade", $select_unidade);
                            $insereAgendamento->bindValue(":idFicha", $idficha);
                            $insereAgendamento->execute();                            

                            $_SESSION['msgcadLigacao'] = "<div class='alert alert-success' role='alert'>
                                                            Registrado com Sucesso!
                                             </div>";

                            //LOG                                   
                            $ip_log = $_SERVER['REMOTE_ADDR'];
                            $idfuncionario = $_SESSION['idUsuario'];
                            $insereLog=$pdo->prepare("INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), :ipLog, 'agendamento inserido LIG -> CLI: $id_new_cliente | FICH_OFF: $idficha', 'ALERTA', :idFuncionario)");
                            $insereLog->bindValue(":ipLog", $ip_log);
                            $insereLog->bindValue(":idFuncionario", $idfuncionario);
                            $insereLog->execute();
                            header("Location: ".$_SERVER['HTTP_REFERER']."");
                        }else{
                            $_SESSION['msgcadLigacao'] = "<div class='alert alert-info' role='alert'>
                                            Erro ao adicionar o cliente, entre em contato com seu supervisor.
                             </div>";
                            header("Location: ".$_SERVER['HTTP_REFERER']."");
                        }
                    }
                }else{
                    $_SESSION['msgcadLigacao'] = "<div class='alert alert-info' role='alert'>
                                            O horário do seu agendamento está fora do horário de funcionamento da Agência. Entre em contato com seu supervisor para mais detalhes.
                             </div>";
                            header("Location: ".$_SERVER['HTTP_REFERER']."");
                }
            }else{
                $_SESSION['msgcadLigacao'] = "<div class='alert alert-info' role='alert'>
                                            Temporariamente, os agendamentos aos domingos estão suspensos.
                             </div>";
                             echo $diasemanaNumber;
                header("Location: ".$_SERVER['HTTP_REFERER']."");

            }
        }else{
            $_SESSION['msgcadLigacao'] = "<div class='alert alert-info' role='alert'>
                                            A data do agendamento, não pode ser anterior a sua data atual.
                             </div>";          
            
            header("Location: ".$_SERVER['HTTP_REFERER']."");
        }
    }else{
        $_SESSION['msgNotPermission'] = "<div class='alert alert-info' role='alert'>
                                            Você Não Tem Permissão de Acesso!
                             </div>";
        header("Location: ../index");

    }
                        


                            
                            







?>