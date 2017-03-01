<?php
// test commit for branch slim2
require 'vendor/autoload.php';

use \Services\Filter\Helper\FilterFactoryNames as stripChainers;


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

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC()); 
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());




   


/**
 *  * Okan CIRAN
 * @since 10-02-2016
 */
$app->get("/pkFillConsultantOperationsDropDown_sysOperationTypes/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
     $BLL = $app->getBLLManager()->get('sysOperationTypesBLL');  
    $headerParams = $app->request()->headers();
     if (!isset($headerParams['X-Public']))
         throw new Exception('rest api "pkGetConsPendingUser_sysOsbConsultants" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
     
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $vMainGroup = 0;
    if (isset($_GET['main_group'])) {
        $stripper->offsetSet('main_group', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['main_group']));
    }
    $stripper->strip();   
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('main_group')) {
        $vMainGroup = $stripper->offsetGet('main_group')->getFilterValue();
    } 
    $resCombobox = $BLL->fillConsultantOperations (array(
            'language_code'=>$vLanguageCode,
            'main_group'=>$vMainGroup,
            'pk' => $pk )  );  
 
    $menus = array();
    $menus[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",); 
    foreach ($resCombobox as $menu){
        $menus[]  =  array(
            "text" => $menu["name"],
            "value" => intval($menu["id"]),
            "selected"=> false,
            "description"=> $menu["name_eng"],
            //"imageSrc"=>""
       
        );
    } 
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($menus));
  
});




$app->run();