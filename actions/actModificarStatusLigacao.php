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
			$encaminhaAgendamento->bindValue(":idControle", $idControle);
			$encaminhaAgendamento->bindValue(":idFunc", $idfuncionario);
			$encaminhaAgendamento->execute();
			$qtdRetorno=$encaminhaAgendamento->rowCount();
			if($qtdRetorno > 0){
				$linhaAgenda=$encaminhaAgendamento->fetch(PDO::FETCH_OBJ);		

				header("Location:../cadastrar_agendamento_ligacao.php?idcontrole=".$linhaAgenda->id_controle."&nomeresponsavel=".$linhaAgenda->nome_responsavel_controle."&nomemodelo=".$linhaAgenda->nome_modelo_controle."&telefoneprincipal=".$linhaAgenda->telefone_principal_controle."&telefonesecundario=".$linhaAgenda->telefone_secundario_controle);
			}else{
				$_SESSION["msgRetornoLista"] = "<div class='alert alert-warning' role='alert'>
                                          <center> Esta ficha aparentemente não se encontra mais com você, fale com seu supervisor; </center>";
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

	        //PAREI AQUI, PROGRAMAR DEPOIS E NÃO ESQUECER DE NÃO FAZER MERDA. 
	        //             SE HIDRATEM E NÃO USEM DROGAS
	        //              \/ \/ \/ \/ \/ SEXTEI TODAH

	                                  

	        $update_ultimo_fedback = "UPDATE controle_ligacao SET ultimo_fedback = NOW(), qtd_feedback = '$qtd_final_n_fedback' WHERE id_controle = '$idcontrole'";
	        $exec_update_ultimo_fedback = mysqli_query($conn, $update_ultimo_fedback);
	        header("Location: ../lista_telefonica.php");

		}else if($select == 5 || $select == 7){
			header("Location:../motivo_ficha_si.php?idcontrole=$idcontrole");
			
		}

 	}else{
        $_SESSION['msgNotPermission'] = "<div class='alert alert-info' role='alert'>
                                            Você Não Tem Permissão de Acesso!
                             </div>";
        header("Location: ../index");

    }




?>