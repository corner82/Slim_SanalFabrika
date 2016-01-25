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
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareHMAC());



 
/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkFillGrid_infoUsers/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoUsersBLL');


    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'search_name' => $vSearchName,
        'pk' => $pk));

    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('search_name' => $vSearchName));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "f_check" => $flow["f_check"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "name" => $flow["name"],
            "surname" => $flow["surname"],
            "username" => $flow["username"],
            "auth_email" => $flow["auth_email"],
            "language_code" => $flow["language_code"],
            "language_name" => $flow["language_name"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "deleted" => $flow["deleted"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "act_parent_id" => $flow["act_parent_id"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alow" => $flow["auth_alow"],
            "cons_allow_id" => $flow["cons_allow_id"],
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
 * @since 25-01-2016
 */
$app->get("/pkInsert_infoUsers/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $fProfilePublic = $_GET['profile_public'];
    $fName = $_GET['name'];
    $fSurname = $_GET['surname'];
    $fUsername = $_GET['username'];
    $fPassword = $_GET['password'];
    $fAuthEmail = $_GET['auth_email'];
    $fLanguageCode = $_GET['language_code'];
    $fUserId = $_GET['user_id'];
    $fConsAllowId = $_GET['cons_allow_id'];
    $fOperationTypeId = $_GET['operation_type_id'];
    $fPreferredLanguage = $_GET['preferred_language'];


    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];

    $resDataInsert = $BLL->insert(array(    
        'profile_public' => $fProfilePublic ,
        'name' => $fName , 
        'surname' => $fSurname , 
        'username' => $fUsername, 
        'password' => $fPassword, 
        'auth_email' => $fAuthEmail, 
        'language_code' => $fLanguageCode, 
        'user_id' => $fUserId,  
        'cons_allow_id' => $fConsAllowId ,  
        'operation_type_id' => $fOperationTypeId,  
        'preferred_language' => $fPreferredLanguage,         
        'pk' => $vPk));

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkUpdate_infoUsers/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->update(array(
        'id' => $_GET['id'],
        'f_check' => $_GET['f_check'],
        'operation_type_id' => $_GET['operation_type_id'],
        'active' => $_GET['active'],
        'deleted' => $_GET['deleted'],
        'act_parent_id' => $_GET['act_parent_id'],
        'language_code' => $_GET['language_code'],
        'profile_public' => $_GET['profile_public'],
        'c_date' => $_GET['c_date'],
        'operation_type_id' => $_GET['operation_type_id'],
        'name' => $_GET['name'],
        'surname' => $_GET['surname'],
        'username' => $_GET['username'],        
        'password' => $_GET['password'],
        'auth_email' => $_GET['auth_email'],
        'auth_allow_id' => $_GET['auth_allow_id'],
        'user_id' => $_GET['user_id'],
        'act_parent_id' => $_GET['act_parent_id'],
        'cons_allow_id' => $_GET['cons_allow_id'],
        'language_code' => $_GET['language_code'],
        'preferred_language' => $_GET['preferred_language'],        
        'pk' => $pk));

 
    $app->response()->header("Content-Type", "application/json");


    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkDeletedAct_infoUsers/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->deletedAct(array(
        'id' => $_GET['id'],
        'f_check' => $_GET['f_check'],
        'operation_type_id' => $_GET['operation_type_id'],
        'active' => $_GET['active'],
        'deleted' => $_GET['deleted'],
        'act_parent_id' => $_GET['act_parent_id'],
        'language_code' => $_GET['language_code'],
        'profile_public' => $_GET['profile_public'],
        'c_date' => $_GET['c_date'],
        'operation_type_id' => $_GET['operation_type_id'],
        'name' => $_GET['name'],
        'surname' => $_GET['surname'],
        'username' => $_GET['username'],        
        'password' => $_GET['password'],
        'auth_email' => $_GET['auth_email'],
        'auth_allow_id' => $_GET['auth_allow_id'],
        'user_id' => $_GET['user_id'],
        'act_parent_id' => $_GET['act_parent_id'],
        'cons_allow_id' => $_GET['cons_allow_id'],
        'language_code' => $_GET['language_code'],
        'preferred_language' => $_GET['preferred_language'],        
        'pk' => $pk));

 
    $app->response()->header("Content-Type", "application/json");


    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkGetAll_infoUsers/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoUsersBLL');


    $resDataGrid = $BLL->getAll(array( 
        'pk' => $pk));

    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('search_name' => $vSearchName));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "f_check" => $flow["f_check"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "name" => $flow["name"],
            "surname" => $flow["surname"],
            "username" => $flow["username"],
            "auth_email" => $flow["auth_email"],
            "language_code" => $flow["language_code"],
            "language_name" => $flow["language_name"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "deleted" => $flow["deleted"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "act_parent_id" => $flow["act_parent_id"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alow" => $flow["auth_alow"],
            "cons_allow_id" => $flow["cons_allow_id"],
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
