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
 * @since 17-02-2016
 */
$app->get("/pkFillGrid1_sysMachineTools/", function () use ($app ) {
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }   
    if (isset($_GET['parent_id']) && $_GET['parent_id'] != "") {
        $resCombobox = $BLL->fillMachineToolGroups(array('parent_id' => $_GET ["parent_id"],
                                                         'language_code' =>$vLanguageCode));
    } else {
        $resCombobox = $BLL->fillMachineToolGroups(array('language_code' =>$vLanguageCode));
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
            "picture" => $flow["picture"],
                        
            
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
 * @since 15-06-2016
 */
$app->get("/pkGetMachineTools_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkGetMachineTools_sysMachineTools" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
 
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vMachineGroupsId = NULL;
    if (isset($_GET['machine_groups_id'])) {
         $stripper->offsetSet('machine_groups_id',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1,
                                                $app,
                                                $_GET['machine_groups_id']));
    }  
    $vManufacturerId = NULL;
    if (isset($_GET['manufacturer_id'])) {
         $stripper->offsetSet('manufacturer_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['manufacturer_id']));
    }  
     $vPage = NULL;
    if (isset($_GET['page'])) {
         $stripper->offsetSet('page',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['page']));
    }       
    $vRows = NULL;
    if (isset($_GET['rows'])) {
         $stripper->offsetSet('rows',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['rows']));
    }   
    $vSort = NULL;
    if (isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['sort']));
    }
    $vOrder = NULL;
    if (isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ORDER,
                                                $app,
                                                $_GET['order']));
    }  
    $filterRules = null;
    if (isset($_GET['filterRules'])) {
        $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1,
                                                $app,
                                                $_GET['filterRules']));
    }   
 
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }    
    if ($stripper->offsetExists('machine_groups_id')) {
        $vMachineGroupsId = $stripper->offsetGet('machine_groups_id')->getFilterValue();
    }    
    if ($stripper->offsetExists('manufacturer_id')) {
        $vManufacturerId = $stripper->offsetGet('manufacturer_id')->getFilterValue();
    }    
    if ($stripper->offsetExists('page')) {
        $vPage = $stripper->offsetGet('page')->getFilterValue();
    } 
    if ($stripper->offsetExists('rows')) {
        $vRows = $stripper->offsetGet('rows')->getFilterValue();
    }        
    if ($stripper->offsetExists('sort')) {
        $vSort = $stripper->offsetGet('sort')->getFilterValue();
    }    
    if ($stripper->offsetExists('order')) {
        $vOrder = $stripper->offsetGet('order')->getFilterValue();
    }
    if ($stripper->offsetExists('filterRules')) {
        $filterRules = $stripper->offsetGet('filterRules')->getFilterValue();
    }
 
  //  if(isset($_GET['filterRules'])) $filterRules = $_GET['filterRules'];
    
    $resDataGrid = $BLL->getMachineTools(array(
        'language_code' => $vLanguageCode,
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder, 
        'machine_groups_id' => $vMachineGroupsId,
        'manufacturer_id' => $vManufacturerId,
        'filterRules' => $filterRules,
       
    ));
    $resTotalRowCount = $BLL->getMachineToolsRtc(array(
        'language_code' => $vLanguageCode,
        'machine_groups_id' => $vMachineGroupsId,
        'manufacturer_id' => $vManufacturerId,
        'filterRules' => $filterRules,
        
    ));
 
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "machine_tool_name" => $flow["machine_tool_name"],
            "machine_tool_name_eng" => $flow["machine_tool_name_eng"],
            "group_name" => $flow["group_name"],
            "group_name_eng" => $flow["group_name_eng"],
            "manufacturer_name" => $flow["manufacturer_name"],             
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});


/**
 *  * Okan CIRAN
 * @since 15-06-2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                $app, $_GET['id']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    $resData = $BLL->makeActiveOrPassive(array(
        'id' => $vId,
        'pk' => $Pk,
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resData));
}
);



$app->run();
