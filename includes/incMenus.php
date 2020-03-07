    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">
            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="./"><img src="images/logonovo.png" alt="Logo"></a>
                <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            </div>
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"> 
                        <a href="index"> <i class="menu-icon fa fa-home"></i>Pagina Inicial <br><center> <?php if($_SESSION['unidadeUsuario'] == 1){echo "<center> Agency Exclusive </center>";}else if($_SESSION['unidadeUsuario'] == 4){echo "<center> Agency Concept </center>";}; ?></center></a>
                    </li>

                    <?php

                        
                        $implodePage = "'".implode("','", $_SESSION['allowPages'])."'";
                        //echo $implodePage;

                        $pdo=conectar();
                        $verifMenu=$pdo->prepare("SELECT * FROM pagesPermission WHERE namePage IN ($implodePage) ORDER BY topMenuName ASC");
                        $verifMenu->execute();
                        $linhaMenu=$verifMenu->fetchAll(PDO::FETCH_OBJ);

                        $qtdRetorno = $verifMenu->rowCount();
                        $menuVet = Array($topMenu=Array('Scouter Ligação'), $subMenu=Array('Fichas'=>Array('Fichas', 'Visualizar')));
                        //print_r($menuVet);

                        $verSubMenu = array();

                ?>
                       <!-- <h3 class="menu-title"><?php echo "Menu"; ?></h3>/.menu-title -->
                        <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ti-arrow-circle-right"></i>   <?php echo "Autorized Pages"; ?></a>
                            <ul class="sub-menu children dropdown-menu">
                <?php
                        foreach($linhaMenu as $rowMenu){                          
                    ?>                          


                            

                            <li><i class="ti-angle-double-right"></i><a href="<?php echo $rowMenu->nameArchive; ?>"><?php echo $rowMenu->nameMenu; ?></a></li> 

                                


                    <?php
                        if(in_array($rowMenu->subMenuName, $verSubMenu)){
                            
                        }else{
                            array_push($verSubMenu, $rowMenu->subMenuName);
                        }


                        }
                    ?>
                    </ul>
                            </li>

                    
                   <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-calendar"></i>Fichas</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-calendar"></i><a href="lista_telefonica">Lista Telefonica</a></li>
                            <li><i class="fa fa-calendar"></i><a href="lista_de_recuperacao">Lista Recuperação</a></li>
                            <li><i class="fa fa-calendar"></i><a href="pesquisar_ficha_ligacao">Pesquisar Ficha</a></li>
                            
                        </ul>
                    </li>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-calendar"></i>Agendamentos</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-calendar"></i><a href="listaRecuperacaoAgendamento.php">Lista Telefonica</a></li>
                            <li><i class="fa fa-calendar"></i><a href="pesquisarRecuperacao.php">Pesquisar Recuperação</a></li>
                            <li><i class="fa fa-calendar"></i><a href="confirmacaonew.php">Confirmar Cliente</a></li>
                            <li><i class="fa fa-calendar"></i><a href="acompanhamentonew.php">Acompanhar Cliente</a></li> 
                            <li><i class="fa fa-calendar"></i><a href="reagendar_cliente_ligacao.php">Reagendar Cliente</a></li>
                        </ul>
                    </li>

                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-calendar"></i>Visualizar</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-calendar"></i><a href="visualizar_agendamento_ligacao_new">Agendamentos</a></li>                       
                        </ul>
                    </li>
                    </li>  
                -->




                
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside><!-- /#left-panel -->

    <!-- Left Panel -->