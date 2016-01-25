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
$app->get("/fillGridRowTotalCount_infoUsers/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('infoUsersBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    
    
  
    //print_r('--****************get parent--' );  
     $resTotalRowCount = $BLL->fillGridRowTotalCount(array('language_code'=>$_GET['language_code'])); 
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];

    print_r(' user sayımız =' . $resultArray['total'] );

   
   /*
    $resDataMenu = $BLL->fillGrid(array('page'=>$_GET['page'],
                                        'rows'=>$_GET['rows'],
                                        'sort'=>$_GET['sort'],
                                        'order'=>$_GET['order'] ));  
        
      print_r($resDataMenu);    
 
    $menus = array();
    foreach ($resDataMenu as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "profile_public" => $menu["profile_public"],
             "f_check" => $menu["f_check"],
             "s_date" => $menu["s_date"],
             "c_date" => $menu["c_date"],
             "operation_type_id" => $menu["operation_type_id"],
             "operation_name" => $menu["operation_name"],
             "name" => $menu["name"],
             "surname" => $menu["surname"],
             "username" => $menu["username"],
             "password" => $menu["password"],
             "auth_email" => $menu["auth_email"],            
             "gender_id" => $menu["gender_id"],
             "language_id" => $menu["language_id"],
             "active" => $menu["active"],
             "deleted" => $menu["deleted"],
             "user_id" => $menu["user_id"],
             "act_parent_id" => $menu["act_parent_id"],
             "auth_allow_id" => $menu["auth_allow_id"],
            "auth_alow" => $menu["auth_alow"],
            "cons_allow_id" => $menu["cons_allow_id"],
            "cons_allow" => $menu["cons_allow"],
            
          
            
           
        );  
    
    
    }*/ 
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($resultArray));
  
});




$app->run();