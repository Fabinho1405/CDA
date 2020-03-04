<?php
	
	session_start();
	ob_start();
	include_once("../connection/connection.php");
	$pdo=conectar();

	$btnLogin = filter_input(INPUT_POST, 'btn_login', FILTER_SANITIZE_STRING);
	$ip_log = $_SERVER['REMOTE_ADDR'];

	if($btnLogin){
		$usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
		$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

		//echo "$usuario - $senha" ;

		if(!empty($usuario) AND !empty($senha)){
			$verifConta=$pdo->prepare("SELECT * FROM funcionario WHERE CPF_func = :usuario LIMIT 1");
			$verifConta->bindValue(":usuario", $usuario);
			$verifConta->execute();
			$qtdRetornoConta=$verifConta->rowCount();

			if($qtdRetornoConta == 1){
				$infoUsuario=$verifConta->fetch(PDO::FETCH_OBJ);

					if(password_verify($senha, $infoUsuario->senha_func)){
						if($infoUsuario->status_sistema == 1 && $infoUsuario->newAcess == 1){							
								//GLOBAIS
								$tempolimite = 600; //equivale a 10 segundos
								$_SESSION['registro'] = time();
								$_SESSION['limite'] = $tempolimite;
								
								$_SESSION['idUsuario'] = $infoUsuario->id_func; 
								$_SESSION['cpfUsuario'] = $infoUsuario->CPF_func;
								$_SESSION['nomeCompletoUsuario'] = $infoUsuario->nome_completo_func;
								$_SESSION['unidadeUsuario'] = $infoUsuario->id_unidade;
								$_SESSION['autorizacaoFinanceira'] = $infoUsuario->aut_financeiro;
								$_SESSION['allowPages'] = explode(";", $infoUsuario->allowPages);

								//LOG DE ACESSO AO SISTEMA
								$ipLog = $_SERVER['REMOTE_ADDR'];
								$logAcesso=$pdo->prepare("INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), :ipLog, 'Acessou o Sistema', 'NOTIFICAÇÃO', :idFuncionario");
								$logAcesso->bindValue(":ipLog", $ipLog);
								$logAcesso->bindValue(":idFuncionario", $_SESSION['idUsuario']);
								$logAcesso->execute();

								//REGISTRA O PRIMEIRO ACESSO DO DIA
								$primeiroAcesso=$pdo->prepare("SELECT * FROM funcionario WHERE date(primeiro_acesso_dia) <> date(NOW()) AND id_func-:idFuncionario");
								$primeiroAcesso->bindValue(":idFuncionario", $_SESSION['idUsuario']);
								$primeiroAcesso->execute();
								$qtdRetornoAcesso=$primeiroAcesso->rowCount();

								if($qtdRetornoAcesso >= 1){
									$updatePrimeiroAcesso=$pdo->prepare("UPDATE funcionario SET primeiro_acesso_dia = NOW() WHERE id_funcionario=:idFuncionario");
									$updatePrimeiroAcesso->bindValue(":idFuncionario", $_SESSION['idFuncionario']);
								}else{

								}

								header("Location: ../index");
							
						}else{
						$_SESSION['msgLogin'] = "<div class='alert alert-warning' role='alert'>
                                           Conta<b>Desativada</b> ! Entre em contato com o seu gestor para mais informações.
                             </div>";
                        //LOG DE ACESSO AO SISTEMA
						$ip_log = $_SERVER['REMOTE_ADDR'];
						$idfuncionario = $_SESSION['id_usuario'];
							$insert_log = "INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), '$ip_log', 'conta restringida tentando acessar', 'ALERTA', 0);";
							$exec_insert_log = mysqli_query($conn, $insert_log);
						//FIM DO LOG DE ACESSO
						//header("Location: ../loginpage.php");
						}
					}else{
						$_SESSION['msg'] = "<div class='alert alert-info' role='alert'>
                                            Login e Senha INCORRETOS!
                             </div>";
                             //LOG DE ACESSO AO SISTEMA
						$ip_log = $_SERVER['REMOTE_ADDR'];						
							$insert_log = "INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), '$ip_log', 'login ou senha incorretos', 'ALERTA', 0);";
							//$exec_insert_log = mysqli_query($conn, $insert_log);
						//FIM DO LOG DE ACESSO

						//header("Location: ../loginpage.php");
					}
			}


		}else{
			$_SESSION['msg'] = "<div class='alert alert-info' role='alert'>
                                            Login e Senha INCORRETOS!
                             </div>";
             //LOG DE ACESSO AO SISTEMA
						$ip_log = $_SERVER['REMOTE_ADDR'];					
							$insert_log = "INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), '$ip_log', 'login ou senha incorretos', 'ALERTA', 0);";
							//$exec_insert_log = mysqli_query($conn, $insert_log);
			//FIM DO LOG DE ACESSO                 
		//header("Location: ../loginpage.php");
		}
	}else{
		$_SESSION['msg'] = "<div class='alert alert-danger' role='alert'>
                                            Conecte-se corretamente ou contate o Suporte Tecnico central.
                             </div>";
         //LOG DE ACESSO AO SISTEMA
						$ip_log = $_SERVER['REMOTE_ADDR'];
					
							$insert_log = "INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), '$ip_log', 'tentativa de acesso a pagina nao autorizada', 'PERIGO', 0);";
							$exec_insert_log = mysqli_query($conn, $insert_log);
						//FIM DO LOG DE ACESSO
		//header("Location: ../loginpage.php");

	}


	


?>