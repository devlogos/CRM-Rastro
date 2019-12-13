<?php
$attrSaveAccess = '';
$dataMail = '';
$dataPassword = '';

if (isset($_COOKIE['save_data_access']) && isset($_COOKIE['data_mail']) && isset($_COOKIE['data_password'])) {
    $cookieAccess = (bool) $_COOKIE['save_data_access'];

    if ($cookieAccess) {
        $attrSaveAccess = 'checked';

        $dataMail = $_COOKIE['data_mail'];
        $dataPassword = $_COOKIE['data_password'];
    }
}
?>
<!DOCTYPE HTML>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rastro</title>
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/bootstrap-4.3.1/css/bootstrap.min.css?build=<?php echo time(); ?>">        
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/navbar.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/login.css?build=<?php echo time(); ?>">
        <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>/css/main.css?build=<?php echo time(); ?>">
    </head>
    <body>
        <nav class="navbar navbar-dark navbar-expand-lg bg-light">
            <div class="container-fluid nav-1">
                <a class="navbar-brand" href="#">
                    <img class="img-fluid login brand" src="<?php echo ASSETS_PATH ?>/img/logo-main.png" alt="Rastro">
                </a>
                <button class="navbar-toggler login" data-toggle="collapse" data-target="#nav-1">
                    <span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav ml-auto">
                        <li role="presentation" class="nav-item">
                            <a href="#" class="nav-link" name="user">
                                <button class="btn btn-secondary btn-download" type="button">
                                    <i class="fa fa-android"></i>Baixe sua versão Android
                                </button>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid main login">
            <div class="row align-items-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 m-auto">
                    <form class="login" id="login" action="web/app/login" method="post">
                        <h2 class="text-danger mb-4 h2-login">Login</h2>
                        <div class="form-group mb-4">
                            <label for="email">E-mail</label>
                            <input class="form-control form-control-lg input-login" type="email" value="<?php echo $dataMail ?>" name="email" required="" autocomplete="off" inputmode="email" id="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <div class="remember-login">
                                <h6>Esqueceu?</h6>
                            </div>
                            <input class="form-control form-control-lg input-login" type="password" value="<?php echo $dataPassword ?>" name="password" required="" autocomplete="off" id="password">
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input <?php echo $attrSaveAccess ?> class="form-check-input" type="checkbox" name="save_data_access" id="formCheck-1">
                                <label class="form-check-label" for="formCheck-1">Lembrar</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger btn-lg button-login" type="submit">Entrar</button>
                        </div>
                        <div class="form-group">
                            <div id="result"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.min.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/bootstrap-4.3.1/js/bootstrap.min.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.form.js"></script>
        <script src="<?php echo ASSETS_PATH ?>/js/jquery.ajax.js?build=<?php echo time(); ?>"></script>
        <script>
            postContent("#result", "#login");
        </script>
    </body>
</html>