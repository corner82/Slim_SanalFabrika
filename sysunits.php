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
 * @since 15-02-2016
 */
$app->get("/pkGetUnits_sysUnits/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysUnitsBLL');

    $vLanguageCode  = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    
    
    if (isset($_GET['main']) && $_GET['main'] != "") {
        $resCombobox = $BLL->getUnits(array(
                            'main' => $_GET ["main"],
                            'language_code' => $vLanguageCode,
                ));
    } else {
        $resCombobox = $BLL->getUnits(array(                             
                            'language_code' => $vLanguageCode,
                ));
    }

        $menus = array();
        $menus[] = array("text" => "Lütfen Seçiniz", "value" => -1, "selected" => true,);
        
    if (isset($_GET['main']) && $_GET['main'] != "") {
         if ($componentType == 'bootstrap') {
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "text" => $menu["unitcodes"],
                "state" => 'closed',
                "checked" => false,
                "attributes" => array("notroot" => true, "active" => $menu["active"]),
            );
        }
    } else if ($componentType == 'ddslick') {       
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "text" => $menu["unitcodes"],
                "value" => $menu["id"],
                "selected" => false,
                "description" => $menu["units"],
                "imageSrc" => ""
            );
        }
    }
    }   else { 
    if ($componentType == 'bootstrap') {
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "text" => $menu["systems"],
                "state" => 'closed',
                "checked" => false,
                "attributes" => array("notroot" => true, "active" => $menu["active"]),
            );
        }
    } else if ($componentType == 'ddslick') {       
        foreach ($resCombobox as $menu) {
            $menus[] = array(
                "text" => $menu["systems"],
                "value" => $menu["id"],
                "selected" => false,
                "description" => $menu["system_eng"],
                "imageSrc" => ""
            );
        }
    }
    }

    $app->response()->header("Content-Type", "application/json");

    $app->response()->body(json_encode($menus));
});
 

 
/**
 *  * Okan CIRAN
 * @since 26-02-2016
 */
$app->get("/pkFillUnitsTree_sysUnits/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysUnitsBLL');
    
    $headerParams = $app->request()->headers();
    
    $componentType = 'bootstrap'; // 'easyui'    
    if (isset($_GET['component_type'])) {
        $componentType = $_GET['component_type']; 
    }
    
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

   
    $resDataGrid = $BLL->fillUnitsTree(array(
                                            'language_code' => $vLanguageCode,
                                            'pk' => $pk,
                                            'id' => $vParentId,
                                                    ));
                                                    
                                                  
    $resTotalRowCount = $BLL->fillUnitsTreeRtc(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'id' => $vParentId,
                                                                ));
                                                              
    
        $flows = array();
    if (isset($resDataGrid['resultSet'][0]['id'])) {      
        foreach ($resDataGrid['resultSet']  as $flow) {    
            $flows[] = array(
                "id" => $flow["id"],
                "text" =>  $flow["unitcodes"],
                "state" => $flow["state_type"],
                "checked" => false,
                "attributes" => array ("notroot"=>true,"text_eng"=>$flow["unitcodes_eng"]),               
                
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




/**
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkInsert_sysUnits/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysUnitsBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    $vParentId =0; 
    if (isset($_GET['parent_id'])) {
        $vParentId = $_GET['parent_id'];
    }    
   
    $vMainId = $_GET['main'];
    $vSubId = $_GET['sub'];    
    $vSystem = $_GET['system'];
    $vSystemEng = $_GET['system_eng'];    
    $vUnit = $_GET['unit'];    
    $vUnitEng = $_GET['unit_eng'];        
    $vUnitcode = $_GET['unitcode'];
    $vUnitcodeEng = $_GET['unitcode_eng'];            
    $vAbbreviation = $_GET['abbreviation'];
    $vAbbreviationEng = $_GET['abbreviation_eng'];     
    
    $resDataInsert = $BLL->insert(array(   
            'language_code' => $vLanguageCode,
            'main'=> $vMainId,
            'sub'=> $vSubId,
            'system'=> $vSystem,
            'system_eng'=> $vSystemEng,
            'unit'=> $vUnit,
            'unit_eng'=> $vUnitEng,
            'unitcode'=> $vUnitcode,
            'unitcode_eng'=> $vUnitcodeEng,
            'abbreviation'=> $vAbbreviation,
            'abbreviation_eng'=> $vAbbreviationEng,         
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
$app->get("/pkUpdate_sysUnits/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysUnitsBLL');
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
    
    $vParentId =0; 
    if (isset($_GET['parent_id'])) {
        $vParentId = $_GET['parent_id'];
    }    
    $vMainId = $_GET['main'];
    $vSubId = $_GET['sub'];    
    $vSystem = $_GET['system'];
    $vSystemEng = $_GET['system_eng'];  
    $vUnit = $_GET['unit'];    
    $vUnitEng = $_GET['unit_eng'];       
    $vUnitcode = $_GET['unitcode'];
    $vUnitcodeEng = $_GET['unitcode_eng'];           
    $vAbbreviation = $_GET['abbreviation'];
    $vAbbreviationEng = $_GET['abbreviation_eng'];     
    
    $resDataUpdate = $BLL->update(array(  
            'id' => $vID,
            'language_code' => $vLanguageCode,
            'main'=> $vMainId,
            'sub'=> $vSubId,
            'system'=> $vSystem,
            'system_eng'=> $vSystemEng,
            'unit'=> $vUnit,
            'unit_eng'=> $vUnitEng,
            'unitcode'=> $vUnitcode,
            'unitcode_eng'=> $vUnitcodeEng,
            'abbreviation'=> $vAbbreviation,
            'abbreviation_eng'=> $vAbbreviationEng,       
            
            'active' => $vActive, 
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
$app->get("/pkFillGrid_sysUnits/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysUnitsBLL');
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
            
            "main" => $flow["main"],
            "sub" => $flow["sub"],
            "systems" => $flow["systems"],
            "system_eng" => $flow["system_eng"],
            "abbreviations" => $flow["abbreviations"],
            "abbreviation_eng" => $flow["abbreviation_eng"],
            "unitcodes" => $flow["unitcodes"],          
            "unitcode_eng" => $flow["unitcode_eng"],
            "units" => $flow["units"],          
            "unit_eng" => $flow["unit_eng"], 
            
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
