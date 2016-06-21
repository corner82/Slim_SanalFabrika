<?php

// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

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

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());

 
/**
 *  * Okan CIRAN
 * @since 21-06-2016
 */
$app->get("/pkFillOsbClusterLists_sysOsbClusters/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysOsbClustersBLL');    
    $headerParams = $app->request()->headers();    
    
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }   
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillOsbClusterLists_sysOsbClusters" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vOsbId = NULL;
    if (isset($_GET['osb_id'])) {
        $stripper->offsetSet('osb_id', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['osb_id']));
    }
 
    $stripper->strip();
    if ($stripper->offsetExists('language_code'))
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if ($stripper->offsetExists('osb_id'))
        $vOsbId = $stripper->offsetGet('osb_id')->getFilterValue();
    
    $resData = $BLL->fillOsbClusterLists(array(
                                                'language_code' => $vLanguageCode,
                                                'pk' => $pk,
                                                'osb_id' => $vOsbId,
                                                        )); 
    
    $menus = array();
    $menus[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",); 
     if ($componentType == 'bootstrap') {
        $menus = array();
        foreach ($resData as $menu) {
            $menus[] = array(
                "id" => $menu["id"],       
                "text" => $menu["cluster"],
                "state" => $menu["state_type"],
                "checked" => false,
                "attributes" => array("notroot" => true, 
                                    "active" => $menu["active"] ,
                                    "cluster_eng"=>$menu["cluster_eng"],
                )                
            );
        }
    } else if ($componentType == 'ddslick') {   
        foreach ($resData as $menu) {
            $menus[] = array(
                "text" => $menu["cluster"],
                "value" =>  intval($menu["id"]),
                "selected" => false,
                "description" => $menu["cluster_eng"],
             //   "imageSrc" => ""
            );
        }
    }
    
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
   // $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $menus;
    
     // $app->response()->body(json_encode($flows));
    if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($menus));
    }else if($componentType == 'ddslick'){
        $app->response()->body(json_encode($resultArray));
    }
      //  $app->response()->body(json_encode($resultArray));
        
 
});
 
 
 
 /**x
 *  * Okan CIRAN
 * @since 21-06-2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysOsbClusters/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysOsbClustersBLL');
    
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdateMakeActiveOrPassive_sysOsbClusters" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {$vId = $stripper->offsetGet('id')->getFilterValue(); }
    $resData = $BLL->makeActiveOrPassive(array(                  
            'id' => $vId ,    
            'pk' => $pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 

/**x
 *  * Okan CIRAN
 * @since 21-06-2016
 */
$app->get("/pkDelete_sysOsbClusters/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysOsbClustersBLL');   
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkDelete_sysOsbClusters" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];   
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) 
        {$vId = $stripper->offsetGet('id')->getFilterValue(); }  
        
    $resDataDeleted = $BLL->Delete(array(                  
            'id' => $vId ,    
            'pk' => $pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataDeleted));
}
); 


$app->run();
