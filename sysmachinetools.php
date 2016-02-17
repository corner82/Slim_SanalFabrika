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



/**
 *  * Okan CIRAN
 * @since 17-02-2016
 */
$app->get("/pkFillGrid1_sysMachineTools/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');


    if (isset($_GET['parent_id']) && $_GET['parent_id'] != "") {
        $resCombobox = $BLL->fillMachineToolGroups(array('parent_id' => $_GET ["parent_id"]));
    } else {
        $resCombobox = $BLL->fillMachineToolGroups();
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
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
 

  
/**
 * Okan CIRAN
 * @since 01-02-2016
 */
$app->get("/pkFillGrid_sysMachineTools/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');

    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    $fPk = $vPk ; 
     
    
    $vLanguageCode  = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }

    
    $resDataGrid = $BLL->fillGridSingular(array(
                                            'pk' => $fPk ,
                                            'language_code' => $vLanguageCode 
                                            ));

    $resTotalRowCount = $BLL->fillGridSingularRowTotalCount(array(
                                                                'pk' => $fPk ,
                                                                'language_code' => $vLanguageCode 
                                                                 ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "group_name" => $flow["group_name"],
            "machine_tool_name" => $flow["machine_tool_name"],
            "machine_tool_name_eng" => $flow["machine_tool_name_eng"],
            "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
            "manufactuer_id" => $flow["manufactuer_id"],
            "model" => $flow["model"],
            "model_year" => $flow["model_year"],          
            "procurement" => $flow["procurement"],
            "qqm" => $flow["qqm"],
            "machine_code" => $flow["machine_code"],      
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],       
            "active" => $flow["active"],              
            "state_active" => $flow["state_active"],                             
            "op_user_id" => $flow["op_user_id"],      
	    "op_user_name" => $flow["op_user_name"],
            "language_id" => $flow["language_id"],                
            "language_name" => $flow["language_name"],  
            "language_code" => $flow["language_code"],  
                        
            
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});


$app->run();
