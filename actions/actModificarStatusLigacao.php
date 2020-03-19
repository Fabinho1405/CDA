<?php
	session_start();
	ob_start();

	if(!empty($_SESSION['idUsuario']) AND in_array("listaTelefonicaLigacao",$_SESSION['allowPages'])){
		include_once("../connection/connection.php");
    	include_once("../phpFunctions/verificarSessao.php");
    	$pdo=conectar();
	
		$select = $_POST['select_status'];
		$idcontrole = $_GET['idcontrole'];
		$usuario = $_SESSION['idUsuario'];
		$unidade = $_SESSION['unidadeUsuario'];



		if($select == 2){
			$encaminhaAgendamento=$pdo->prepare("SELECT * FROM controle_ligacao WHERE id_controle = :idControle AND id_func = :idFunc");
			$encaminhaAgendamento->bindValue(":idControle", $idcontrole);
			$encaminhaAgendamento->bindValue(":idFunc", $usuario);
			$encaminhaAgendamento->execute();
			$qtdRetorno=$encaminhaAgendamento->rowCount();
			if($qtdRetorno > 0){
				$linhaAgenda=$encaminhaAgendamento->fetch(PDO::FETCH_OBJ);		

				header("Location:../cadastrarAgendamentoLigacao?idcontrole=".$linhaAgenda->id_controle."&nomeresponsavel=".$linhaAgenda->nome_responsavel_controle."&nomemodelo=".$linhaAgenda->nome_modelo_controle."&telefoneprincipal=".$linhaAgenda->telefone_principal_controle."&telefonesecundario=".$linhaAgenda->telefone_secundario_controle);
			}else{
				$_SESSION["msgRetornoLista"] = "<div class='alert alert-warning' role='alert'>
                                          <center> Esta ficha aparentemente não se encontra mais com você, fale com seu supervisor; </center>";
                //LOG                                   
                            $ip_log = $_SERVER['REMOTE_ADDR'];
                            $idfuncionario = $_SESSION['idUsuario'];
                            $insereLog=$pdo->prepare("INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), :ipLog, 'O usuario tentou seguir para tela de agendamento, com uma ficha que nao ertence mais a ele', 'PERIGO', :idFuncionario)");
                            $insereLog->bindValue(":ipLog", $ip_log);
                            $insereLog->bindValue(":idFuncionario", $idfuncionario);
                            $insereLog->execute();
                header("Location: ../listaTelefonica");
			}
		}else if($select == 3 || $select == 4 || $select == 6 || $select == 8 || $select == 5 || $select == 9){
			$updateFeedback=$pdo->prepare("SELECT * FROM controle_fb_ligacao WHERE id_ficha = :idFicha");
			$updateFeedback->bindValue(":idFicha", $idControle);
			$updateFeedback->execute();
			$qtdFeedback=$updateFeedback->rowCount();
			$qtdFinalFeedback=$qtdFeedback+1;
			
	        $logFeedbackFicha=$pdo->prepare("INSERT controle_fb_ligacao(id_func, num_fedback, id_unidade, hora_ligacao, id_ficha,status) VALUES ($usuario,$qtd_final_n_fedback,$unidade,NOW(),$idcontrole, $select)");
	        $logFeedbackFicha->bindValue(":idFunc", $usuario);
	        $logFeedbackFicha->bindValue(":qtdFinalFB", $qtdFinalFeedback);
	        $logFeedbackFicha->bindValue(":unidadeUser", $unidade);
	        $logFeedbackFicha->bindValue(":idControle", $idcontrole);
	        $logFeedbackFicha->bindValue(":selecionado", $select);
	        $logFeedbackFicha->execute();

	        //LOG                                   
                            $ip_log = $_SERVER['REMOTE_ADDR'];
                            $idfuncionario = $_SESSION['idUsuario'];
                            $insereLog=$pdo->prepare("INSERT INTO logs (datetime_log, ip_user, mensagem_log, tipo_log, id_func) VALUES (NOW(), :ipLog, 'O usuario deu um Feedback. || FICH: $idcontrole', 'ALERTA', :idFuncionario)");
                            $insereLog->bindValue(":ipLog", $ip_log);
                            $insereLog->bindValue(":idFuncionario", $idfuncionario);
                            $insereLog->execute();

	        $updateFeedback=$pdo->prepare("UPDATE controle_ligacao SET ultimo_fedback = NOW(), qtd_feedback = :qtdFinal WHERE id_controle = :idControle");
	        $updateFeedback->bindValue(":qtdFinal", $qtdFinalFeedback);
	        $updateFeedback->bindValue(":idControle", $idcontrole);
	        $updateFeedback->execute();

	        header("Location: ../listaTelefonica");

		}else if($select == 5 || $select == 7){
			header("Location:../motivoFichaSi?idcontrole=$idcontrole");
			
		}

 	}else{
        $_SESSION['msgNotPermission'] = "<div class='alert alert-info' role='alert'>
                                            Você Não Tem Permissão de Acesso!
                             </div>";
        header("Location: ../index");

    }




?>