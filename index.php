<?php

/**
 * web app and mobile routes
 *
 * @author Giovane Pessoa
 */

// requires
require 'vendor/autoload.php';
require 'init.php';

$app = new \Slim\App([ "settings" => [
        "displayErrorDetails" => true
    ]
]);

$app->get('/helpers/hash/generate/{password}', function ($request, $response, $args) {
    //$header = $response->withHeader('Content-type', 'text/html');

    $password = $request->getAttribute('password');

    echo password_hash($password, PASSWORD_ARGON2I);
});

$app->get('/payload/jwt/auth/{key}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    $key = $request->getAttribute('key');

    // create token
    $authorize = new \App\Authorize();
    echo $authorize->create($key);
});

$app->get('/', function () {
    $LoginController = new \App\Controllers\LoginController();
    
    $LoginController->view();
});

$app->get('/logout', function () {
    $LoginController = new \App\Controllers\LoginController();
    
    $LoginController->logout();
});

$app->post('/web/app/login', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    $allPostPutVars = $request->getParsedBody();

    $email = $allPostPutVars['email'];
    $password = $allPostPutVars['password'];
    $saveDataAccess = isset($allPostPutVars['save_data_access']) ? true : false;
    
    if ($saveDataAccess) {
        setcookie('save_data_access', '1', time() + (86400 * 30), "/");

        setcookie('data_mail', $email, time() + (86400 * 30), "/");
        setcookie('data_password', $password, time() + (86400 * 30), "/");
    } else {
        setcookie('save_data_access', '0', time() + (86400 * 30), "/");

        setcookie('data_mail', '', time() + (86400 * 30), "/");
        setcookie('data_password', '', time() + (86400 * 30), "/");
    }

    $usersController = new \App\Controllers\UsersController();

    $data = $usersController->readUsersAuthentication($email, $password);

    if (empty($data)) {
        echo alert(3, 'Verifique e-mail ou senha!');
    } else {
        $userUrlImage = $data[0];
        $userId = $data[1];
        $userName = $data[2];
        $userEmail = $data[3];
        $userCompanyId = $data[4];
        $userCompanyName = $data[5];
        $secretKey = $data[6];
        $token = $data[7];
        $sectors_id = $data[8];
        $sectors_name = $data[9];
        $status_id = $data[10];

        $sectors = explode(',', $sectors_name);
        $inc = 0;
        $strSectors = null;

        foreach ($sectors as $item) {
            $inc++;

            if ($inc == 1) {
                $strSectors = $strSectors . $item;
            } else if ($inc !== 1 && $inc == count($sectors)) {
                $strSectors = $strSectors . ' e ' . $item;
            } else if ($inc !== 1) {
                $strSectors = $strSectors . ', ' . $item;
            } else {
                $strSectors = $strSectors . $item . ' e ';
            }
        }

        $strSectors = count($sectors) > 1 ? '<span>Setores:</span> ' . $strSectors : '<span>Setor:</span> ' . $strSectors;

        session_start();

        $_SESSION['URL_IMAGE_USER'] = $userUrlImage;
        $_SESSION['USER_ID'] = $userId;
        $_SESSION['USER_NAME'] = $userName;
        $_SESSION['USER_EMAIL'] = $userEmail;
        $_SESSION['USER_COMPANY_ID'] = $userCompanyId;
        $_SESSION['USER_COMPANY_NAME'] = $userCompanyName;
        $_SESSION['USER_LOGIN'] = true;

        storage('DOMAIN', DOMAIN);
        storage('USER_COMPANY_ID', $userCompanyId);
        storage('SECRETKEY', $secretKey);
        storage('TOKEN', $token);
        storage('USER_STATUS_ID', $status_id);
        storage('USER_SECTORS_ID', $sectors_id);
        storage('USER_SECTORS_NAME', $strSectors);
        
        // check all user permissions and store        
        $columns = $usersController->readColumnsUsersPermissions();

        foreach ($columns as $column) {
            $permission = $usersController->readUsersPermissions($userId, $column['COLUMN_NAME']);
            
            $value = $permission[$column['COLUMN_NAME']] ? true : false;
            
            storage($column['COLUMN_NAME'], $value);
        }        

        echo "<script>window.location = '" . DOMAIN . "/dashboard'</script>";
    }
});

