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
$app->get("/pkFillMachineToolGroupPropertyDefinitions_sysMachineToolPropertyDefinition/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkGetConsConfirmationProcessDetails_sysOsbConsultants" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vMachineGrupId = NULL;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
    $vUnitGrupId = NULL;
    if (isset($_GET['unit_grup_id'])) {
         $stripper->offsetSet('unit_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['unit_grup_id']));
    }
    
    
     $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('machine_grup_id')) $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    if($stripper->offsetExists('unit_grup_id')) $vUnitGrupId = $stripper->offsetGet('unit_grup_id')->getFilterValue();
     
    
    
    $resCombobox = $BLL->fillMachineToolGroupPropertyDefinitions(array(
                                    'machine_grup_id' => $vMachineGrupId,
                                    'unit_grup_id' =>$vUnitGrupId,
                                    'language_code' => $vLanguageCode,
                        ));    

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => html_entity_decode($flow["property_name"]),
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class"=>"icon_class", 
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"],
                "machinegroup" => html_entity_decode($flow["machinegroup"]),
                "unitgroup" => html_entity_decode($flow["unitgroup"]),
                "text_eng" => html_entity_decode($flow["property_name_eng"]),
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 
/**
 *  * Okan CIRAN
 * @since 22-04-2016
 */
$app->get("/pkFillMachineGroupPropertyDefinitions_sysMachineToolPropertyDefinition/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillMachineGroupPropertyDefinitions_sysMachineToolPropertyDefinition" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vMachineGrupId = NULL;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
    $vUnitGrupId = NULL;
    if (isset($_GET['unit_grup_id'])) {
         $stripper->offsetSet('unit_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['unit_grup_id']));
    }
    
    
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('machine_grup_id')) $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    if($stripper->offsetExists('unit_grup_id')) $vUnitGrupId = $stripper->offsetGet('unit_grup_id')->getFilterValue();
     
    
    
    $resCombobox = $BLL->fillMachineGroupPropertyDefinitions(array(
                                    'machine_grup_id' => $vMachineGrupId,
                                    'unit_grup_id' =>$vUnitGrupId,
                                    'language_code' => $vLanguageCode,
                        ));    

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => html_entity_decode($flow["property_name"]),
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class"=>"icon_class", 
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"],
                "machine_grup_id" => $flow["machine_grup_id"], "unitgroup" => html_entity_decode($flow["unitgroup"]),  
                "property_name_eng" => html_entity_decode($flow["property_name_eng"]),
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 
 
/**
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkInsert_sysMachineToolPropertyDefinition/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];     
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }           
    $vPropertyName = NULL;
    if (isset($_GET['property_name'])) {
         $stripper->offsetSet('property_name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['property_name']));
    } 
    $vPropertyNameEng = NULL;
    if (isset($_GET['property_name_eng'])) {
         $stripper->offsetSet('property_name_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['property_name_eng']));
    }      
    $vMachineGrupId = NULL;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
     $vUnitGrupId = NULL;
    if (isset($_GET['unit_grup_id'])) {
         $stripper->offsetSet('unit_grup_id',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_JASON_LVL1,
                                                $app,
                                                $_GET['unit_grup_id']));
    }  
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }  
    if ($stripper->offsetExists('property_name')) {
        $vPropertyName = $stripper->offsetGet('property_name')->getFilterValue();
    }
    if ($stripper->offsetExists('property_name_eng')) {
        $vPropertyNameEng = $stripper->offsetGet('property_name_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_grup_id')) {
        $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    }
    if ($stripper->offsetExists('unit_grup_id')) {
        $vUnitGrupId = $stripper->offsetGet('unit_grup_id')->getFilterValue();
    }
   //  print_r('//'.$vMachineGrupId.'//\\'.$vUnitGrupId);
   // $vMachineGrupId = $_GET['machine_grup_id'];
   // $vUnitGrupId = $_GET['unit_grup_id'];
    $resData = $BLL->insert(array(  
            'language_code' => $vLanguageCode, 
            'property_name' => $vPropertyName ,
            'property_name_eng'=> $vPropertyNameEng, 
            'machine_grup_id' => $vMachineGrupId , 
            'unit_grup_id' => $vUnitGrupId ,
            'pk' => $Pk,        
            ));


    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
 
/**
 *  * Okan CIRAN
 * @since 15-02-2016
 */
$app->get("/pkUpdate_sysMachineToolPropertyDefinition/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];     
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }           
    $vPropertyName = NULL;
    if (isset($_GET['property_name'])) {
         $stripper->offsetSet('property_name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['property_name']));
    } 
    $vPropertyNameEng = NULL;
    if (isset($_GET['property_name_eng'])) {
         $stripper->offsetSet('property_name_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['property_name_eng']));
    }      
    $vMachineGrupId = 0;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
     $vUnitGrupId = NULL;
    if (isset($_GET['unit_grup_id'])) {
         $stripper->offsetSet('unit_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['unit_grup_id']));
    }  
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }  
    if ($stripper->offsetExists('property_name')) {
        $vPropertyName = $stripper->offsetGet('property_name')->getFilterValue();
    }
    if ($stripper->offsetExists('property_name_eng')) {
        $vPropertyNameEng = $stripper->offsetGet('property_name_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_grup_id')) {
        $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    }
    if ($stripper->offsetExists('unit_grup_id')) {
        $vUnitGrupId = $stripper->offsetGet('unit_grup_id')->getFilterValue();
    }
    
    $resData = $BLL->update(array(  
            'language_code' => $vLanguageCode, 
            'property_name' => $vPropertyName ,
            'property_name_eng'=> $vPropertyNameEng, 
            'machine_grup_id' => $vMachineGrupId , 
            'unit_grup_id' => $vUnitGrupId ,
            'pk' => $Pk,        
            ));


    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
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
            "tool_group_name" => html_entity_decode($flow["tool_group_name"]),
            "tool_group_name_eng" => html_entity_decode($flow["tool_group_name_eng"]),
            "property_name" => html_entity_decode($flow["property_name"]),
            "property_name_eng" => html_entity_decode($flow["property_name_eng"]),
            "unit_grup_id" => $flow["unit_grup_id"],
            "unit_group_name" => html_entity_decode($flow["unit_group_name"]),
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

 /**x
 *  * Okan CIRAN
 * @since 13-04-2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysMachineToolPropertyDefinition/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];      
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
            'pk' => $Pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 

/**x
 *  * Okan CIRAN
 * @since 13-04-2016
 */
$app->get("/pkDelete_sysMachineToolPropertyDefinition/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];  
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {$vId = $stripper->offsetGet('id')->getFilterValue(); }  
    $resDataDeleted = $BLL->Delete(array(                  
            'id' => $vId ,    
            'pk' => $Pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataDeleted));
}
); 



 
/**
 *  * Okan CIRAN
 * @since 02-05-2016
 */
$app->get("/pkDeletePropertyMachineGroup_sysMachineToolPropertyDefinition/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];     
   
    $vMachineGrupId = -1;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
     $vPropertyId = NULL;
    if (isset($_GET['property_id'])) {
         $stripper->offsetSet('property_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['property_id']));
    }      
    $stripper->strip(); 
    if ($stripper->offsetExists('machine_grup_id')) {
        $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    }
    if ($stripper->offsetExists('property_id')) {
        $vPropertyId = $stripper->offsetGet('property_id')->getFilterValue();
    }
    
    $resData = $BLL->deletePropertyMachineGroup(array(      
            'machine_grup_id' => $vMachineGrupId , 
            'property_id' => $vPropertyId ,
            'pk' => $Pk,        
            ));


    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
 
/**
 *  * Okan CIRAN
 * @since 20-06-2016
 */
$app->get("/pkFillMachineGroupProperties_sysMachineToolPropertyDefinition/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysMachineToolPropertyDefinitionBLL');
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillMachineGroupProperties_sysMachineToolPropertyDefinition" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vMachineGrupId = NULL;
    if (isset($_GET['machine_grup_id'])) {
         $stripper->offsetSet('machine_grup_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_grup_id']));
    } 
    $vMachineId = NULL;
    if (isset($_GET['machine_id'])) {
         $stripper->offsetSet('machine_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_id']));
    } 
    
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('machine_grup_id')) $vMachineGrupId = $stripper->offsetGet('machine_grup_id')->getFilterValue();
    if($stripper->offsetExists('machine_id')) $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
      
    
    $resCombobox = $BLL->fillMachineGroupProperties(array(
                                    'machine_grup_id' => $vMachineGrupId,
                                    'machine_id' =>$vMachineId,
                                    'language_code' => $vLanguageCode,
                        ));    

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => intval($flow["id"]),
            //"text" => strtolower($flow["name"]),
            "text" => html_entity_decode($flow["property_name"]),
            "state" => $flow["state_type"], //   'closed',
            "checked" => $flow["checked"],
            //"icon_class"=>"icon_class", 
            "attributes" => array(  "active" => $flow["active"],
                "machine_grup_id" => $flow["machine_grup_id"], //"machinegroup" => $flow["machinegroup"],  
                "property_name_eng" => html_entity_decode($flow["property_name_eng"]),
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 


$app->run();
