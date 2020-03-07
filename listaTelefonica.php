<?php
    session_start();
   if(!empty($_SESSION['idUsuario']) AND in_array("listaTelefonicaLigacao",$_SESSION['allowPages'])){
    include_once("connection/connection.php");
    include_once("phpFunctions/verificarSessao.php");
    $idfuncionario = $_SESSION['idUsuario'];
    $unidade = $_SESSION['unidadeUsuario'];
    $pdo=conectar();


?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv=”content-type” content=”text/html; charset=UTF-8″ />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Models Painel Admin</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="favicon.ico">

    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <!-- <link rel="stylesheet" href="assets/css/bootstrap-select.less"> -->
    <link rel="stylesheet" href="assets/scss/style.css">
    <link href="assets/css/lib/vector-map/jqvmap.min.css" rel="stylesheet">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
    <script type="text/javascript" src="js/javascriptpersonalizado.js"></script>

     <link href="CSS/uploadfilemulti.css" rel="stylesheet">

    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</head>
<body>
   <?php
        include_once("includes/incMenus.php");
    ?>
    <div id="right-panel" class="right-panel">

        <!-- Header-->
         <?php
            include_once("includes/incHeader.php");
         ?><!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                            <li class="active"><?php echo "São Paulo, ".date("d/m/Y"); ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
   
            <div class="animated fadeIn">
                <div class="row">
                <div class="col-md-12"> 
                    <div class="card">
                        <div class="card-header">
                            <?php
                                $fichasLiberadas=$pdo->prepare("SELECT * FROM controle_ligacao WHERE id_func = :idFunc AND id_status_sistema = 1 AND id_extracao = 0 AND liberado_controle_externo = 0 AND date(data_liberada_stand_by) = date(NOW()) ORDER BY qtd_feedback, id_controle ASC ");
                                $fichasLiberadas->bindValue(":idFunc", $idfuncionario);
                                $fichasLiberadas->execute();
                                $qtdLiberadas=$fichasLiberadas->rowCount();
                                
                            ?>
                            <strong class="card-title">Lista</strong> Telefônica - Total de Fichas: <?php echo $qtdLiberadas; ?> - <small>Aqui você poderá ligar para fichas que foram liberadas a você no dia de hoje.</small>
                        </div>

                        <div class="card-body">
                             <?php
                            if(isset($_SESSION['msgRetornoLista'])){
                                echo $_SESSION['msgRetornoLista'];
                                unset($_SESSION['msgRetornoLista']);
                            };
                        ?>
                
                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th>Modelo</th>
                        <th>Responsável</th>
                        <th>Telefone</th>
                        <th>Telefone 2</th>
                        <th>Resposta </th>
                        <th>+</th>
                      </tr>
                    </thead>                  
                        <?php  
                             $result_total_1_fb = "";      
                             $resultFichas=$pdo->prepare("SELECT * FROM controle_ligacao WHERE id_func = :idFunc AND id_status_sistema = 1 AND id_extracao = 0 AND liberado_controle_externo = 0 AND date(data_liberada_stand_by) = date(NOW()) ORDER BY qtd_feedback, id_controle ASC LIMIT 1");
                             $resultFichas->bindValue(":idFunc", $idfuncionario);
                             $resultFichas->execute();
                             $linhaFicha=$resultFichas->fetchAll(PDO::FETCH_OBJ);

                            foreach($linhaFicha as $rowFicha){
                        ?>
                    <tbody>
                      <tr>
                        <td><?php  echo $rowFicha->nome_modelo_controle; ?></td>
                        <td><?php  echo $rowFicha->nome_responsavel_controle; ?></td>
                        <td>
                            <?php  echo $rowFicha->telefone_principal_controle; ?>
                            <?php
                               if(strlen($rowFicha->telefone_principal_controle) > 10){
                                $telefone = $rowFicha->telefone_principal_controle;
                                $modelo = $rowFicha->nome_modelo_controle;
                            ?>
                            <a href="https://api.whatsapp.com/send?phone=55<?php echo $telefone; ?>&text=Ol%C3%A1%20<?php echo $modelo; ?>%2C%20tudo%20bem%3F%20Sou%20da%20<?php if($unidade == 1){echo 'Agency Exclusive'; }else{echo 'Agency Concept';} ?>%2C%20recebi%20o%20seu%20cadastro%20atrav%C3%A9s%20de%20nosso%20sistema%20e%20estamos%20com%20algumas%20sele%C3%A7%C3%B5es%20abertas%20para%20marcas%20e%20comerciais%2C%20ser%C3%A1%20que%20poder%C3%ADamos%20conversar%3F" target="_blank"><img src="images/envio_wts.png" width="32px" height="32px"></a>
                            <?php
                                };
                            ?>
                        </td>
                        <td>
                            <?php  echo $rowFicha->telefone_secundario_controle; ?>
                            <?php
                               if(strlen($rowFicha->telefone_secundario_controle) > 10){
                                $telefone = $rowFicha->telefone_secundario_controle;
                                $modelo = $rowFicha->nome_modelo_controle;
                            ?>
                            <a href="https://api.whatsapp.com/send?phone=55<?php echo $telefone; ?>&text=Ol%C3%A1%20<?php echo $modelo; ?>%2C%20tudo%20bem%3F%20Sou%20da%20<?php if($unidade == 1){echo 'Agency Exclusive'; }else{echo 'Agency Concept';} ?>%2C%20recebi%20o%20seu%20cadastro%20atrav%C3%A9s%20de%20nosso%20sistema%20e%20estamos%20com%20algumas%20sele%C3%A7%C3%B5es%20abertas%20para%20marcas%20e%20comerciais%2C%20ser%C3%A1%20que%20poder%C3%ADamos%20conversar%3F" target="_blank"><img src="images/envio_wts.png" width="32px" height="32px"></a>
                            <?php
                                };
                            ?>
                        </td>              
                        <td>
                            <form name="formstatus" action="actions/actModificarStatusLigacao.php?idcontrole=<?php echo $rowFicha->id_controle; ?>" method="POST">
                                <select name="select_status" style="width: 400px;" class="form-control">   
                                    <option value="1"> Em Aberto </option>
                                    <option value="2"> Agendado </option>
                                    <option value="3"> Caixa Postal </option>        
                                    <option value="4"> Não Atende </option>
                                    <option value="5"> DDD de Fora </option>
                                    <option value="6"> Sem Resposta </option>
                                    <option value="7"> Sem Interesse </option>
                                    <option value="8"> Desligou </option>
                                    <option value="9"> Contato Via Whatsapp </option>
                                </select>                            
                        </td>
                        <td>
                            <input type="submit" value=">" class="btn btn-success btn-lg">
                        </td>                 
                      </tr>
                            </form>
                    </tbody>
                    <?php
                        };
                    ?>
                </table>


                        </div>
                    </div>
                </div>
                </div>
            </div><!-- .animated -->

<!-- FIM PAGE -->      
            </div>
         <!-- .content -->
    </div><!-- /#right-panel -->


    <!-- Right Panel -->

    <script src="assets/js/vendor/jquery-2.1.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>


    <script src="assets/js/lib/chart-js/Chart.bundle.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/widgets.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.min.js"></script>
    <script src="assets/js/lib/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="assets/js/lib/vector-map/country/jquery.vmap.world.js"></script>
    <script>
        ( function ( $ ) {
            "use strict";

            jQuery( '#vmap' ).vectorMap( {
                map: 'world_en',
                backgroundColor: null,
                color: '#ffffff',
                hoverOpacity: 0.7,
                selectedColor: '#1de9b6',
                enableZoom: true,
                showTooltip: true,
                values: sample_data,
                scaleColors: [ '#1de9b6', '#03a9f5' ],
                normalizeFunction: 'polynomial'
            } );
        } )( jQuery );



    </script>

</body>
</html>
<script src="JS/jquery.fileuploadmulti.min.js"></script>
<?php
    }else{
        $_SESSION['msgNotPermission'] = "<div class='alert alert-info' role='alert'>
                                            Você Não Tem Permissão de Acesso!
                             </div>";
        header("Location: index");

    }

?>