$app->get('/mobile/app/login/{user}/{password}/{firebasetoken}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'application/json');

    $sellersController = new \App\Controllers\SellersController();

    $user = $request->getAttribute('user');
    $password = $request->getAttribute('password');
    $firebaseToken = $request->getAttribute('firebasetoken') == 'undefined' ? null : $request->getAttribute('firebasetoken');
    
    $data = $sellersController->readSellersAuthentication($user, $password, $firebaseToken);

    return $header->withJson($data);
});

$app->get('/dashboard', function () {
    $DashboardController = new \App\Controllers\DashboardController();
    $DashboardController->dashboardView();
});

$app->get('/products/list', function () {
    $ProductsController = new \App\Controllers\ProductsController();
    $ProductsController->listView();
});

$app->get('/products/categories/{companyid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $categoryId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $ProductsController = new \App\Controllers\ProductsController();

            $data = $ProductsController->readCategories($companyId, $categoryId);
        }
    }

    return $header->withJson($data);
});

$app->get('/products/{companyid}/{categoryid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $categoryId = $request->getAttribute('categoryid');
    $productId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');
    
    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $ProductsController = new \App\Controllers\ProductsController();

            $data = $ProductsController->readProducts($categoryId, $productId);
        }
    }

    return $header->withJson($data);
});

$app->get('/cities/{companyid}/{stateid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $stateId = $request->getAttribute('stateid') == 0 || empty($request->getAttribute('stateid')) ? null : $request->getAttribute('stateid');

    $data = null;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $CitiesController = new \App\Controllers\CitiesController();

            $data = $CitiesController->read($stateId);
        }
    }

    return $header->withJson($data);
});

$app->get('/districts/cities/{companyid}/{id}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $cityId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $DistrictsController = new \App\Controllers\DistrictsController();

            $data = $DistrictsController->read($cityId);
        }
    }

    return $header->withJson($data);
});

$app->get('/districts/{companyid}/{sellerid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $sellerId = $request->getAttribute('sellerid');

    $data = null;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $DistrictsController = new \App\Controllers\DistrictsController();

            $data = $DistrictsController->readDistrictsForSeller($sellerId);
        }
    }

    return $header->withJson($data);
});

$app->get('/geolocations/{companyid}/{dates}/{type}/{sellerid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $dates = $request->getAttribute('dates') == 0 || empty($request->getAttribute('dates')) ? null : explode(',', $request->getAttribute('dates'));
    
    // types
    // 0 -> all
    // 1 -> there was no attendance
    // 2 -> its finished
    // 3 -> its cancelled
    // 4 -> its scheduled

    $type = $request->getAttribute('type');
    $sellerId = $request->getAttribute('sellerid') == 0 || empty($request->getAttribute('sellerid')) ? null : $request->getAttribute('sellerid');

    $data = null;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $GeolocationsController = new \App\Controllers\GeolocationsController();

            $data = $GeolocationsController->read($companyId, $dates, $type, $sellerId);
        }
    }

    return $header->withJson($data);
});

$app->post('/geolocations/{companyid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');
    
    // main attributes
    $companyId = $request->getAttribute('companyid');  
       
    $data = 0;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $allPostPutVars = $request->getParsedBody();
            
            // get keys
            $creationDate = $allPostPutVars["creation_date"];
            $userAgent = isset($request->getHeaders()['HTTP_USER_AGENT'][0]) ? $request->getHeaders()['HTTP_USER_AGENT'][0] : null;
            $sellerId = $allPostPutVars["seller_id"];
            $saleId = $allPostPutVars["sale_id"];
            $latitude = $allPostPutVars["latitude"];
            $longitude = $allPostPutVars["longitude"];
            
            $GeolocationsController = new \App\Controllers\GeolocationsController();
            
            // define properties
            $GeolocationsController->setCreationDate($creationDate);
            $GeolocationsController->setUserAgent($userAgent);
            $GeolocationsController->setSellerId($sellerId);
            $GeolocationsController->setSaleId($saleId);
            $GeolocationsController->setLatitude($latitude);
            $GeolocationsController->setLongitude($longitude);
            
            $data = $GeolocationsController->create();
        }
    }
    
    return $data;
});

