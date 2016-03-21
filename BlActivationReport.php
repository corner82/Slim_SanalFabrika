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
 * @author Okan CIRAN Ğ
 * @since 05.01.2016
 */
$res = $app->response();
$res->header('Access-Control-Allow-Origin', '*');
$res->header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

//$app->add(new \Slim\Middleware\MiddlewareTest());
$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());



 


/**
 *  * OKAN CIRAN
 * @since 05-02-2016
 */
$app->get("/pkGetConsultantOperation_blActivationReport/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blActivationReportBLL'); 
  
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
  
    $resDataMenu = $BLL->getConsultantOperation(array('pk'=>$vPk));
  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body($resDataMenu);
  
});
 

/**
 *  * OKAN CIRAN
 * @since 05-02-2016
 */
$app->get("/pkGetConsultantFirmCount_blActivationReport/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blActivationReportBLL'); 
  
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
  
    $resDataMenu = $BLL->getConsultantFirmCount(array('pk'=>$vPk));
  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body($resDataMenu);
  
});
 

/**
 *  * OKAN CIRAN
 * @since 05-02-2016
 */
$app->get("/getAllFirmCount_blActivationReport/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blActivationReportBLL'); 
  
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
  
    $resDataMenu = $BLL->getAllFirmCount(array('pk'=>$vPk));
  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body($resDataMenu);
  
});
 


/**
 *  * OKAN CIRAN
 * @since 05-02-2016
 */
$app->get("/pkGetConsultantUpDashBoardCount_blActivationReport/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blActivationReportBLL'); 
  
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
  
    $resDataMenu = $BLL->getConsultantUpDashBoardCount(array('pk'=>$vPk));
  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body($resDataMenu);
  
});
 

/**
 *  * OKAN CIRAN
 * @since 05-02-2016
 */
$app->get("/pkGetConsWaitingForConfirm_blActivationReport/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blActivationReportBLL'); 
  
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
  
    $resDataMenu = $BLL->getConsWaitingForConfirm(array('pk'=>$vPk));
  
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body($resDataMenu);
  
});
 







$app->run();