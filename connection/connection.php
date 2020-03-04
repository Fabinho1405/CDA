<?php
		function conectar(){
			try{
			$pdo = new PDO("mysql:host=51.79.99.148;dbname=cda_dbmodels", "cda_Fabio","Fabinho140598", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_PERSISTENT => true));
			}catch(PDOException $e){
				echo $e->getMessage(); 
			}
			return $pdo;
		}		
?>