$app->get('/status/{companyid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $statusId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $StatusController = new \App\Controllers\StatusController();

            $data = $StatusController->read($companyId,$statusId);
        }
    }

    return $header->withJson($data);
});

$app->get('/reasons/{companyid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $reasonsId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $ReasonsController = new \App\Controllers\ReasonsController();

            $data = $ReasonsController->read($companyId, $reasonsId);
        }
    }

    return $header->withJson($data);
});

$app->get('/sales/list', function () {
    $SalesController = new \App\Controllers\SalesController;
    $SalesController->listView();
});

$app->get('/sales/funnel', function () {
    $SalesController = new \App\Controllers\SalesController;
    $SalesController->funnelView();
});

$app->get('/sales/trackback', function () {
    $SalesController = new \App\Controllers\SalesController;
    $SalesController->trackbackView();
});

$app->get('/sales/{companyid}/{sellerid}/{id}/{dates}/{status}/{sectors}/{limit}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $sellerId = $request->getAttribute('sellerid') == 0 || empty($request->getAttribute('sellerid')) ? null : $request->getAttribute('sellerid');
    $saleId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');    
    
    // other attributes
    $dates = $request->getAttribute('dates') == 0 || empty($request->getAttribute('dates')) ? null : explode(',', $request->getAttribute('dates'));
    $status = $request->getAttribute('status') == 0 || empty($request->getAttribute('status')) ? null : explode(',', $request->getAttribute('status'));
    $sectors = $request->getAttribute('sectors') == 0 || empty($request->getAttribute('sectors')) ? null : explode(',', $request->getAttribute('sectors'));   
    $limit = $request->getAttribute('limit') == 0 || empty($request->getAttribute('limit')) ? null : explode(',', $request->getAttribute('limit'));

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $SalesController = new \App\Controllers\SalesController;
            
            $data = $SalesController->read($companyId, $sellerId, $saleId, $dates, $status, $sectors, $limit);
        }
    }

    return $header->withJson($data);
});

$app->post('/sales/create/{companyid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    $companyId = $request->getAttribute('companyid');

    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $allPostPutVars = $request->getParsedBody();

            // get keys for sales
            $creationDate = $allPostPutVars["creation_date"];
            $updateDate = $allPostPutVars["update_date"] ? $allPostPutVars["update_date"] : $allPostPutVars["creation_date"];
            $code = $allPostPutVars["code"];
            $audioTimeStamp = $allPostPutVars["audio_time_stamp"] ? $allPostPutVars["audio_time_stamp"] : null;
            $sendAudioFile = $allPostPutVars["send_audio_file"] ? 1 : 0;
            $sellerId = $allPostPutVars["seller_id"];
            $productId = $allPostPutVars["product_id"];
            $districtId = $allPostPutVars["district_id"];
            $note = $allPostPutVars["note"];
            $clientIsHolder = $allPostPutVars["client_is_holder"] ? 1 : 0;
            $reasonId = $allPostPutVars["reason_id"] ? $allPostPutVars["reason_id"] : null;
            $shippingId = $allPostPutVars["shipping_id"];

            // get keys for clients
            
            if (isset($allPostPutVars["client_id"])){
                $clientIdManager = $allPostPutVars["client_id"] ? $allPostPutVars["client_id"] : null;
            }
            else{
                $clientIdManager = null;
            }
            
            $clientName = $allPostPutVars["client_name"];
            $clientEmail = $allPostPutVars["client_email"];
            $clientTelephone = $allPostPutVars["client_telephone"];

            $SalesController = new \App\Controllers\SalesController();
            $ShipmentsController = new \App\Controllers\ShipmentsController;
            $ClientsController = new \App\Controllers\ClientsController();

            // define properties for sales
            $SalesController->setCreationDate($creationDate);
            $SalesController->setUpdateDate($updateDate);
            $SalesController->setCode($code);
            $SalesController->setAudioTimeStamp($audioTimeStamp);
            $SalesController->setSendAudioFile($sendAudioFile);
            $SalesController->setSellerId($sellerId);
            $SalesController->setProductId($productId);
            $SalesController->setDistrictId($districtId);
            $SalesController->setNote($note);
            $SalesController->setClientIsHolder($clientIsHolder);

            $statusId = $ShipmentsController->readStatusId($shippingId);
            $SalesController->setStatusId($statusId);

            $SalesController->setReasonId($reasonId);
            $SalesController->setShippingId($shippingId);

            if (empty($clientIdManager)) {
                // define properties for clients
                $ClientsController->setCreationDate($creationDate);
                $ClientsController->setUpdateDate($updateDate);
                $ClientsController->setCompanyId($companyId);
                $ClientsController->setName($clientName);
                $ClientsController->setEmail($clientEmail);
                $ClientsController->setTelephone($clientTelephone);

                $clientId = $ClientsController->create();
            } else {
                $clientId = $clientIdManager;
            }

            if ($clientId != 0) {
                $SalesController->setClientId($clientId);

                $dataSale = $SalesController->create();

                if ($dataSale == 0) {
                    $ClientsController->delete($clientId);
                    
                    $data = 0;
                }
                else{
                    $data = $dataSale;
                }
            }
            else{
                $data = 0;
            }
        } else {
            $data = -1;
        }
    } else {
        $data = 0;
    }

    echo $data;
});

