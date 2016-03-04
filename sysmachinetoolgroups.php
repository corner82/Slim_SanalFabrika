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

//$app->add(new \Slim\Middleware\MiddlewareTest());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());



/**
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkFillMachineToolGroups_sysMachineToolGroups/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    
    $BLL = $app->getBLLManager()->get('sysMachineToolGroupsBLL');
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }
     $vParentId = 0;
    if (isset($_GET['parent_id'])) {
        $stripper->offsetSet('parent_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['parent_id']));
    }
    $vState =NULL;
    if (isset($_GET['state'])) {
        $stripper->offsetSet('state', $stripChainerFactory->get(stripChainers::FILTER_ONLY_STATE_ALLOWED,
                                                $app,
                                                $_GET['state']));
    }
    $vLastNode =NULL;
    if (isset($_GET['last_node'])) {
        $stripper->offsetSet('last_node', 
                    $stripChainerFactory->get(stripChainers::FILTER_ONLY_BOOLEAN_ALLOWED,
                                                $app,
                                                $_GET['last_node']));  
    }
    $vMachine= NULL;
     if (isset($_GET['machine'])) {
        $stripper->offsetSet('machine', 
                $stripChainerFactory->get(stripChainers::FILTER_ONLY_BOOLEAN_ALLOWED,
                        $app,
                        $_GET['machine']));
    }
    
    $vsearch = null;
    if(isset($_GET['search'])) {
        $stripper->offsetSet('search', 
                $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                        $app,
                        $_GET['search']));
    }
    
    
    $stripper->strip();
    if($stripper->offsetExists('machine')) $vMachine = $stripper->offsetGet('machine')->getFilterValue();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('parent_id')) $vParentId = $stripper->offsetGet('parent_id')->getFilterValue();
    if($stripper->offsetExists('state')) $vState = $stripper->offsetGet('state')->getFilterValue();
    if($stripper->offsetExists('last_node')) $vLastNode = $stripper->offsetGet('last_node')->getFilterValue();
    if($stripper->offsetExists('search')) $vsearch = $stripper->offsetGet('search')->getFilterValue();

    if (isset($_GET['parent_id'])) {
        $resCombobox = $BLL->fillMachineToolGroups(array('parent_id' => $vParentId,
                                                         'language_code' => $vLanguageCode, 
                                                         'state' => $vState,
                                                         'last_node' => $vLastNode,
                                                         'machine' => $vMachine,
                                                         'search' => $vsearch,
                                                                ));
    } else {
        $resCombobox = $BLL->fillMachineToolGroups(array('language_code' => $vLanguageCode));
    }

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class"=>$flow["icon_class"], 
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"]
                ,"machine" => $flow["machine"],"last_node" => $flow["last_node"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
/**
 *  * Okan CIRAN
 * @since 29-02-2016
 */

$app->get("/pkFillMachineToolGroupsMachineProperties_sysMachineToolGroups/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    
    $BLL = $app->getBLLManager()->get('sysMachineToolGroupsBLL');
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }
     $vMachineId = 0;
    if (isset($_GET['machine_id'])) {
        $stripper->offsetSet('machine_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_id']));
    }
    
    $stripper->strip();    
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('machine_id')) $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
    
    if (isset($_GET['machine_id'])) {
        $resData = $BLL->fillMachineToolGroupsMachineProperties(array('machine_id' => $vMachineId,
                                                         'language_code' => $vLanguageCode, 
                                                        
                                                                ));
    } else {
        $resData = $BLL->fillMachineToolGroupsMachineProperties(array('language_code' => $vLanguageCode));
    }
     $flows = array();
    if (isset($resData['resultSet'][0]['machine_id'])) {      
        foreach ($resData['resultSet']  as $flow) {
            $flows[] = array(
                "id" => $flow["id"],
                "machine_id" => $flow["machine_id"], 
                "machine_names" => $flow["machine_names"],                
                "property_names" => $flow["property_names"],
                "property_name_eng" => $flow["property_name_eng"],
                "property_value" => $flow["property_value"],
                "unit_id" => $flow["unit_id"],
                "unitcodes" => $flow["unitcodes"],             
                "attributes" => array("notroot" => true ),
            );
        }
        
    }
    $resultArray = array();
    //  $resultArray['total'] = 2;//$resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
    
});


$app->run();
