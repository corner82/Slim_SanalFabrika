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

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());




/**
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkFillMachineToolGroupPropertyDefinitions_sysMachineToolPropertyDefinition/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');

    $vLanguageCode  = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    
    
    if (isset($_GET['machine_tool_grup_id']) && $_GET['machine_tool_grup_id'] != "") {
        $resCombobox = $BLL->fillMachineToolGroupPropertyDefinitions(array(
                            'machine_tool_grup_id' => $_GET ["machine_tool_grup_id"],
                            'language_code' => $vLanguageCode,
                ));
    } else {
        $resCombobox = $BLL->fillMachineToolGroupPropertyDefinitions(array(                             
                            'language_code' => $vLanguageCode,
                ));
    }

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class"=>"icon_class", 
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
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkInsert_sysMachineToolPropertyDefinition/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    $vPropertyNameEng = '';
    if (isset($_GET['property_name_eng'])) {
        $vPropertyNameEng = strtolower(trim($_GET['property_name_eng']));
    }
      $vAlgorithmicId = 0;
    if (isset($_GET['algorithmic_id'])) {
        $vAlgorithmicId = strtolower(trim($_GET['algorithmic_id']));
    } 
 
    $vMachineToolGrupId = $_GET['machine_tool_grup_id'];
    $vPropertyName = $_GET['property_name']; 
    $vUnitGrupId = $_GET['unit_grup_id'];   
    
    $fLanguageCode = $vLanguageCode;     
    $fMachineToolGrupId = $vMachineToolGrupId;
    $fPropertyName =$vPropertyName;
    $fPropertyNameEng = $vPropertyNameEng;
    $fUnitGrupId = $vUnitGrupId;  
    $fAlgorithmicId=$vAlgorithmicId; 
     
    
    $resDataInsert = $BLL->insert(array(   
            'language_code' => $fLanguageCode,
            'machine_tool_grup_id' => $fMachineToolGrupId ,         
            'property_name' => $fPropertyName ,         
            'property_name_eng' => $fPropertyNameEng ,
            'unit_grup_id' => $fUnitGrupId , 
            'algorithmic_id' =>$fAlgorithmicId,         
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
 * @since 15-02-2016
 */
$app->get("/pkUpdate_sysMachineToolPropertyDefinition/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    $vPropertyNameEng = '';
    if (isset($_GET['property_name_eng'])) {
        $vPropertyNameEng = strtolower(trim($_GET['property_name_eng']));
    }
      $vAlgorithmicId = 0;
    if (isset($_GET['algorithmic_id'])) {
        $vAlgorithmicId = strtolower(trim($_GET['algorithmic_id']));
    } 
    $vActive =0; 
    if (isset($_GET['active'])) {
        $vActive = $_GET['active'];
    }    
    $vID =$_GET['id'];   
    $vMachineToolGrupId = $_GET['machine_tool_grup_id'];
    $vPropertyName = $_GET['property_name']; 
    $vUnitGrupId = $_GET['unit_grup_id'];   
    
    $fLanguageCode = $vLanguageCode;     
    $fMachineToolGrupId = $vMachineToolGrupId;
    $fPropertyName =$vPropertyName;
    $fPropertyNameEng = $vPropertyNameEng;
    $fUnitGrupId = $vUnitGrupId;  
    $fAlgorithmicId=$vAlgorithmicId; 
    $fActive=$vActive;
    $fID=$vID ; 
     
    
    $resDataUpdate = $BLL->update(array(  
            'id' => $fID,
            'language_code' => $fLanguageCode,
            'machine_tool_grup_id' => $fMachineToolGrupId ,         
            'property_name' => $fPropertyName ,         
            'property_name_eng' => $fPropertyNameEng ,
            'unit_grup_id' => $fUnitGrupId , 
            'algorithmic_id' =>$fAlgorithmicId,  
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
 * @since 15-02-2016
 */
$app->get("/pkFillGrid_sysMachineToolPropertyDefinition/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }    
    
    $resDataGrid = $BLL->fillGrid(array(              
            'language_code' => $vLanguageCode,
            ));
    
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array(              
            'language_code' => $vLanguageCode,
            ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
             "id" => $flow["id"],
            "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
            "tool_group_name" => $flow["tool_group_name"],
            "tool_group_name_eng" => $flow["tool_group_name_eng"],
            "property_name" => $flow["property_name"],
            "property_name_eng" => $flow["property_name_eng"],
            "unit_grup_id" => $flow["unit_grup_id"],
            "unit_group_name" => $flow["unit_group_name"],          
            "algorithmic_id" => $flow["algorithmic_id"],
            "state_algorithmic" => $flow["state_algorithmic"],
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

 

$app->run();