$app->post('/sales/status/update/{companyid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    $companyId = $request->getAttribute('companyid');

    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $allPostPutVars = $request->getParsedBody();

            // get keys for update sale
            $statusId = $allPostPutVars["statusId"];
            $saleId = $allPostPutVars["saleId"];

            $SalesController = new \App\Controllers\SalesController();

            // define properties for sales
            $SalesController->setStatusId($statusId);
            $SalesController->setSaleId($saleId);

            $data = $SalesController->updateStatus();
        }
    }

    echo $data;
});

$app->post('/sales/sector/update/{companyid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    $companyId = $request->getAttribute('companyid');

    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $allPostPutVars = $request->getParsedBody();

            // get keys for update sale
            $sectorId = $allPostPutVars["sectorId"];
            $saleId = $allPostPutVars["saleId"];

            $SalesController = new \App\Controllers\SalesController();

            // define properties for sales
            $SalesController->setSectorId($sectorId);
            $SalesController->setSaleId($saleId);

            $data = $SalesController->updateSector();
        }
    }

    echo $data;
});

$app->post('/sales/images/convert/{companyid}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    $companyId = $request->getAttribute('companyid');

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $allPostPutVars = $request->getParsedBody();
            
            // get key
            $paths = substr($allPostPutVars["paths"], 1, strlen($allPostPutVars["paths"]) - 2);
            
            $arrayPaths = explode(',', $paths);
            
            $SalesController = new \App\Controllers\SalesController();
            
            $data = $SalesController->convertImages($arrayPaths,$allPostPutVars['timestamp']);
            
            if ($data !== 1){
                echo alert(3, 'Conversão não realizada!');
            }
            else{
                echo alert(1, 'Conversão realizada!');
            }
        }
    }
});

$app->get('/sellers/list', function () {
    $SalesController = new \App\Controllers\SellersController();
    $SalesController->view();
});

$app->get('/sellers/{companyid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $sellerId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $SellerController = new \App\Controllers\SellersController();

            $data = $SellerController->read($companyId, $sellerId);
        }
    }

    return $header->withJson($data);
});

$app->get('/seller/goals/{companyid}/{id}', function ($request, $response, $args){
    $header = $response->withHeader('Content-type', 'application/json');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $sellerId = $request->getAttribute('id') == 0 || empty($request->getAttribute('id')) ? null : $request->getAttribute('id');

    $data = null;
    
    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {        
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            $SellerController = new \App\Controllers\SellersController();

            $data = $SellerController->readSellersGoals($sellerId);
        }
    }

    return $header->withJson($data);
});

