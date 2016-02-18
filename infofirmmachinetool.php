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
 * @since 18-02-2016
 */
$app->get("/pkFillSingularFirmMachineTools_infoFirmMachineTool/", function () use ($app ) {

   
 
    $BLL = $app->getBLLManager()->get('infoFirmMachineToolBLL');
 
    $headerParams = $app->request()->headers();
    $sort = null;
    if(isset($_GET['sort'])) $sort = $_GET['sort'];
    
    $order = null;
    if(isset($_GET['order'])) $order = $_GET['order'];
    
    $rows = 10;
    if(isset($_GET['rows'])) $rows = $_GET['rows'];
    
    $page = 1;
    if(isset($_GET['page'])) $page = $_GET['page'];
    
    $filterRules = null;
    if(isset($_GET['filterRules'])) $filterRules = $_GET['filterRules'];
    
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillSingularFirmMachineTools_infoFirmMachineTool" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode  = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
 
    $resDataGrid = $BLL->fillSingularFirmMachineTools(array('language_code' => $vLanguageCode,
        'page' => $page,
        'rows' => $rows,
        'sort' => $sort,
        'order' => $order,     
        'pk' => $pk,
        'filterRules' => $filterRules));    
 
    $resTotalRowCount = $BLL->fillSingularFirmMachineToolsRtc(array('pk' => $pk,
                                                'language_code' => $vLanguageCode));
 
    $flows = array();
    foreach ($resDataGrid['resultSet'] as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "firm_id" => $flow["firm_id"],
            "firm_name" => $flow["firm_name"],
            "sys_machine_tool_id" => $flow["sys_machine_tool_id"],            
            
            "machine_tool_name" => $flow["machine_tool_name"],
            "machine_tool_name_eng" => $flow["machine_tool_name_eng"],
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "act_parent_id" => $flow["act_parent_id"],
            "language_parent_id" => $flow["language_parent_id"],
            "owner_id" => $flow["owner_id"],
            "owner_username" => $flow["owner_username"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allow" => $flow["cons_allow"],
            "availability_id" => $flow["availability_id"],
            "state_availability" => $flow["state_availability"],
            
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
                                                                                        
		 
			
           
            
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount['resultSet'][0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));

});


/**
 * getting user details for consultant confirmation process
 * @author Mustafa Zeynel Dağlı
 * @since 09/02/2016
 */
$app->get("/pkGetConsConfirmationProcessDetails_sysOsbConsultants/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmMachineToolBLL');

    $headerParams = $app->request()->headers();

    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkGetConsConfirmationProcessDetails_sysOsbConsultants" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    $profileID;
    if(isset($_GET['profile_id'])) $profileID = $_GET['profile_id'];

    $result = $BLL->getConsConfirmationProcessDetails(array('profile_id' => $profileID,
                                                         'pk' => $pk));    
    //print_r($resDataGrid['$result']);
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(

 
            "id" => $flow["id"],
            "firmname" => $flow["firm_name"],
            "username" => $flow["username"],   
            "sgkno" => $flow["sgk_sicil_no"],
            "languagecode" => $flow["language_code"],
            "iletisimadresi" => $flow["iletisimadresi"],
            "faturaadresi" => $flow["faturaadresi"],
            "irtibattel" => $flow["irtibattel"],
            "irtibatcep" => $flow["irtibatcep"],
            "sdate" => $flow["s_date"],

            
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
  
    
    
});

 
 


$app->run();
