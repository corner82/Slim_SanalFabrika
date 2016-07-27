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
 * @since 27.07.2016
 */
$app->get("/pkInsert_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');  
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkInsert_sysServicesGroups" end point, X-Public variable not found');    
    $pk = $headerParams['X-Public'];
    
    $vName = NULL;
    if (isset($_GET['name'])) {
         $stripper->offsetSet('name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['name']));
    }
    $vDescription = NULL;
    if (isset($_GET['description'])) {
         $stripper->offsetSet('description',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description']));
    }
    $vParent = NULL;
    if (isset($_GET['parent_id'])) {
         $stripper->offsetSet('parent_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['parent_id']));
    }
   
    $stripper->strip();
    if($stripper->offsetExists('name')) $vName = $stripper->offsetGet('name')->getFilterValue();
    if($stripper->offsetExists('description')) $vDescription = $stripper->offsetGet('description')->getFilterValue();
    if($stripper->offsetExists('parent_id')) $vParent = $stripper->offsetGet('parent_id')->getFilterValue();
      
    $resDataInsert = $BLL->insert(array(
            'name' => $vName,       
            'parent_id' => $vParent,          
            'description' => $vDescription,
            'pk' => $pk));
        
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataInsert));
    
}
);
/**
 *  * Okan CIRAN
 * @since 27.07.2016
 */
$app->get("/pkUpdate_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');  
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkUpdate_sysServicesGroups" end point, X-Public variable not found');    
    $pk = $headerParams['X-Public'];
    
    $vId = NULL;
    if (isset($_GET['id'])) {
         $stripper->offsetSet('id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
         $stripper->offsetSet('name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['name']));
    }
    $vDescription = NULL;
    if (isset($_GET['description'])) {
         $stripper->offsetSet('description',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description']));
    }
    $vParent = NULL;
    if (isset($_GET['parent'])) {
         $stripper->offsetSet('parent',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['parent']));
    }
   
    $stripper->strip();
    if($stripper->offsetExists('id')) $vId = $stripper->offsetGet('id')->getFilterValue();
    if($stripper->offsetExists('name')) $vName = $stripper->offsetGet('name')->getFilterValue();
    if($stripper->offsetExists('description')) $vDescription = $stripper->offsetGet('description')->getFilterValue();
    if($stripper->offsetExists('parent')) $vParent = $stripper->offsetGet('parent')->getFilterValue();
      
    $resDataInsert = $BLL->update(array(
            'id' => $vId,  
            'name' => $vName,       
            'parent' => $vParent,          
            'description' => $vDescription,
            'pk' => $pk));
        
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataInsert));
    
}
);
  
/**
 *  * Okan CIRAN
 * @since 27.07.2016
 */
 
$app->get("/pkDelete_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');   
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
 * @since 27.07.2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');
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
 
/**
 *  * Okan CIRAN
 * @since 27.07.2016
 */
$app->get("/pkFillServicesGroupsDdList_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');
    
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillServicesGroupsDdList_sysServicesGroups" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public']; 
    $resCombobox = $BLL->fillServicesGroupsDdList();    
      
    $flows = array();
    $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",); 
    foreach ($resCombobox as $flow) {
        $flows[] = array(            
            "text" => html_entity_decode($flow["name"]),
            "value" =>  intval($flow["id"]),
            "selected" => false,
            "description" => html_entity_decode($flow["description"]),
           // "imageSrc"=>$flow["logo"],             
            "attributes" => array(  
                                "active" => $flow["active"],                                                    
                                   
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 


/**
 *  * Okan CIRAN
 * @since 15-07-2016
 */
$app->get("/pkFillResourceGroups_sysServicesGroups/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysServicesGroupsBLL');    
    $vParentId = 0;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
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
    $vRoles= NULL;
     if (isset($_GET['roles'])) {
        $stripper->offsetSet('roles', 
                $stripChainerFactory->get(stripChainers::FILTER_ONLY_BOOLEAN_ALLOWED,
                        $app,
                        $_GET['roles']));
    }
    
    $vsearch = null;
    if(isset($_GET['search'])) {
        $stripper->offsetSet('search', 
                $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                        $app,
                        $_GET['search']));
    }
     
    $stripper->strip();
    if($stripper->offsetExists('roles')) $vRoles = $stripper->offsetGet('roles')->getFilterValue();    
    if($stripper->offsetExists('id')) $vParentId = $stripper->offsetGet('id')->getFilterValue();
    if($stripper->offsetExists('state')) $vState = $stripper->offsetGet('state')->getFilterValue();
    if($stripper->offsetExists('last_node')) $vLastNode = $stripper->offsetGet('last_node')->getFilterValue();
    if($stripper->offsetExists('search')) $vsearch = $stripper->offsetGet('search')->getFilterValue();
 
    if (isset($_GET['id'])) {
        $resCombobox = $BLL->FillResourceGroups(array('parent_id' => $vParentId,
                                                         'state' => $vState,
                                                         'last_node' => $vLastNode,
                                                         'roles' => $vRoles,
                                                         'search' => $vsearch,
                                                                ));
    } else {
        $resCombobox = $BLL->FillResourceGroups(array('parent_id' => $vParentId,
                                                                ));
    }

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => html_entity_decode($flow["name"]),
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
           // "icon_class"=>$flow["icon_class"], 
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"]
                ,"roles" => html_entity_decode($flow["roles"]),"last_node" => $flow["last_node"]),
        );
    }

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($flows));
});




$app->run();
