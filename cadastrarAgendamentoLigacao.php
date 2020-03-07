<?php
    session_start();
   if(!empty($_SESSION['idUsuario']) AND in_array("cadastrarAgendamentoLigacao",$_SESSION['allowPages'])){
    include_once("connection/connection.php");
    include_once("phpFunctions/verificarSessao.php");
    $idfuncionario = $_SESSION['idUsuario'];
    $unidadefunc = $_SESSION['unidadeUsuario'];
    $pdo=conectar();
?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Models Painel Admin</title>
    <meta name="description" content="Sufee Admin - HTML5 Admin Template">
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
<!--  INICIO DE FORM DE CADASTRO  -->
     <?php
                        if(isset($_SESSION['msgcadLigacao'])){
                            echo $_SESSION['msgcadLigacao'];
                            unset($_SESSION['msgcadLigacao']);
                        };
                        ?>

        <div class="content mt-3">
            <div class="col-sm-12" >
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-header">
                        
                        <strong>Cadastro</strong> de Agendamento Ligação <small>Cadastre aqui seus agendamentos.</small>                 
                      </div>
                      <div class="card-body card-block">
                        <form action="actions/act_cadastrar_agendamento_ligacao.php?<?php 
                        if(!empty($_GET['idcontrole'])){echo "idficha=".$_GET['idcontrole'];}else{ }
                        ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Nome:<b><font color="red">*</font></b></labelcan></div>
                            <div class="col-12 col-md-9">                              
                           <input type="text" id="email-input" name="nomecliente" placeholder="" class="form-control" value="<?php
                           if(!empty($_GET['nomemodelo'])){echo $_GET['nomemodelo']; }else{echo "";} ?>" required>
                            </div>
                          </div>
                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Idade:<b><font color="red">*</font></b> </label></div>
                            <div class="col-12 col-md-9"><input type="text" id="idade" name="idade_cliente" placeholder="" class="form-control" value="" maxlength="2" required=""><small class="form-text text-muted"></small></div>
                          </div>
                         
                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Telefone Principal:<b><font color="red">*</font></b></label></div>
                            <div class="col-12 col-md-9"><input type="text" id="celular" name="telefoneprincipal" placeholder="" class="form-control" value="<?php 
                            if(!empty($_GET['telefoneprincipal'])){echo $_GET['telefoneprincipal'];}else{echo "";} ?>" required></div>
                          </div>
                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Telefone Secundário: </label></div>
                            <div class="col-12 col-md-9"><input type="text" id="celular2" name="telefonesecundario" placeholder="" class="form-control" value="<?php 
                            if(!empty($_GET['telefonesecundario'])){echo $_GET['telefonesecundario'];}else{echo "";} ?>"></div>
                          </div>

                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Nome do Responsável:</label></div>
                            <div class="col-12 col-md-9"><input type="text" id="text-input" name="responsavel" placeholder="" class="form-control" value="<?php
                            if(!empty($_GET['nomeresponsavel'])){echo $_GET['nomeresponsavel'];}else{echo "";} ?>"></div>
                            
                          </div>                                      
                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Data de agendamento:<b><font color="red">*</font></b> </label></div>
                            <div class="col-12 col-md-9"><input type="date" id="text-input" name="data_agendado" placeholder="" class="form-control" required><small class="form-text text-muted"></small></div>
                          </div>                               
                          <div class="row form-group">
                            <div class="col col-md-3"><label for="text-input" class=" form-control-label">Hora agendada:<b><font color="red">*</font></b> </label></div>
                            <div class="col-12 col-md-9"><input type="time" id="text-input" name="hora_agendado" placeholder="" class="form-control" required><small class="form-text text-muted"></small></div>
                          </div> 
                          <div class="row form-group">
                            <div class="col col-md-3"><label class=" form-control-label">Unidade:<b><font color="red">*</font></b></label></div>
                            <div class="col-12 col-md-9">
                              <select name="select_unidade" id="select" class="form-control">
                                <?php 
                                    $selectUnidade=$pdo->prepare("SELECT id_unidade, desc_unidade FROM unidade WHERE id_unidade = :unidadeFunc");
                                    $selectUnidade->bindValue(":unidadeFunc", $unidadefunc);
                                    $selectUnidade->execute();
                                    $linhaUnidade=$selectUnidade->fetchAll(PDO::FETCH_OBJ);
                                    foreach($linhaUnidade as $rowUnidade){
                                    
                                ?>
                                <option value="<?php echo $rowUnidade->id_unidade; ?>"><?php echo $rowUnidade->desc_unidade;  ?></option>
                               <?php
                                    };
                               ?>
                              </select>
                            </div>
                          </div>       

                      </div>
                      <div class="card-footer">
                        <button type="submit" name="" class="btn btn-outline-success">
                          <i class="fa fa-dot-circle-o"></i> Cadastrar Agendamento
                        </button>
                        </form>                       
                      </div>
                    </div>

        <!-- FIM DE FORM DE CADASTRO -->
       

            </div>
        </div>
        </div> <!-- .content -->
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#rg").mask("00.000.000-0");
            $("#cpf").mask("000.000.000-00");
            $("#rg2").mask("00.000.000-0");
            $("#cpf2").mask("000.000.000-00");
            $("#celular").mask("(00)00000-0000");
            $("#celular2").mask("(00)00000-0000");
            $("#residencial").mask("(00)0000-0000");
            $("#altura").mask("0.00");
            $("#peso").mask("00.00");
            $("#sapatos").mask("00/00");


            

        });

    </script>

</body>
</html>
<?php
    }else{
        $_SESSION['msgNotPermission'] = "<div class='alert alert-info' role='alert'>
                                            Você Não Tem Permissão de Acesso!
                             </div>";
        header("Location: index");

    }

?>