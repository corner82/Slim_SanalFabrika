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
$app->add(new \Slim\Middleware\MiddlewareMQManager());


 
/**
 *  * Okan CIRAN
 * @since 17.02.2016
 */
$app->get("/pkInsert_sysMachineToolProperties/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertiesBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    
    $vMachineToolId = $_GET['machine_tool_id'];
    $vMachineToolPropertyDefinitionId = $_GET['machine_tool_property_definition_id']; 
    $vUnitId = $_GET['unit_id'];  
    $vPropertyValue = $_GET['property_value']; 
     
    $fLanguageCode = $vLanguageCode;     
    $fMachineToolId = $vMachineToolId;
    $fMachineToolPropertyDefinitionId =$vMachineToolPropertyDefinitionId;
    $fPropertyValue = $vPropertyValue;
    $fUnitId = $vUnitId;  
  
    
    $resDataInsert = $BLL->insert(array(   
            'language_code' => $fLanguageCode,
            'machine_tool_id' => $fMachineToolId ,         
            'machine_tool_property_definition_id' => $fMachineToolPropertyDefinitionId ,         
            'property_value' => $fPropertyValue ,
            'unit_id' => $fUnitId , 
               
            'pk' => $vPk,        
            ));

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataInsert));
}
); 

/**
 *  * Okan CIRAN
 * @since 17.02.2016
 */
$app->get("/pkUpdate_sysMachineToolProperties/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertiesBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    
    $vActive =0; 
    if (isset($_GET['active'])) {
        $vActive = $_GET['active'];
    }    
    $vID =$_GET['id'];  
    $vMachineToolId = $_GET['machine_tool_id'];
    $vMachineToolPropertyDefinitionId = $_GET['machine_tool_property_definition_id']; 
    $vUnitId = $_GET['unit_id'];  
    $vPropertyValue = $_GET['property_value']; 
     
    $fLanguageCode = $vLanguageCode;     
    $fMachineToolId = $vMachineToolId;
    $fMachineToolPropertyDefinitionId =$vMachineToolPropertyDefinitionId;
    $fPropertyValue = $vPropertyValue;
    $fUnitId = $vUnitId;  
    $fID=$vID ; 
  
    
    $resDataUpdate = $BLL->update(array(  
            'id' => $fID,
            'language_code' => $fLanguageCode,
            'machine_tool_id' => $fMachineToolId ,         
            'machine_tool_property_definition_id' => $fMachineToolPropertyDefinitionId ,         
            'property_value' => $fPropertyValue ,
            'unit_id' => $fUnitId , 
            'active' => $fActive,   
            'pk' => $vPk,        
            ));
    
     

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
}
); 


/**
 *  * Okan CIRAN
 * @since 17-02-2016
 */
$app->get("/pkFillGrid_sysMachineToolProperties/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertiesBLL');
   // $headerParams = $app->request()->headers();
   // $vPk = $headerParams['X-Public'];   
      $vLanguageCode  = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $fLanguageCode=$vLanguageCode;
    
    $resDataGrid = $BLL->fillGrid(array(              
            'language_code' => $fLanguageCode,
            ));
    
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array(              
            'language_code' => $fLanguageCode,
            ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
             "id" => $flow["id"],
            "machine_tool_id" => $flow["machine_tool_id"],
            "machine_tool_names" => $flow["machine_tool_names"],
            "machine_tool_name_eng" => $flow["machine_tool_name_eng"],
            "machine_tool_property_definition_id" => $flow["machine_tool_property_definition_id"],
            "property_names" => $flow["property_names"],
            "property_name_eng" => $flow["property_name_eng"],
            "property_value" => $flow["property_value"],          
            "unit_id" => $flow["unit_id"],
            "unitcodes" => $flow["unitcodes"],
            "deleted" => $flow["deleted"],      
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],       
            "state_active" => $flow["state_active"],              
            "language_code" => $flow["language_code"],                             
            "language_id" => $flow["language_id"],      
	    "language_name" => $flow["language_name"],
            "language_parent_id" => $flow["language_parent_id"],                
            "op_user_id" => $flow["op_user_id"],  
            "op_user_name" => $flow["op_user_name"],            
            
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

 

/**
 *  * Okan CIRAN
 * @since 26-02-2016
 */
$app->get("/pkFillMachineToolFullProperties_sysMachineToolProperties/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertiesBLL');
    
    $headerParams = $app->request()->headers();
    
    $componentType = 'bootstrap'; // 'easyui'
    
    
    
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillMachineToolFullProperties_sysMachineToolProperties" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vParentId = 0;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }    
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('id')) {
        $vParentId = $stripper->offsetGet('id')->getFilterValue();
    }

   
    $resDataGrid = $BLL->fillMachineToolFullProperties(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'id' => $vParentId,
                                                                ));
                                                               
    $resTotalRowCount = $BLL->fillMachineToolFullPropertiesRtc(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'id' => $vParentId,
                                                                ));
  
        $flows = array();
    if (isset($resDataGrid['resultSet'][0]['id'])) {      
        foreach ($resDataGrid['resultSet']  as $flow) {    
            $flows[] = array(
                "id" => $flow["id"],
                "text" =>  $flow["property_names"],
                "state" => $flow["state_type"],
                "checked" => false,
                "attributes" => array ("notroot"=>true),               
                
            );
        }
        
    }
   
    
    
    
               
              
              
             
    
    
    
    
    
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    
     // $app->response()->body(json_encode($flows));
    if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($flows));
    }else if($componentType == 'easyui'){
        $app->response()->body(json_encode($resultArray));
    }
      //  $app->response()->body(json_encode($resultArray));
        
 
});


$app->run();
