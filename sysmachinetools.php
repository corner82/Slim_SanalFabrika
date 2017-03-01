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
 * kullanılmıyor
 */
$app->get("/pkFillGrid1_sysMachineTools/", function () use ($app ) {
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    if (isset($_GET['parent_id']) && $_GET['parent_id'] != "") {
        $resCombobox = $BLL->fillMachineToolGroups(array('parent_id' => $_GET ["parent_id"],
            'language_code' => $vLanguageCode));
    } else {
        $resCombobox = $BLL->fillMachineToolGroups(array('language_code' => $vLanguageCode));
    }
    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" =>  html_entity_decode($flow["name"]),
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class" => $flow["icon_class"],
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"]),
        );
    }
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($flows));
});



/**
 * Okan CIRAN
 * @since 01-02-2016
 * kullanlmıyor
 */
$app->get("/pkFillGrid_sysMachineTools/", function () use ($app ) {
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    $fPk = $vPk;

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    } 
    $resDataGrid = $BLL->fillGridSingular(array(
        'pk' => $fPk,
        'language_code' => $vLanguageCode
    ));

    $resTotalRowCount = $BLL->fillGridSingularRowTotalCount(array(
        'pk' => $fPk,
        'language_code' => $vLanguageCode
    ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "group_name" =>  html_entity_decode($flow["group_name"]),
            "machine_tool_name" =>  html_entity_decode($flow["machine_tool_name"]),
            "machine_tool_name_eng" =>  html_entity_decode($flow["machine_tool_name_eng"]),
            "machine_tool_grup_id" =>  html_entity_decode($flow["machine_tool_grup_id"]),
            "manufactuer_id" => $flow["manufactuer_id"],
            "model" =>  html_entity_decode($flow["model"]),
            "model_year" => $flow["model_year"],
            "procurement" => $flow["procurement"],
            "qqm" => $flow["qqm"],
            "machine_code" =>  html_entity_decode($flow["machine_code"]),
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "op_user_id" => $flow["op_user_id"],
            "op_user_name" => $flow["op_user_name"],
            "language_id" => $flow["language_id"],
            "language_name" =>  html_entity_decode($flow["language_name"]),
            "language_code" =>  html_entity_decode($flow["language_code"]),
            "picture" => $flow["picture"],
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
 *  rest servislere eklendi
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
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vMachineToolGrupId = NULL;
    if (isset($_GET['machine_tool_grup_id'])) {
        $stripper->offsetSet('machine_tool_grup_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['machine_tool_grup_id']));
    }    
    $vPage = NULL;
    if (isset($_GET['page'])) {
        $stripper->offsetSet('page', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['page']));
    }
    $vRows = NULL;
    if (isset($_GET['rows'])) {
        $stripper->offsetSet('rows', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['rows']));
    }
    $vSort = NULL;
    if (isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['sort']));
    }
    $vOrder = NULL;
    if (isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ORDER, $app, $_GET['order']));
    }
    $filterRules = null;
    if (isset($_GET['filterRules'])) {
        $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, $app, $_GET['filterRules']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    } 
    if ($stripper->offsetExists('machine_tool_grup_id')) {
        $vMachineToolGrupId = $stripper->offsetGet('machine_tool_grup_id')->getFilterValue();
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
        'machine_tool_grup_id' => $vMachineToolGrupId,
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder,        
        'filterRules' => $filterRules,
    ));
    $resTotalRowCount = $BLL->getMachineToolsRtc(array(
        'language_code' => $vLanguageCode,        
        'machine_tool_grup_id' => $vMachineToolGrupId,
        'filterRules' => $filterRules,
    ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "machine_tool_name" =>  html_entity_decode($flow["machine_tool_name"]),
            "machine_tool_name_eng" =>  html_entity_decode($flow["machine_tool_name_eng"]),
            "group_name" =>  html_entity_decode($flow["group_name"]),
            "group_name_eng" =>  html_entity_decode($flow["group_name_eng"]),
            "manufacturer_name" =>  html_entity_decode($flow["manufacturer_name"]),   
            "model" =>  html_entity_decode($flow["model"]),
            "attributes" => array(
                        "notroot" => true, 
                        "active" => $flow["active"],
                        "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
                        "manufactuer_id" => $flow["manufactuer_id"],
                        "model" =>  html_entity_decode($flow["model"]),
                        "model_year" => $flow["model_year"],
                        "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
                        "machine_code" =>  html_entity_decode($flow["machine_code"]),
                        "language_id" => $flow["language_id"],
                        "picture" => $flow["picture"],
 
                ),
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
 * @since 18-08-2016
 *  rest servislere eklendi
 */
$app->get("/pkGetMachineToolsGrid_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkGetMachineToolsGrid_sysMachineTools" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];


    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vMachineGroupsId = NULL;
    if (isset($_GET['machine_groups_id'])) {
        $stripper->offsetSet('machine_groups_id', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, $app, $_GET['machine_groups_id']));
    }
    
    $vPage = NULL;
    if (isset($_GET['page'])) {
        $stripper->offsetSet('page', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['page']));
    }
    $vRows = NULL;
    if (isset($_GET['rows'])) {
        $stripper->offsetSet('rows', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['rows']));
    }
    $vSort = NULL;
    if (isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['sort']));
    }
    $vOrder = NULL;
    if (isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ORDER, $app, $_GET['order']));
    }
    $filterRules = null;
  /*  if (isset($_GET['filterRules'])) {
        $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_FILTER_RULES_JSON_LVL1, $app, $_GET['filterRules']));
     //   $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, $app, $_GET['filterRules']));
        
    }
   * 
   */
   ///////////////////////////////////////////////////////////// 
    $vmachineToolName = NULL;
    $vmachineToolNameEng = NULL;
    $vgroupName = NULL;
    $vmanufacturerName = NULL;
    $vmodel = NULL;
     if (isset($_GET['filterRules'])) {
            $filterRules = trim($_GET['filterRules']);
            $jsonFilter = json_decode($filterRules, true);             
            foreach ($jsonFilter as $std) {
                if ($std['value'] != null) {
                    switch (trim($std['field'])) {
                        case 'machine_tool_name':                            
                            $stripper->offsetSet('machine_tool_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        case 'machine_tool_name_eng':
                            $stripper->offsetSet('machine_tool_name_eng', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        case 'group_name':
                            $stripper->offsetSet('group_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));                            
                            break;
                        case 'manufacturer_name':
                            $stripper->offsetSet('manufacturer_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        case 'model':
                            $stripper->offsetSet('model', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app,$std['value']));
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    
                        
    ////////////////////////////////////////////////////////////
  
    $stripper->strip();
    
    $addfilterRules = NULL;
    if (isset($_GET['filterRules'])) {
            $filterRules = trim($_GET['filterRules']);
            $jsonFilter = json_decode($filterRules, true);             
            $addfilterRules = NULL;
            $filterRules = NULL;
            foreach ($jsonFilter as $std) {
                if ($std['value'] != NULL) {                    
                    switch (trim($std['field'])) {
                        case 'machine_tool_name':                            
                                $vmachineToolName = $stripper->offsetGet('machine_tool_name')->getFilterValue();
                                $addfilterRules = '{"field":"machine_tool_name","op":"contains","value":"'.$vmachineToolName.'"}';
                                $filterRules .= $addfilterRules;
                              //  print_r($filterRules);
                            break;
                        case 'machine_tool_name_eng':
                                $vmachineToolNameEng = $stripper->offsetGet('machine_tool_name_eng')->getFilterValue();
                                if ($filterRules != NULL) {$filterRules .=",";}
                                $addfilterRules = '{"field":"machine_tool_name_eng","op":"contains","value":"'.$vmachineToolNameEng.'"}';
                                $filterRules .= $addfilterRules;
                               // print_r($filterRules);
                            break;
                        case 'group_name':
                                $vgroupName = $stripper->offsetGet('group_name')->getFilterValue();
                            if ($addfilterRules != NULL) {$filterRules .=",";}
                                $addfilterRules = '{"field":"group_name","op":"contains","value":"'.$vgroupName.'"}';
                                $filterRules .= $addfilterRules;
                               // print_r($filterRules);
                            break;
                        case 'manufacturer_name':
                                $vmanufacturerName = $stripper->offsetGet('manufacturer_name')->getFilterValue();
                            if ($addfilterRules != NULL) {$filterRules .=",";}
                                $addfilterRules = '{"field":"manufacturer_name","op":"contains","value":"'.$vmanufacturerName.'"}';
                                $filterRules .= $addfilterRules;
                              //  print_r($filterRules);
                            break;
                        case 'model':
                                $vmodel = $stripper->offsetGet('model')->getFilterValue();
                            if ($addfilterRules != NULL) {$filterRules .=",";}
                                $addfilterRules = '{"field":"model","op":"contains","value":"'.$vmodel.'"}';
                                $filterRules .= $addfilterRules;
                              //  print_r($filterRules);
                            break;
                        default:
                            break;
                    }
                   
                   
                }
            }
        } 
      
    if ($addfilterRules != NULL) { $filterRules = "[".$filterRules."]";}
     
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_groups_id')) {
        $vMachineGroupsId = $stripper->offsetGet('machine_groups_id')->getFilterValue();
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
  
    $resDataGrid = $BLL->getMachineToolsGrid(array(
        'url' => $_GET['url'],
        'language_code' => $vLanguageCode,
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder,
        'machine_groups_id' => $vMachineGroupsId,
        'filterRules' => $filterRules,
    ));
    $resTotalRowCount = $BLL->getMachineToolsGridRtc(array(
        'language_code' => $vLanguageCode,
        'machine_groups_id' => $vMachineGroupsId,        
        'filterRules' => $filterRules,
    ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "machine_tool_name" =>  html_entity_decode($flow["machine_tool_name"]),
            "machine_tool_name_eng" =>  html_entity_decode($flow["machine_tool_name_eng"]),
            "group_name" =>  html_entity_decode($flow["group_name"]),
            "group_name_eng" =>  html_entity_decode($flow["group_name_eng"]),
            "manufactuer_id" => $flow["manufactuer_id"],
            "manufacturer_name" =>  html_entity_decode($flow["manufacturer_name"]),
            "machine_tool_grup_id" => $flow["machine_tool_grup_id"],            
            "model" =>  html_entity_decode($flow["model"]),
            "model_year" => $flow["model_year"],
            "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
            "machine_code" =>  html_entity_decode($flow["machine_code"]),
            "attributes" => array(            
                        "active" => $flow["active"],
                        "language_id" => $flow["language_id"],
                ),
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
 * @since 08-06-2016
*  rest servislere eklendi
 */
$app->get("/pkGetMachineProperities_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkGetMachineProperities_sysMachineTools" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
 
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vMachineId = NULL;
    if (isset($_GET['machine_id'])) {
        $stripper->offsetSet('machine_id', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, $app, $_GET['machine_id']));
    }
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_id')) {
        $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
    } 

    $resDataGrid = $BLL->getMachineProperities(array(
        'language_code' => $vLanguageCode,    
        'machine_id' => $vMachineId,        
    )); 

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "property_name" =>  html_entity_decode($flow["property_name"]),
            "property_name_eng" =>  html_entity_decode($flow["property_name_eng"]),
            "property_value" => $flow["property_value"],
            "property_value" =>  html_entity_decode($flow["property_value"]),
            "unit_id" => $flow["unit_id"],
            "unitcode_eng" =>  html_entity_decode($flow["unitcode_eng"]),
            "unitcode" =>  html_entity_decode($flow["unitcode"]),
            "model_materials_id" => $flow["model_materials_id"],
            "material_name" =>  html_entity_decode($flow["material_name"]),
            "material_name_eng" =>  html_entity_decode($flow["material_name_eng"]),
            "attributes" => array(
                        "notroot" => true, 
                        "active" => $flow["active"],
                ),
        );
    }       
    
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});

/**
 *  * Okan CIRAN
 * @since 18-05-2016
 *  rest servislere eklendi
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdateMakeActiveOrPassive_sysMachineTools" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['id']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    $resData = $BLL->makeActiveOrPassive(array(
        'id' => $vId,
        'pk' => $pk,
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resData));
}
);

/* * x
 *  * Okan CIRAN
 * @since 18-05-2016
 */
$app->get("/pkInsert_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkInsert_sysMachineTools" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                    $app, $_GET['language_code']));
    }
    $vMachineToolGrupId = 0;
    if (isset($_GET['machine_tool_grup_id'])) {
        $stripper->offsetSet('machine_tool_grup_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['machine_tool_grup_id']));
    }
    $vMachineToolName = NULL;
    if (isset($_GET['machine_tool_name'])) {
        $stripper->offsetSet('machine_tool_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_tool_name']));
    }
    $vMachineToolNameEng = NULL;
    if (isset($_GET['machine_tool_name_eng'])) {
        $stripper->offsetSet('machine_tool_name_eng', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_tool_name_eng']));
    }
    $vManufactuerId = NULL;
    if (isset($_GET['manufactuer_id'])) {
        $stripper->offsetSet('manufactuer_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['manufactuer_id']));
    }
    $vModel = NULL;
    if (isset($_GET['model'])) {
        $stripper->offsetSet('model', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['model']));
    }
    $vModelYear = NULL;
    if (isset($_GET['model_year'])) {
        $stripper->offsetSet('model_year', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['model_year']));
    }
    $vMachineCode = NULL;
    if (isset($_GET['machine_code'])) {
        $stripper->offsetSet('machine_code', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_code']));
    }
    $vPicture = NULL;
    if (isset($_GET['picture'])) {
        $stripper->offsetSet('picture', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                    $app, $_GET['picture']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_grup_id')) {
        $vMachineToolGrupId = $stripper->offsetGet('machine_tool_grup_id')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_name')) {
        $vMachineToolName = $stripper->offsetGet('machine_tool_name')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_name_eng')) {
        $vMachineToolNameEng = $stripper->offsetGet('machine_tool_name_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('manufactuer_id')) {
        $vManufactuerId = $stripper->offsetGet('manufactuer_id')->getFilterValue();
    }
    if ($stripper->offsetExists('model')) {
        $vModel = $stripper->offsetGet('model')->getFilterValue();
    }
    if ($stripper->offsetExists('model_year')) {
        $vModelYear = $stripper->offsetGet('model_year')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_code')) {
        $vMachineCode = $stripper->offsetGet('machine_code')->getFilterValue();
    }
    if ($stripper->offsetExists('picture')) {
        $vPicture = $stripper->offsetGet('picture')->getFilterValue();
    }

    $resData = $BLL->insert(array(
        'language_code' => $vLanguageCode,
        'machine_tool_grup_id' => $vMachineToolGrupId,
        'machine_tool_name' => $vMachineToolName,
        'machine_tool_name_eng' => $vMachineToolNameEng,
        'manufactuer_id' => $vManufactuerId,
        'model' => $vModel,
        'model_year' => $vModelYear,
        'machine_code' => $vMachineCode,
        'picture' => $vPicture,
        'pk' => $pk,
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resData));
}
);
/* * x
 *  * Okan CIRAN
 * @since 18-05-2016
 */
$app->get("/pkUpdate_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdate_sysMachineTools" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                    $app, $_GET['language_code']));
    }
    $vId = -1;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['id']));
    }
    $vMachineToolGrupId = 0;
    if (isset($_GET['machine_tool_grup_id'])) {
        $stripper->offsetSet('machine_tool_grup_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['machine_tool_grup_id']));
    }
    $vMachineToolName = NULL;
    if (isset($_GET['machine_tool_name'])) {
        $stripper->offsetSet('machine_tool_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_tool_name']));
    }
    $vMachineToolNameEng = NULL;
    if (isset($_GET['machine_tool_name_eng'])) {
        $stripper->offsetSet('machine_tool_name_eng', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_tool_name_eng']));
    }
    $vManufactuerId = NULL;
    if (isset($_GET['manufactuer_id'])) {
        $stripper->offsetSet('manufactuer_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['manufactuer_id']));
    }
    $vModel = NULL;
    if (isset($_GET['model'])) {
        $stripper->offsetSet('model', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['model']));
    }
    $vModelYear = NULL;
    if (isset($_GET['model_year'])) {
        $stripper->offsetSet('model_year', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['model_year']));
    }
    $vMachineCode = NULL;
    if (isset($_GET['machine_code'])) {
        $stripper->offsetSet('machine_code', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['machine_code']));
    }
  /*  $vPicture = NULL;
    if (isset($_GET['picture'])) {
        $stripper->offsetSet('picture', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                    $app, $_GET['picture']));
    }
*/
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_grup_id')) {
        $vMachineToolGrupId = $stripper->offsetGet('machine_tool_grup_id')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_name')) {
        $vMachineToolName = $stripper->offsetGet('machine_tool_name')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_name_eng')) {
        $vMachineToolNameEng = $stripper->offsetGet('machine_tool_name_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('manufactuer_id')) {
        $vManufactuerId = $stripper->offsetGet('manufactuer_id')->getFilterValue();
    }
    if ($stripper->offsetExists('model')) {
        $vModel = $stripper->offsetGet('model')->getFilterValue();
    }
    if ($stripper->offsetExists('model_year')) {
        $vModelYear = $stripper->offsetGet('model_year')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_code')) {
        $vMachineCode = $stripper->offsetGet('machine_code')->getFilterValue();
    }
   /* if ($stripper->offsetExists('picture')) {
        $vPicture = $stripper->offsetGet('picture')->getFilterValue();
    }
    * 
    */

    $resData = $BLL->update(array(
        'id' => $vId,
        'language_code' => $vLanguageCode,
        'machine_tool_grup_id' => $vMachineToolGrupId,
        'machine_tool_name' => $vMachineToolName,
        'machine_tool_name_eng' => $vMachineToolNameEng,
        'manufactuer_id' => $vManufactuerId,
        'model' => $vModel,
        'model_year' => $vModelYear,
        'machine_code' => $vMachineCode,
      //  'picture' => $vPicture,
        'pk' => $pk,
    ));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resData));
}
);


 /**x
 *  * Okan CIRAN
 * @since 18-05-2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysMachineTools1/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkInsert_sysMachineTools" end point, X-Public variable not found');
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
 * @since 18-05-2016
 */
