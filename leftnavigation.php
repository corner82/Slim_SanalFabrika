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
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareHMAC());


    







/**
 *  * zeynel daÄŸlÄ±
 * @since 11-09-2014
 */
$app->get("/getLeftMenu_leftnavigation/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    
    
  
    //print_r('--****************get parent--'.$_GET['parent']);  
    $resDataMenu = $BLL->getLeftMenu(array('parent'=>$_GET['parent']));
    //print_r($resDataMenu);
   
     
        
        
 
    $menus = array();
    foreach ($resDataMenu as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "menu_name" => $menu["menu_name"],
             "language_id" => $menu["language_id"],
             "menu_name_eng" => $menu["menu_name_eng"],
             "url" => $menu["url"],
             "parent" => $menu["parent"],
             "icon_class" => $menu["icon_class"],
             "page_state" => $menu["page_state"],
             "collapse" => $menu["collapse"],
             "active" => $menu["active"],
              "deleted" => $menu["deleted"],
             "state" => $menu["state"],
             "warning" => $menu["warning"],
             "warning_type" => $menu["warning_type"],
             "hint" => $menu["hint"],
             "z_index" => $menu["z_index"],
             "language_parent_id" => $menu["language_parent_id"],
             "hint_eng" => $menu["hint_eng"],
             "warning_class" => $menu["warning_class"],
             
            
           
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($menus));
  
});




$app->run();