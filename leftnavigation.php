<?php
// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

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

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());



    







/**
 *  * zeynel daÄŸlÄ±
 * @since 11-09-2014
 */
$app->get("/pkGetLeftMenu_leftnavigation/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public']  ;     
    $resDataMenu = $BLL->pkGetLeftMenu(array('parent' => $_GET['parent'],
                                           'language_code' => $_GET['language_code'], 
                                           'pk' => $pk ,
                                           ) );
    //print_r($resDataMenu);
   
     
        
        
 
    $menus = array();
    foreach ($resDataMenu as $menu){
        $menus[]  = array(
            "id" => $menu["id"],
            "menu_name" => $menu["menu_name"],
             "language_id" => $menu["language_id"],
             "menu_name_eng" => $menu["menu_name_eng"],
             "url" => $menu["url"],
             "parent" => $menu["parent"],
             "icon_class" => $menu["icon_class"],
             "page_state" => $menu["page_state"],
             "collapse" => $menu["collapse"],
             "active" => $menu["active"],
              "deleted" => $menu["deleted"],
             "state" => $menu["state"],
             "warning" => $menu["warning"],
             "warning_type" => $menu["warning_type"],
             "hint" => $menu["hint"],
             "z_index" => $menu["z_index"],
             "language_parent_id" => $menu["language_parent_id"],
             "hint_eng" => $menu["hint_eng"],
             "warning_class" => $menu["warning_class"],
             "acl_type" => $menu["acl_type"],
             "language_code" => $menu["language_code"],
             "active_control" => $menu["active_control"],
            
            
            
            
             
            
           
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($menus));
  
});

  
 
/**
 *  * Okan CIRAN
 * @since 28-03-2016
 */
$app->get("/pkFillGridForAdmin_leftnavigation/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL');
 
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkGetConsConfirmationProcessDetails_sysOsbConsultants" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vRoleId = 1;
    if (isset($_GET['role_id'])) {
        $stripper->offsetSet('role_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['role_id']));
    }
    
    $vPage = 1;
    if (isset($_GET['page'])) {
        $stripper->offsetSet('page', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['page']));
    }
    $vRows = 10;
    if (isset($_GET['rows'])) {
        $stripper->offsetSet('rows', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['rows']));
    }
    
    $vfilterRules = null;
    if(isset($_GET['filterRules'])) {
        $stripper->offsetSet('filterRules', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ALPHABETIC_ALLOWED ,
                                                $app,
                                                $_GET['filterRules']));
    }
    $vSort = null;
    if(isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['sort']));
    }
    $vOrder = null;
    if(isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['order']));
    }
   
  
     $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('role_id')) $vRoleId = $stripper->offsetGet('role_id')->getFilterValue();
    if($stripper->offsetExists('page')) $vPage = $stripper->offsetGet('page')->getFilterValue();
    if($stripper->offsetExists('rows')) $vRows = $stripper->offsetGet('rows')->getFilterValue();
    if($stripper->offsetExists('sort')) $vSort = $stripper->offsetGet('sort')->getFilterValue();
    if($stripper->offsetExists('order')) $vOrder = $stripper->offsetGet('order')->getFilterValue();
    if($stripper->offsetExists('filterRules')) $vfilterRules = $stripper->offsetGet('filterRules')->getFilterValue();
    
 

    $resDataGrid = $BLL->fillGridForAdmin(array('language_code' => $vLanguageCode,
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder, 
        'role_id' => $vRoleId,
        'pk' => $pk,        
        'filterRules' => $vfilterRules));    
 
    $resTotalRowCount = $BLL->fillGridForAdminRtc(array('pk' => $pk,
                                                'language_code' => $vLanguageCode,
                                                'role_id' => $vRoleId,
                                                'filterRules' => $vfilterRules   ));
 
                                            
    $flows = array();
    foreach ($resDataGrid['resultSet'] as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "menu_name" => $flow["menu_name"],
            "menu_name_eng" => $flow["menu_name_eng"],
            "url" => $flow["url"],
            "parent" => $flow["parent"],
            "icon_class" => $flow["icon_class"],            
            
            "page_state" => $flow["page_state"], 
            "collapse" => $flow["collapse"], 
            "deleted" => $flow["deleted"], 
            "state_deleted" => $flow["state_deleted"], 
            "active" => $flow["active"], 
            "state_active" => $flow["state_active"], 
            "warning" => $flow["warning"], 
            "warning_type" => $flow["warning_type"], 
            "warning_class" => $flow["warning_class"], 
            "hint" => $flow["hint"], 
            "hint_eng" => $flow["hint_eng"], 
            "z_index" => $flow["z_index"], 
            "language_parent_id" => $flow["language_parent_id"], 
            
            "active_control" => $flow["active_control"], 
            "role_id" => $flow["role_id"], 
            "role_name" => $flow["role_name"],  
            "attributes" => array("notroot" => true, "active" => $flow["active"],"active_control" => $flow["active_control"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = 2; //$resTotalRowCount['resultSet'][0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));

});

 
 
/**
 *  * Okan CIRAN
 * @since 17-03-2016 
 */
$app->get("/pkFillForAdminTree_leftnavigation/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL');
    
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
    $vRoleId = 1;
    if (isset($_GET['role_id'])) {
        $stripper->offsetSet('role_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['role_id']));
    }
    $vParentId = 0;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }
    
    
    
    
    
     $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('role_id')) $vRoleId = $stripper->offsetGet('role_id')->getFilterValue();
    if($stripper->offsetExists('id')) $vParentId = $stripper->offsetGet('id')->getFilterValue();
    
   
   
    $resDataGrid = $BLL->fillForAdminTree(array(
                                            'language_code' => $vLanguageCode,                                          
                                            'role_id' => $vRoleId,
                                            'parent_id' => $vParentId,
                                            'pk' => $pk,
        
                                                    ));
                                                    
                                                  
 
                                                              
    
        $flows = array();
    if (isset($resDataGrid['resultSet'][0]['id'])) {      
        foreach ($resDataGrid['resultSet']  as $flow) {    
            $flows[] = array(
                "id" => $flow["id"],
                "text" =>  $flow["menu_name"],
                "state" => $flow["state_type"],
                "checked" => false,
                "attributes" => array ("notroot"=>true,"text_eng"=>$flow["menu_name_eng"],"active" => $flow["active"], ),               
                
            );
        }        
    }
   
      
    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
  //  $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    
     // $app->response()->body(json_encode($flows));
    if($componentType == 'bootstrap'){
        $app->response()->body(json_encode($flows));
    }else //if($componentType == 'easyui')
        {
        $app->response()->body(json_encode($resultArray));
        }
      //  $app->response()->body(json_encode($resultArray));
        
 
});

 
/**x
 *  * Okan CIRAN
 * @since 25-02-2016
 */
$app->get("/pkDelete_leftnavigation/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL');
 
   
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

/**x
 *  * Okan CIRAN
 * @since 29-03-2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_leftnavigation/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysNavigationLeftBLL');
 
   
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



$app->run();