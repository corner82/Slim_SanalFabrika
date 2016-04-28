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
 * @since 25-01-2016
 */
$app->get("/pkFillGrid_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkFillGrid_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                $app, $_GET['language_code']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }   
    
    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'language_code' => $vLanguageCode,
        'pk' => $pk,
      ));

    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('language_code' => $vLanguageCode));
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "name" => $flow["name"],
            "surname" => $flow["surname"],
            "username" => $flow["username"],
            "auth_email" => $flow["auth_email"],
            "user_language" => $flow["user_language"],
            "language_name" => $flow["language_name"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "deleted" => $flow["deleted"],
            "op_user_id" => $flow["op_user_id"],
            "op_user_name" => $flow["op_user_name"],            
            "act_parent_id" => $flow["act_parent_id"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alow" => $flow["auth_alow"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allow" => $flow["cons_allow"],
            "consultant_id" => $flow["consultant_id"],
            "cons_name" => $flow["cons_name"],
            "cons_surname" => $flow["cons_surname"],            
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
 * @since 25-01-2016
 */
$app->get("/pkInsert_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkInsert_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    } 
    $resDataInsert = $BLL->insert(array(
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pk' => $pk));

    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 27-01-2016
 */
$app->get("/tempInsert_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();
     

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }
    if ($vPreferredLanguage<0 ) {$vPreferredLanguage = 647 ;}
    
    $resDataInsert = $BLL->insertTemp(array(
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
    ));

    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);


/**
 *  * Okan CIRAN
 * @since 27-01-2016
 */
$app->get("/pktempUpdate_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');
    $headerParams = $app->request()->headers();    
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pktempUpdate_infoUsers" end point, X-Public variable not found');
    $PkTemp = $headerParams['X-Public-Temp'];    

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    }
 
    $resDataInsert = $BLL->UpdateTemp(array(
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pktemp' => $PkTemp
    ));
    
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkUpdate_infoUsers/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdate_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                    $app, $_GET['language_code']));
    }
    $vId =-1;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['id']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $stripper->offsetSet('preferred_language', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['preferred_language']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['profile_public']));
    }
    $vName = NULL;
    if (isset($_GET['name'])) {
        $stripper->offsetSet('name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['name']));
    }
    $vSurname = NULL;
    if (isset($_GET['surname'])) {
        $stripper->offsetSet('surname', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['surname']));
    }
    $vUsername = NULL;
    if (isset($_GET['username'])) {
        $stripper->offsetSet('username', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                    $app, $_GET['username']));
    }
    $vPassword = NULL;
    if (isset($_GET['password'])) {
        $stripper->offsetSet('password', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                    $app, $_GET['password']));
    }
    $vAuthEmail = NULL;
    if (isset($_GET['auth_email'])) {
        $stripper->offsetSet('auth_email', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL1, 
                $app, $_GET['auth_email']));
    }

    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('preferred_language')) {
        $vPreferredLanguage = $stripper->offsetGet('preferred_language')->getFilterValue();
    }
    if ($stripper->offsetExists('name')) {
        $vName = $stripper->offsetGet('name')->getFilterValue();
    }
    if ($stripper->offsetExists('surname')) {
        $vSurname = $stripper->offsetGet('surname')->getFilterValue();
    }
    if ($stripper->offsetExists('username')) {
        $vUsername = $stripper->offsetGet('username')->getFilterValue();
    }
    if ($stripper->offsetExists('password')) {
        $vPassword = $stripper->offsetGet('password')->getFilterValue();
    }
    if ($stripper->offsetExists('auth_email')) {
        $vAuthEmail = $stripper->offsetGet('auth_email')->getFilterValue();
    } 

    $resDataUpdate = $BLL->update(array(
        'id' => $vId,
        'profile_public' => $vProfilePublic,
        'name' => $vName,
        'surname' => $vSurname,
        'username' => $vUsername,
        'password' => $vPassword,
        'auth_email' => $vAuthEmail,
        'language_code' => $vLanguageCode,
        'preferred_language' => $vPreferredLanguage,
        'pk' => $pk));

    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataUpdate));
});

/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkDeletedAct_infoUsers/", function () use ($app ) {
$stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkDeletedAct_infoUsers" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];   
    $vId = -1;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED, 
                    $app, $_GET['id']));
    }
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    $resDataUpdate = $BLL->deletedAct(array(
        'id' => $vId,       
        'pk' => $pk));
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($resDataUpdate));
});

 
 
$app->run();
