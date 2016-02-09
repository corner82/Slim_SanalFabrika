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


$pdo = new PDO('pgsql:dbname=ecoman_01_10;host=88.249.18.205;user=postgres;password=1q2w3e4r');

\Slim\Route::setDefaultConditions(array(
    'firstName' => '[a-zA-Z]{3,}',
    'page' => '[0-9]{1,}'
));

 
/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/pkGetConsPendingFirmProfile_sysOsbConsultants/", function () use ($app ) {

   

    $BLL = $app->getBLLManager()->get('sysOsbConsultantsBLL');

    $headerParams = $app->request()->headers();
    $sort = null;
    if(isset($_GET['sort'])) $sort = $_GET['sort'];
    
    $order = null;
    if(isset($_GET['order'])) $order = $_GET['order'];
    
    $rows = 10;
    if(isset($_GET['rows'])) $rows = $_GET['rows'];
    
    $page = 1;
    if(isset($_GET['page'])) $page = $_GET['page'];
    
    $filterRules = null;
    if(isset($_GET['filterRules'])) $filterRules = $_GET['filterRules'];
    
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkGetConsPendingFirmProfile_sysOsbConsultants" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $resDataGrid = $BLL->getConsPendingFirmProfile(array('page' => $page,
        'rows' => $rows,
        'sort' => $sort,
        'order' => $order,     
        'pk' => $pk,
        'filterRules' => $filterRules));    
 
    $resTotalRowCount = $BLL->getConsPendingFirmProfilertc(array('pk' => $pk));
    //print_r($resTotalRowCount);
    //print_r($resDataGrid['resultSet']);
    $flows = array();
    foreach ($resDataGrid['resultSet'] as $flow) {
        $flows[] = array(
//            "id" => $flow["id"],
 
  //          "c_date" => $flow["c_date"],
            "company_name" => $flow["company_name"],
            "username" => $flow["username"],
  //          "operation_name" => $flow["operation_name"],
  //          "cep" => $flow["cep"],
  //          "istel" => $flow["istel"],  
            "s_date" => $flow["s_date"],
            "id" => $flow["id"],
            
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount['resultSet'][0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
  
    
    
});
 
 


$app->run();
