<?php
// test commit for branch slim2
require 'vendor/autoload.php';




/*$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    ));*/

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
 * @since 10-02-2016
 */
$app->get("/fillConsultantOperations_sysOperationTypes/", function () use ($app ) {
 
    $BLL = $app->getBLLManager()->get('sysOperationTypesBLL');  
    
    
  
    if (isset($_GET['main_group'])) {
    $resCombobox = $BLL->fillConsultantOperations (array('language_code'=>$_GET['language_code'],
                                                          'main_group'=>$_GET['main_group'])  ); 
    } else {
        $resCombobox = $BLL->fillConsultantOperations (array('language_code'=>$_GET['language_code']) 
                                                          ); 
    }
 
    $menus = array();
    foreach ($resCombobox as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "name" => $menu["name"],
       
        );
    }
 
    $app->response()->header("Content-Type", "application/json");
    
  
    
  $app->response()->body(json_encode($menus));
  
});




$app->run();