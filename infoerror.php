<?php

// test commit for branch slim2
require 'vendor/autoload.php';




/* $app = new \Slim\Slim(array(
  'mode' => 'development',
  'debug' => true,
  'log.enabled' => true,
  )); */

$app = new \Slim\SlimExtended(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO,
    'exceptions.rabbitMQ' => true,
    'exceptions.rabbitMQ.logging' => \Slim\SlimExtended::LOG_RABBITMQ_FILE,
    'exceptions.rabbitMQ.queue.name' => \Slim\SlimExtended::EXCEPTIONS_RABBITMQ_QUEUE_NAME
        ));

/**
 * "Cross-origion resource sharing" kontrolüne izin verilmesi için eklenmiştir
 * @author Mustafa Zeynel Dağlı
 * @since 2.10.2015
 */
$res = $app->response();
$res->header('Access-Control-Allow-Origin', '*');
$res->header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

//$app->add(new \Slim\Middleware\MiddlewareTest());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());


/**
 *  * Okan CIRAN
 * @since 11-02-2016
 */
$app->get("/pkFillGrid_infoError/", function () use ($app ) {
 
   
    $BLL = $app->getBLLManager()->get('infoErrorBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];


    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'search_name' => $vSearchName,
        'pk' => $pk));
     
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('search_name' => $vSearchName));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
 

    $app->response()->body(json_encode($resultArray));
});
/**
 *  * Okan CIRAN
 * @since 11-02-2016
 */
$app->get("/pkInsert_infoError/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('InfoErrorBLL');

           
                                
    $vServiceName = $_GET['service_name'];
    
    $vUrl = $_GET['url_full'];
    
    $vErrorCode = $_GET['error_code'];
    $vErrorInfo = $_GET['error_info'];    
    $vPageName = $_GET['page_name'];

    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];


    $resDataInsert = $BLL->insert(array(
        'url' => $vUrl,
        'error_code' => $vErrorCode,
        'error_info' => $vErrorInfo,
        'service_name' => $vServiceName,
        'page_name' => $vPageName,
        'pk' => $vPk));


    $app->response()->header("Content-Type", "application/json");
 
    $app->response()->body(json_encode($resDataInsert));
});
/**
 *  * Okan CIRAN
 * @since 11-02-2016
 */
$app->get("/pkUpdate_infoError/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoErrorBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->update($_GET['id'], array('name' => $_GET['name'],
        'active' => $_GET['active'],
        'user_id' => $_GET['user_id'],
        'id' => $_GET['id'],
        'pk' => $pk));


    $app->response()->header("Content-Type", "application/json");



    $app->response()->body(json_encode($resDataUpdate));
});

 
/**
 *  * Okan CIRAN
 * @since 11-01-2016
 */
$app->get("/pkGetAll_infoError/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoErrorBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataGrid = $BLL->getAll();

    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;


    $app->response()->body(json_encode($resultArray));
});

/**
 *  * Okan CIRAN
 * @since 11-02-2016
 */
$app->get("/pkDelete_infoError/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoErrorBLL');


    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->delete($_GET['id'], array(
        'user_id' => $_GET['user_id'],
        'pk' => $pk));


    $app->response()->header("Content-Type", "application/json");

    $app->response()->body(json_encode($resDataUpdate));
});

$app->run();