$app->post('/seller/create', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');
    
    $allPostPutVars = $request->getParsedBody();
    
    $companyId = $allPostPutVars['companyid'];
        
    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            // get keys for sales
            $creationDate = date('Y-m-d h:m');
            $updateDate = $creationDate;            
            $name = $allPostPutVars['name'];
            $email = $allPostPutVars['email'];
            $telephone = $allPostPutVars['telephone'];
            $user = $allPostPutVars['user'];
            $password = $allPostPutVars['password'];            
            $cityId = $allPostPutVars['city_id'];
            $recordingTime = $allPostPutVars['recording_time'];
            $sampleRate = $allPostPutVars['sample_rate'];
            $bitsPerSample = $allPostPutVars['bits_per_sample'];
            $sendAfterSale = isset($allPostPutVars['send_after_sale']) ? 1 : 0;
                        
            extract($allPostPutVars);
            
            $SellerController = new \App\Controllers\SellersController();
                        
            // define property for image profile
            $SellerController->setImageProfile(null);

            if (isset($_FILES['url_image'])) {
                $fileInfo = new SplFileInfo($_FILES['url_image']['name']);
                
                $fileName = $companyId.date('Ymdhms');
                $extension = $fileInfo->getExtension();                
                $newFileName = $fileName.'.'.$extension;
                
                if (move_uploaded_file($_FILES['url_image']['tmp_name'], 'media/images/sellers/' . $newFileName)) {
                    $SellerController->setImageProfile($newFileName);
                }
            }
            
            // define properties for sales
            $SellerController->setCreationDate($creationDate);
            $SellerController->setUpdateDate($updateDate);
            $SellerController->setCompanyId($companyId);
            $SellerController->setName($name);
            $SellerController->setEmail($email);
            $SellerController->setTelephone($telephone);
            $SellerController->setUser($user);
            $SellerController->setPassword($password);
            $SellerController->setCityId($cityId);
            $SellerController->setRecordingTime($recordingTime);
            $SellerController->setSampleRate($sampleRate);
            $SellerController->setBitsPerSample($bitsPerSample);
            $SellerController->setSendAfterSale($sendAfterSale);
                                
            $data = $SellerController->create();
            
            if ($data === -3){
                unlink('media/images/sellers/' . $SellerController->getImageProfile());
                
                echo alert(3, 'Não foi possível inserir novo vendedor!');
            }
            else{
                echo alert(1, 'Vendedor inserido com sucesso!');
                echo '<script>readSellersList();</script>';
            }
        } else {
            echo alert(4, 'Erro ao autenticar!');
        }
    } else {
        echo alert(4, 'Erro ao autenticar!');
    }
});

$app->post('/seller/update/{companyid}/{id}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $sellerId = $request->getAttribute('id');

    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);        
       
        if ($auth) {
            $allPostPutVars = $request->getParsedBody();

            // get keys for seller
            //$name = $allPostPutVars["name"];
            //$email = $allPostPutVars["email"];
            //$telephone = $allPostPutVars["telephone"];
            $password = password_hash($allPostPutVars["password"], PASSWORD_ARGON2I);

            $SellerController = new \App\Controllers\SellersController();

            // define properties for seller
            $SellerController->setSellerId($sellerId);
            //$SellerController->setName($name);
            //$SellerController->setEmail($email);
            //$SellerController->setTelephone($telephone);
            $SellerController->setPassword($password);

            $data = $SellerController->updateSeller();
        } else {
            $data = 0;
        }
    } else {
        $data = 0;
    }

    echo $data;
});

