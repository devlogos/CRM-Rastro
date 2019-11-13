<?php
$pages = array('sales/list', 'sales/funnel', 'sales/trackback', 'sellers/list');

$pos = array_search(getURL(), $pages) || array_search(getURL(), $pages) == 0 ? true : false;

if (!$pos) {
    session_start();
}
?>
<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rastro</title>
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/bootstrap/css/bootstrap.min.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/fonts/simple-line-icons.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/fonts/material-icons.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/navbar.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/main.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/sales-list-funnel.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/sellers-list.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/trackback.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/datatable/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/jquery.toast.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/datepicker.min.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/datepicker.min.css?build=<?php echo time(); ?>">        
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/dropify/css/dropify.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/css/lightbox.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" />
    </head>
    <body class="<?php echo setBodyClass(); ?>">
        <nav class="navbar navbar-dark nav-main navbar-expand-lg bg-danger">
            <div class="container-fluid nav-1">
                <a class="navbar-brand" href="#">
                    <img class="img-fluid brand" src="<?php echo ASSETS_PATH ?>/img/rastro.png" alt="Rastro">
                </a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-1">
                    <span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span>
                </button>
                <div id="nav-1" class="collapse navbar-collapse">
                    <form class="form-inline mr-2">
                        <div class="form-group">
                            <input class="form-control search" type="text" placeholder="Pesquisar...">
                        </div>
                    </form>
                    <ul class="nav navbar-nav mr-auto">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="<?php echo DOMAIN ?>/sales/list">
                                <i class="icon-doc icon-nav-top"></i>
                                <span class="ml-4">Vendas</span>
                            </a>
                        </li>                        
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="<?php echo DOMAIN ?>/sales/trackback">
                                <i class="icon-location-pin mr-1 icon-nav-top"></i>
                                <span class="ml-4">Rastreamento</span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle nav-link nav1-contact dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">
                                <i class="icon-notebook icon-nav-top"></i>
                                <span class="ml-4">Contatos</span>
                            </a>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" role="presentation" href="#">
                                    <i class="icon-user mr-1 icon-nav-top"></i>
                                    <span>Usuários</span>
                                </a>
                                <a class="dropdown-item" role="presentation" href="<?php echo DOMAIN ?>/sellers/list">
                                    <i class="icon-handbag mr-1 mr-1 icon-nav-top"></i>
                                    <span>Vendedores</span>
                                </a>
                                <a class="dropdown-item" role="presentation" href="#">
                                    <i class="icon-home mr-1 mr-1 icon-nav-top"></i>                                    
                                    <span>Clientes</span>
                                </a>
                            </div>
                        </li>                        
                        <!--
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="#">
                                <i class="icon-chart mr-1 mr-1 icon-nav-top"></i>
                                <span class="ml-4">Estatísticas</span>
                            </a>
                        </li>
                        -->
                    </ul>
                    <ul class="nav navbar-nav nav-user">
                        <li class="dropdown">
                            <a class="dropdown-toggle nav-link dropdown-toggle" name="user" data-toggle="dropdown" aria-expanded="false" href="#">
                                <span name="name-user"><?php echo getFirstName(isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : 'Usuário'); ?></span>
                                <img class="rounded-circle img-fluid image-user" src="<?php echo isset($_SESSION['URL_IMAGE_USER']) || !empty($_SESSION['URL_IMAGE_USER']) ? DOMAIN . '/media/images/users/' . $_SESSION['URL_IMAGE_USER'] : ASSETS_PATH . '/img/user.png'; ?>">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a class="dropdown-item" role="presentation" href="#">
                                    <i class="icon-user-following mr-1 icon-nav-top"></i>
                                    <span>Perfil de usuário</span>
                                </a>
                                <a class="dropdown-item" role="presentation" href="<?php echo DOMAIN ?>/logout">
                                    <i class="icon-login mr-1 mr-1 icon-nav-top"></i>
                                    <span>Sair</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-dark navbar-expand-lg bg-dark subnav">
            <div class="container-fluid nav-2">
                <a class="navbar-brand d-inline d-sm-inline d-md-inline d-lg-none d-xl-none subitem" href="#">
                    <span class="title-subitem">Adicione ou organize suas vendas...</span></a>
                <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-2">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div id="nav-2" class="collapse navbar-collapse subitems">
                    <ul class="nav navbar-nav mr-auto">
                        <validation key="create_sale" type="clear">
                            <?php if (getURL() == 'sales/list'): ?>
                            <li class="nav-item new-sale" role="presentation">
                                <a class="nav-link active" style="padding-left: 0px !important; cursor: pointer;">
                                    <i class="icon-plus icon-nav-top"></i>
                                    <span class="ml-4">Adicionar Venda</span>
                                </a>
                            </li>
                            <?php endif; ?>                            
                        </validation>
                        <validation key="create_seller" type="clear">
                            <?php if (getURL() == 'sellers/list'): ?>
                            <li class="nav-item new-seller" role="presentation">
                                <a class="nav-link active" style="padding-left: 0px !important; cursor: pointer;">
                                    <i class="icon-plus icon-nav-top"></i>
                                    <span class="ml-4">Adicionar Vendedor</span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </validation> 
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="<?php echo DOMAIN ?>/sales/list">
                                <i class="icon-menu icon-nav-top"></i>
                                <span class="ml-4">Lista</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="<?php echo DOMAIN ?>/sales/funnel">
                                <i class="icon-layers icon-nav-top"></i>
                                <span class="ml-4">Funil</span>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav ml-auto">
                        <li class="dropdown">
                            <a class="dropdown-toggle nav-link dropdown-toggle" name="company" data-toggle="dropdown" aria-expanded="false" href="#">
                                <span><?php echo isset($_SESSION['USER_COMPANY_NAME']) ? $_SESSION['USER_COMPANY_NAME'] : 'Empresa' ?></span> 
                            </a>
                            <div class="dropdown-menu company dropdown-menu-right">
                                <div>
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a role="tab" data-toggle="tab" href="#tab-company-1" class="nav-link text-dark">
                                                <i class="icon-settings icon-nav-top"></i>
                                                <span>Configurações rápidas</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="tab-company-1">
                                            <!--
                                            <a href="#">
                                                <i class="icon-user-follow mr-1 mr-1 icon-nav-top"></i>
                                                <span>Adicionar usuário</span>
                                            </a>
                                            -->                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <section class="container-fluid main">
            <?php
            if (isset($viewName)) {
                $path = viewsPath() . $viewName . '.php';
                if (file_exists($path)) {
                    require_once $path;
                }
            }
            ?>
        </section>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery-3.3.1.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.form.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.ajax.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/datatable/js/jquery.dataTables.min.js"></script>        
        <script src="<?php echo ASSETS_PATH ?>/js/jquery-ui.min.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.toast.js?build=<?php echo time(); ?>"></script>        
        <script src="<?php echo ASSETS_PATH ?>/js/datepicker.min.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/dropify/js/dropify.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/js/lightbox.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/main.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/moment.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/permissions.js?build=<?php echo time(); ?>"></script>
        <?php if (getURL() == 'sales/list' || getURL() == 'sales/funnel'): ?>
        <script src="<?php echo ASSETS_PATH ?>/js/sale-data.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/sales-list-funnel.js?build=<?php echo time(); ?>"></script>        
        <?php endif; ?>
        <?php if (getURL() == 'sales/trackback'): ?>
        <script src="<?php echo ASSETS_PATH ?>/js/trackback.js?build=<?php echo time(); ?>"></script>
        <?php endif; ?>
        <?php if (getURL() == 'sellers/list'): ?>
        <script src="<?php echo ASSETS_PATH ?>/js/seller-data.js?build=<?php echo time(); ?>"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/sellers-list.js?build=<?php echo time(); ?>"></script>
        <?php endif; ?>
        
        <script>
        <?php if (getURL() == 'sales/list'): ?>
        readSalesList();

        intervalSales = setInterval(function() {
                    readSalesList();
                   }, 300000);
                   
        $('.new-sale').on('click', function (e) {
            $('#scheduled-sale').attr('action', 'create/' + companyId);

            $('#new-sale').modal();
        });

        postContent(".result-new-sale", ".scheduled-sale", true);
        <?php endif; ?>

        <?php if (getURL() == 'sales/funnel'): ?>
        readSalesFunnel();
        <?php endif; ?>

        <?php if (getURL() != 'sales/list' && getURL() != 'sales/funnel'): ?>
        $('.new-sale').hide();
        <?php endif; ?>
            
        <?php if (getURL() == 'sales/trackback'): ?>
        //initMapTrackBack('mapTrackback', null, null, 0, 0);
        <?php endif; ?>
            
        <?php if (getURL() == 'sellers/list'): ?>
        readSellersList();
        
        $('.new-seller').on('click', function (e) {
            $('#new-seller').modal();
            
            $('#url_image').dropify({
                messages: {
                        'default': 'Arraste e solte sua imagem aqui',
                        'replace': 'Arraste e solte sua imagem para substituir',
                        'remove':  'Remover',
                        'error':   'Algo errado aconteceu!'
                    },
                error: {
                        'fileSize': 'Tamanho superior a 5 megabytes!',
                        'fileExtension': 'Somente imagem em formato png ou jpg!'
                    }
                });
            
            clearFields();
        });
        
        postContent(".result-notification", ".form-notification", true);
        
        postContent(".result-new-seller", ".form-new-seller", true);
        <?php endif; ?>
            
        $(document).on('click', '.dropdown-menu.company', function (e) {
            e.stopPropagation();
        });
        
        $(document).ready(function() {
            $('.select2').select2();
            
            $('[data-toggle="popover"]').popover();
        });
        
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        checkPermission();
        </script>
    </body>
</html>