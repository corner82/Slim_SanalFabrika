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
 *  *  
  *  * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/fillComboBox_syscountrys/", function () use ($app ) {
    
    $BLL = $app->getBLLManager()->get('sysCountrysBLL'); 
  
    $componentType =$_GET['component_type'] ;
    $resCombobox = $BLL->fillComboBox(array('language_code' => $_GET['language_code']));

    
    if ($componentType == 'bootstrap') {
        $menus = array();
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "name" => $menu["name"],
                "name_eng" =>$menu["name_eng"],
            );
        }
    } else if ($componentType == 'ddslick') {
        $menus = array();
        $menus[] = array("text" => "Lütfen Bir Ülke Seçiniz", "value" => -1, "selected" => true,);
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "text" => $menu["name"],
                "value" => $menu["id"],
                "selected" => false,
                "description" => $menu["name_eng"],
                "imageSrc" => ""
            );
        }
    }
 

    $app->response()->header("Content-Type", "application/json");
   /*
   if($tableType == 'bootstrap'){
        $app->response()->body(json_encode($flows));
    }else if($tableType == 'easyui'){
        $app->response()->body(json_encode($resultArray));
    }
    */
    
    
  //$app->response()->body(json_encode($menus));

    
    $app->response()->header("Content-Type", "application/json");
    
   if($componentType == 'ddslick'){
        $app->response()->body(json_encode($menus));
    }else if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($resCombobox));
    }
   
  
});




$app->run();