$app->post('/seller/notification', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');
    
    $allPostPutVars = $request->getParsedBody();
    
    $companyId = $allPostPutVars['companyid'];
        
    $data = 0;

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();
        $auth = $authorize->read($request->getHeaders()['HTTP_TOKEN'][0], $request->getHeaders()['HTTP_KEY'][0], $companyId);

        if ($auth) {
            // get keys for sales
            $title = $allPostPutVars['title'];
            $message = $allPostPutVars['message'];
            $firebaseToken = isset($allPostPutVars['firebase_token']) ? $allPostPutVars['firebase_token'] : null;
            
            if (empty($firebaseToken)){
                exit(alert(3, 'Usuário sem autenticação no app!'));
            }
            
            $SellerController = new \App\Controllers\SellersController();
            
            $success = array();
            
            foreach ($firebaseToken as $item){
                $send = $SellerController->createNotification($title, $message, $item);
                
                if ($send){
                    array_push($success, $send);
                }
            }
            
            $countSuccess = count($success);
            
            if ($countSuccess == 0) {
                echo alert(4, 'Algo errado aconteceu!');
            } else {
                if ($countSuccess == 1) {
                    echo alert(1, 'Enviado com sucesso!');
                } else {
                    echo alert(1, sprintf('%s enviados com sucesso!', $countSuccess));
                }
            }
        } else {
            echo alert(4, 'Erro ao autenticar!');
        }
    } else {
        echo alert(4, 'Erro ao autenticar!');
    }
});

$app->post('/files/send/{companyid}/{filetype}/{saleid}', function ($request, $response, $args) {
    //$header = $response->withHeader('Content-type', 'text/html');

    // main attributes
    $companyId = $request->getAttribute('companyid');
    $fileType = $request->getAttribute('filetype');
    $saleId = $request->getAttribute('saleid');

    // checks authorization from the token
    if (isset($request->getHeaders()['HTTP_TOKEN'][0]) && isset($request->getHeaders()['HTTP_KEY'][0])) {
        $authorize = new \App\Authorize();

        $token = $request->getHeaders()['HTTP_TOKEN'][0];

        if (strpos($token, ',')) {
            $token = substr($token, 0, strpos($token, ','));
        }

        $key = $request->getHeaders()['HTTP_KEY'][0];

        if (strpos($key, ',')) {
            $key = substr($key, 0, strpos($key, ','));
        }

        $auth = $authorize->read($token, $key, $companyId);

        if ($auth) {
            try {
                $post = $request->getParsedBody();

                extract($post);

                if (isset($_FILES['file'])) {
                    $fileName = $_FILES['file']['name'];                    
                    
                    if ($fileType === 'audio'){
                        $directory = 'sales';
                        $extension = '.flac';
                    }
                    else if ($fileType === 'images'){
                        $directory = 'documents';
                        $extension = '.jpg';
                    }

                    if (move_uploaded_file($_FILES['file']['tmp_name'], 'media/' . $fileType . "/{$directory}/" . $fileName . $extension)) {                        
                        $SalesController = new \App\Controllers\SalesController; 
                       
                        $result = 0;

                        if ($fileType === 'audio') {
                            $SalesController->setSaleId($saleId);
                            
                            $result = $SalesController->updateAudioFile();
                        } else if ($fileType === 'images') {
                            $SalesController->setSaleId($saleId);
                            $SalesController->setCreationDate(date('Y-m-d H:m:s'));
                            $SalesController->setImageTimeStamp($fileName);
                            
                            $result = $SalesController->createImageFile();
                        }

                        if ($result == 1){
                            $data = 'sent';
                        }
                        else{
                            $data = 'notsent';
                        }                        
                    }
                    else{
                        $data = 'notsent';
                    }
                } else {
                    $data = 'notsent';
                }
            } catch (Exception $ex) {
                $data = 'error';
            }
        } else {
            $data = 'unauthenticated';
        }
    }

    echo $data;
});

$app->post('/mail/send/{args}', function ($request, $response, $args) {
    $header = $response->withHeader('Content-type', 'text/html');
    
    exit('...');

    try {
        $allPostPutVars = $request->getParsedBody();

        $from = $allPostPutVars["from"];
        $to = $allPostPutVars["to"];
        $subject = $allPostPutVars["subject"];
        $message = utf8_decode($allPostPutVars["message"]);
        $headers = 'De: ' . $to;

        if (empty($message)) {
            $message = $message . "Olá Mundo!";
            $message = $message . "";
            $message = $message . "";
            $message = $message . "";
            $message = $message . "";
            $message = $message . "";
            $message = $message . "";
            $message = $message . "";
        }

        $data = mail($to, $subject, $message, $headers);
    } catch (Exception $ex) {
        $data = 0;
    }

    echo $data;
});

$app->run();