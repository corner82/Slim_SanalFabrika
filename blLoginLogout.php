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
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());



    







/**
 *  * OKAN CIRAN
 * @since 05-01-2016
 */
$app->get("/pkSessionControl_blLoginLogout/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('blLoginLogoutBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    
 // print_r( $app->request()->headers());
   
    $resDataMenu = $BLL->pkSessionControl(array('pk'=>$_GET['pk']));
   // print_r($resDataMenu);
   
 
    $menus = array();
    foreach ($resDataMenu as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "name" => $menu["name"],
             "data" => $menu["data"],
             "lifetime" => $menu["lifetime"],
             "c_date" => $menu["c_date"],
             "modified" => $menu["modified"],
             "public_key" => $menu["public_key"],
             "u_name" => $menu["u_name"],
             "u_surname" => $menu["u_surname"],
             "username" => $menu["username"],
           
            
           
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($menus));
  
});
/**
 *  * zeynel daÄŸlÄ±
 * @since 11-09-2014
 */
$app->get("/getLeftMenuFull_leftnavigation/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    
    
    
    
     $resDataMenuFull = $BLL->getLeftMenuFull();
  
    //print_r('--****************get parent--'.$_GET['parent']);  
    
    //print_r($resDataMenu);
   
     
        
        
 
    $menusFull = array();
    foreach ($resDataMenuFull as $menuFull){
        $menusFull[]  = array(
            "id" => $menuFull["id"],
            "menu_name" => $menuFull["menu_name"],
             "language_id" => $menuFull["language_id"],
             "menu_name_eng" => $menuFull["menu_name_eng"],
             "url" => $menuFull["url"],
             "parent" => $menuFull["parent"],
             "icon_class" => $menuFull["icon_class"],
             "page_state" => $menuFull["page_state"],
             "collapse" => $menuFull["collapse"],
             "active" => $menuFull["active"],
              "deleted" => $menuFull["deleted"],
             "state" => $menuFull["state"],
             "warning" => $menuFull["warning"],
             "warning_type" => $menuFull["warning_type"],
             "hint" => $menuFull["hint"],
             "z_index" => $menuFull["z_index"],
             "language_parent_id" => $menuFull["language_parent_id"],
             "hint_eng" => $menuFull["hint_eng"],
             "warning_class" => $menuFull["warning_class"],
             "acl_type" => $menuFull["acl_type"],
             
            
           
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($menusFull));
  
});




$app->run();