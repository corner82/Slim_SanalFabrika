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


$pdo = new PDO('pgsql:dbname=ecoman_01_10;host=88.249.18.205;user=postgres;password=1q2w3e4r');

\Slim\Route::setDefaultConditions(array(
    'firstName' => '[a-zA-Z]{3,}',
    'page' => '[0-9]{1,}'
));

 


/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/fillGridRowTotalCount_sysAclRoles/", function () use ($app, $pdo) {
 
    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');
    
      $resTotalRowCount = $BLL->fillGridRowTotalCount(); 
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];

    print_r(' user sayımız =' . $resultArray['total'] );


    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});
/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/pkFillComboBoxMainRoles_sysAclRoles/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysAclRolesBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);
 
  
    //print_r('--****************get parent--' );  
    $resCombobox = $BLL->fillComboBoxMainRoles();
    //print_r($resDataMenu);
       
        
   $flows = array();
        foreach ($resCombobox as $flow){
            $flows[]  = array(
                "id" => $flow["id"],
                //"text" => strtolower($flow["name"]),
                "text" => $flow["name"],
                "state" => 'open',
                "checked" => false,
                "attributes" => array ("notroot"=>true),
            );
        }
  
    
    $app->response()->header("Content-Type", "application/json");
   
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($flows));
  
});
/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/pkFillComboBoxFullRoles_sysAclRoles/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysAclRolesBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);
 
  
    //print_r('--****************get parent--' );  
    $resCombobox = $BLL->fillComboBoxFullRoles( );
    //print_r($resDataMenu);
   
       
     $flows = array();
        foreach ($resCombobox as $flow){
            $flows[]  = array(
                "id" => $flow["id"],
                //"text" => strtolower($flow["name"]),
                "text" => $flow["name"],
                "state" => 'closed',
                "checked" => false,
                "attributes" => array ("notroot"=>true),
            );
        }   
 
   
    
    $app->response()->header("Content-Type", "application/json");
    
  
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
  $app->response()->body(json_encode($flows));
  
});


/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/pkFillGrid_sysAclRoles/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysAclRolesBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];   
    //print_r($resDataMenu);
    
   
    $resDataGrid = $BLL->fillGrid(array('page'=>$_GET['page'],
                                        'rows'=>$_GET['rows'],
                                        'sort'=>$_GET['sort'],
                                        'order'=>$_GET['order'],
                                        'pk' => $pk  ));
    //print_r($resDataGrid);
    
    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */ 
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            
             "state_deleted" => $flow["state_deleted"],
            "deleted" => $flow["deleted"],
            
             "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            
             "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            
                  "username" => $flow["username"],
            "Role_Parent" => $flow["Role_Parent"], 
            
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
    $app->response()->body(json_encode($resultArray));
  
});

 
/**
 *  * Okan CIRAN
 * @since 07-01-2016
 */
$app->get("/pkInsert_sysAclRoles/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysAclRolesBLL'); 
 
    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];   
    //print_r($resDataMenu);
    
   
    $resDataGrid = $BLL->insert(array('page'=>$_GET['page'],
                                        'rows'=>$_GET['rows'],
                                        'sort'=>$_GET['sort'],
                                        'order'=>$_GET['order'],
                                        'pk' => $pk  ));
    //print_r($resDataGrid);
    
    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */ 
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow){
        $flows[]  = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            
             "state_deleted" => $flow["state_deleted"],
            "deleted" => $flow["deleted"],
            
             "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            
             "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            
                  "username" => $flow["username"],
            "Role_Parent" => $flow["Role_Parent"], 
            
        );
    }
    
    $app->response()->header("Content-Type", "application/json");
    
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    
    /*$app->contentType('application/json');
    $app->halt(302, '{"error":"Something went wrong"}');
    $app->stop();*/
    
    $app->response()->body(json_encode($resultArray));
  
});

 


$app->run();