$app->get("/pkDelete_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');   
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkDelete_sysMachineTools" end point, X-Public variable not found');
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


/**
 *  * Okan CIRAN
 * @since 08-06-2016
*  rest servislere eklendi
 */
$app->get("/pkFillMachineAdvSearchSsm_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillMachineAdvSearchSsm_sysMachineTools" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
 
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                $app, $_GET['language_code']));
    }
    $vMachineGroupId = NULL;
    if (isset($_GET['machine_group_id'])) {
        $stripper->offsetSet('machine_group_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['machine_group_id']));
    }
    $vMachineId = NULL;
    if (isset($_GET['machine_tool_id'])) {
        $stripper->offsetSet('machine_tool_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['machine_tool_id']));
    }
    $vCertificateId = NULL;
    if (isset($_GET['certificate_id'])) {
        $stripper->offsetSet('certificate_id', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, 
                $app, $_GET['certificate_id']));
    }
    $vMachineToolName = NULL;
    if (isset($_GET['machine_tool_name'])) {
        $stripper->offsetSet('machine_tool_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['machine_tool_name']));
    }    
    $vMachineToolNameEng = NULL;
    if (isset($_GET['machine_tool_name_eng'])) {
        $stripper->offsetSet('machine_tool_name_eng', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['machine_tool_name_eng']));
    }
    $vTotalPersons = 0;
    if (isset($_GET['totalPersons'])) {
        $stripper->offsetSet('totalPersons', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['totalPersons']));
    } 
    $vPage = NULL;
    if (isset($_GET['page'])) {
        $stripper->offsetSet('page', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['page']));
    }
    $vRows = NULL;
    if (isset($_GET['rows'])) {
        $stripper->offsetSet('rows', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                $app, $_GET['rows']));
    }
    $vSort = NULL;
    if (isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                $app, $_GET['sort']));
    }
    $vOrder = NULL;
    if (isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ORDER, 
                $app, $_GET['order']));
    }
    $filterRules = null;
    if (isset($_GET['filterRules'])) {
        $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1, 
                $app, $_GET['filterRules']));
    }

    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_group_id')) {
        $vMachineGroupId = $stripper->offsetGet('machine_group_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('machine_tool_id')) {
        $vMachineId = $stripper->offsetGet('machine_tool_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('certificate_id')) {
        $vCertificateId= $stripper->offsetGet('certificate_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('machine_tool_name')) {
        $vMachineToolName= $stripper->offsetGet('machine_tool_name')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_tool_name_eng')) {
        $vMachineToolNameEng= $stripper->offsetGet('machine_tool_name_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('totalPersons')) {
        $vTotalPersons= $stripper->offsetGet('totalPerson')->getFilterValue();
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

    
    $resDataGrid = $BLL->fillMachineAdvSearchSsm(array(
        'pk' => $pk,
        'language_code' => $vLanguageCode,  
        'machine_group_id' => $vMachineGroupId,
        'machine_tool_id' => $vMachineId,
        'certificate_id' => $vCertificateId,
        'machine_tool_name' => $vMachineToolName,
        'machine_tool_name_eng' => $vMachineToolNameEng,    
        'totalPersons' => $vTotalPersons, 
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder,        
        'filterRules' => $filterRules,
    )); 
    
    $resTotalRowCount = $BLL->fillMachineAdvSearchSsmRtc(array(
        'pk' => $pk,
        'language_code' => $vLanguageCode,  
        'machine_group_id' => $vMachineGroupId,
        'machine_tool_id' => $vMachineId,
        'certificate_id' => $vCertificateId,
        'machine_tool_name' => $vMachineToolName,
        'machine_tool_name_eng' => $vMachineToolNameEng,  
        'totalPersons' => $vTotalPersons, 
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder,        
        'filterRules' => $filterRules,
    ));
    $counts=0;

    $flows = array();
    if (isset($resDataGrid[0]['id'])) {  
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "firm_id" => $flow["firm_id"],
            "firm_name" =>  html_entity_decode($flow["firm_name"]),
            "firm_name_eng" =>  html_entity_decode($flow["firm_name_eng"]),
            "firm_name_short" =>  html_entity_decode($flow["firm_name_short"]),       
            "firm_name_short_eng" =>  html_entity_decode($flow["firm_name_short_eng"]),
            "machine_tool_id" => $flow["sys_machine_tool_id"],
            
            "machine_tool_name" =>  html_entity_decode($flow["machine_tool_name"]),
            "machine_tool_name_eng" =>  html_entity_decode($flow["machine_tool_name_eng"]),
            "total" => $flow["total"],
            "machine_tool_grup_id" => $flow["machine_tool_grup_id"],
            "machine_tool_grup_name" =>  html_entity_decode($flow["machine_tool_grup_name"]),
            "machine_tool_grup_name_eng" =>  html_entity_decode($flow["machine_tool_grup_name_eng"]),
            
            "availability_id" => $flow["availability_id"],            
            "state_availability" =>  html_entity_decode($flow["state_availability"]),
            "ownership_id" => $flow["ownership_id"],
            "state_ownership" =>  html_entity_decode($flow["state_ownership"]), 
            "picture" =>  html_entity_decode($flow["picture"]),  
            "attributes" => array(
                       
                ),
        );
    }  
    $counts = $resTotalRowCount[0]['count'];
      } ELSE  $flows = array();  
    
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
    
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $counts;
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
    
    
});

 /**x
 *  * Okan CIRAN
 * @since 24-01-2017
 */
$app->get("/getLeftMenuMachineStatistic_sysMachineTools/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolsBLL');    
     
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    
    $stripper->strip(); 
    
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
      $result = $BLL->getLeftMenuMachineStatistic(array(
        'language_code' => $vLanguageCode,
           
        ));                            
                  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "allmachiningequipment" =>  $flow["allmachiningequipment"],
            "allcnc" => $flow["allcnc"],  
            "allmachine" => $flow["allmachine"],               
            "all5axis" => $flow["all5axis"], 
            "attributes" => array(),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
}
); 

$app->run();
