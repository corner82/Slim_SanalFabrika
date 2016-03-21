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
/*$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareMQManager());*/
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());




/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkFillGrid_infoUsers/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    $vPkTemp = $headerParams['X-Public-Temp'];

    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'search_name' => $vSearchName,
        'pk' => $pk,
        'pktemp' => $vPkTemp));

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
    $fConsAllowId = $_GET['cons_allow_id'];
    $fPreferredLanguage = $_GET['preferred_language'];
    $fpersonIdNumber = $_GET['personIdNumber'];
    $foperationtypeid = $_GET['operation_type_id'];


    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];

    $resDataInsert = $BLL->insert(array(
        'profile_public' => $fProfilePublic,
        'name' => $fName,
        'surname' => $fSurname,
        'username' => $fUsername,
        'password' => $fPassword,
        'auth_email' => $fAuthEmail,
        'language_code' => $fLanguageCode,
        'cons_allow_id' => $fConsAllowId,
        'preferred_language' => $fPreferredLanguage,
        'personIdNumber' => $fpersonIdNumber,
        'operation_type_id' => $foperationtypeid,
        'pk' => $vPk));

    $app->response()->header("Content-Type", "application/json");



    $app->response()->body(json_encode($resDataInsert));
}
);

/**
 *  * Okan CIRAN
 * @since 27-01-2016
 */
$app->get("/tempInsert_infoUsers/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $vProfilePublic = $_GET['profile_public'];
    $vName = $_GET['name'];
    $vSurname = $_GET['surname'];
    $vUsername = $_GET['username'];
    $vPassword = $_GET['password'];
    $vAuthEmail = $_GET['auth_email'];

  
    
    $headerParams = $app->request()->headers();

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $vPreferredLanguage =  trim($_GET['preferred_language'] );
    }

    $fProfilePublic = $vProfilePublic;
    $fName = $vName;
    $fSurname = $vSurname;
    $fUsername = $vUsername;
    $fPassword = $vPassword;
    $fAuthEmail = $vAuthEmail;
    $fLanguageCode = $vLanguageCode;
    $fPreferredLanguage = $vPreferredLanguage;
 
    $resDataInsert = $BLL->insertTemp(array(
        'profile_public' => $fProfilePublic,
        'name' => $fName,
        'surname' => $fSurname,
        'username' => $fUsername,
        'password' => $fPassword,
        'auth_email' => $fAuthEmail,
        'language_code' => $fLanguageCode,
        'preferred_language' => $fPreferredLanguage,
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
 * @since 27-01-2016
 */
$app->get("/pktempUpdate_infoUsers/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $vProfilePublic = $_GET['profile_public'];
    $vName = $_GET['name'];
    $vSurname = $_GET['surname'];
    $vUsername = $_GET['username'];
    $vPassword = $_GET['password'];
    $vAuthEmail = $_GET['auth_email'];

    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];    
    

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $vPreferredLanguage =  trim($_GET['preferred_language'] );
    }

    $fProfilePublic = $vProfilePublic;
    $fName = $vName;
    $fSurname = $vSurname;
    $fUsername = $vUsername;
    $fPassword = $vPassword;
    $fAuthEmail = $vAuthEmail;
    $fLanguageCode = $vLanguageCode;
    $fPreferredLanguage = $vPreferredLanguage;
 
    $resDataInsert = $BLL->UpdateTemp(array(
        'profile_public' => $fProfilePublic,
        'name' => $fName,
        'surname' => $fSurname,
        'username' => $fUsername,
        'password' => $fPassword,
        'auth_email' => $fAuthEmail,
        'language_code' => $fLanguageCode,
        'preferred_language' => $fPreferredLanguage,
        'pktemp' => $vPkTemp
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
 * @since 25-01-2016
 */
$app->get("/pkUpdate_infoUsers/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    $vpk = $headerParams['X-Public'];
    $vPkTemp = $headerParams['X-Public-Temp'];

    $vFCheck = 0;
    if (isset($_GET['f_check'])) {
        $vFCheck = $_GET ["f_check"];
    }
    $vAuthAllowId = 0;
    if (isset($_GET['auth_allow_id'])) {
        $vFCheck = $_GET ["auth_allow_id"];
    }
    $vConsAllowId = 0;
    if (isset($_GET['cons_allow_id'])) {
        $vConsAllowId = $_GET ["cons_allow_id"];
    }
    $vActParentId = 0;
    if (isset($_GET['act_parent_id'])) {
        $vActParentId = $_GET ["act_parent_id"];
    }

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
    $vPreferredLanguage = 647;
    if (isset($_GET['preferred_language'])) {
        $vPreferredLanguage = strtolower(trim($_GET['preferred_language']));
    }



    $vID = $_GET['id'];
    $vOperationTypeId = $_GET['operation_type_id'];
    $vActive = $_GET['active'];
    $vProfilePublic = $_GET['profile_public'];
    $vName = $_GET['name'];
    $vSurname = $_GET['surname'];
    $vUsername = $_GET['username'];
    $vPassword = $_GET['password'];
    $vAuthEmail = $_GET['auth_email'];



    $validater = $app->getServiceManager()->get('validationChainerServiceForZendChainer');
    $validatorChainUrl = new Zend\Validator\ValidatorChain();
    $validater->offsetSet(array_search($_GET['url'], $_GET), new \Utill\Validation\Chain\ZendValidationChainer($app, $_GET['url'], $validatorChainUrl->attach(
                    new Zend\Validator\StringLength(array('min' => 6,
                'max' => 50)))
            // ->attach(new Zend\I18n\Validator\Alnum())    
    ));


    $validatorChainName = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('name', new \Utill\Validation\Chain\ZendValidationChainer($app, $vName, $validatorChainName->attach(
                            new Zend\Validator\StringLength(array('min' => 2,
                        'max' => 80)))
                    ->attach(new Zend\I18n\Validator\Alpha())
    ));
    $validatorChainSurname = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('surname', new \Utill\Validation\Chain\ZendValidationChainer($app, $vSurname, $validatorChainSurname->attach(
                            new Zend\Validator\StringLength(array('min' => 2,
                        'max' => 80)))
                    ->attach(new Zend\I18n\Validator\Alpha())
    ));

    $validatorChainUsername = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('username', new \Utill\Validation\Chain\ZendValidationChainer($app, $vUsername, $validatorChainUsername->attach(
                    new Zend\Validator\StringLength(array('min' => 6,
                'max' => 35)))
            //   ->attach(new Zend\I18n\Validator\Alnum())    
    ));

    $validatorChainPassword = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('password', new \Utill\Validation\Chain\ZendValidationChainer($app, $vPassword, $validatorChainPassword->attach(
                            new Zend\Validator\StringLength(array('min' => 8,
                        'max' => 20)))
                    ->attach(new Zend\I18n\Validator\Alnum())
    ));
    $validatorChainAuthEmail = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('auth_email', new \Utill\Validation\Chain\ZendValidationChainer($app, $vAuthEmail, $validatorChainAuthEmail->attach(
                            new Zend\Validator\StringLength(array('min' => 8,
                        'max' => 20)))
                    //->attach(new Zend\I18n\Validator\Alnum()) 
                    ->attach(new Zend\Validator\EmailAddress())
    ));

    $validatorChainPreferredLanguage = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('preferred_language', new \Utill\Validation\Chain\ZendValidationChainer($app, $vPreferredLanguage, $validatorChainPreferredLanguage->attach(
                            new Zend\Validator\StringLength(array(//'min' => 8,
                        'max' => 2)))
                    ->attach(new Zend\I18n\Validator\Alpha())
    ));

    $validatorChainLanguageCode = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('language_code', new \Utill\Validation\Chain\ZendValidationChainer($app, $vLanguageCode, $validatorChainLanguageCode->attach(
                            new Zend\Validator\StringLength(array('min' => 2,
                        'max' => 2)))
                    ->attach(new Zend\I18n\Validator\Alpha())
    ));

    $validatorChainId = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('id', new \Utill\Validation\Chain\ZendValidationChainer($app, $vID, $validatorChainId->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                            // ,'max' => 2
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));

    $validatorChainOperationTypeId = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('operation_type_id', new \Utill\Validation\Chain\ZendValidationChainer($app, $vOperationTypeId, $validatorChainOperationTypeId->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                            // ,'max' => 2
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));

    $validatorChainActive = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('active', new \Utill\Validation\Chain\ZendValidationChainer($app, $vActive, $validatorChainActive->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));

    $validatorChainProfilePublic = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('profile_public', new \Utill\Validation\Chain\ZendValidationChainer($app, $vProfilePublic, $validatorChainProfilePublic->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));

    $validatorChainFCheck = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('f_check', new \Utill\Validation\Chain\ZendValidationChainer($app, $vFCheck, $validatorChainFCheck->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));


    $validatorChainAuthAllowId = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('auth_allow_id', new \Utill\Validation\Chain\ZendValidationChainer($app, $vAuthAllowId, $validatorChainAuthAllowId->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));

    $validatorChainConsAllowId = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('cons_allow_id', new \Utill\Validation\Chain\ZendValidationChainer($app, $vConsAllowId, $validatorChainConsAllowId->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));


    $validatorChainActParentId = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('act_parent_id', new \Utill\Validation\Chain\ZendValidationChainer($app, $vActParentId, $validatorChainActParentId->attach(
                            new Zend\Validator\StringLength(array('min' => 1
                        , 'max' => 1
                    )))
                    ->attach(new Zend\Validator\Digits())
    ));



    $validater->validate();
    $messager = $app->getServiceManager()->get('validatorMessager');
    print_r($messager->getValidationMessage());






    $fID = $vID;
    $fOperationTypeId = $vOperationTypeId;
    $fActive = $vActive;
    $fActParentId = $vActParentId;
    $fLanguageCode = $vLanguageCode;
    $fProfilePublic = $vProfilePublic;
    $fName = $vName;
    $fSurname = $vSurname;
    $fUsername = $vUsername;
    $fPassword = $vPassword;
    $fAuthEmail = $vAuthEmail;
    $fPreferredLanguage = $vPreferredLanguage;
    $fFCheck = $vFCheck;
    $fAuthAllowId = $vAuthAllowId;
    $fConsAllowId = $vConsAllowId;
    $fpk = $vpk;
    $fPkTemp = $vPkTemp;

    /*
     * filtre işlemleri
     */

    $resDataUpdate = $BLL->update(array(
        'id' => $fID,
        'f_check' => $fFCheck,
        'operation_type_id' => $fOperationTypeId,
        'active' => $fActive,
        'act_parent_id' => $fActParentId,
        'language_code' => $fLanguageCode,
        'profile_public' => $fProfilePublic,
        'name' => $fName,
        'surname' => $fSurname,
        'username' => $fUsername,
        'password' => $fPassword,
        'auth_email' => $fAuthEmail,
        'auth_allow_id' => $fAuthAllowId,
        'cons_allow_id' => $fConsAllowId,
        'preferred_language' => $fPreferredLanguage,
        'pk' => $fpk,
        'pktemp' => $vPkTemp));

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
        'operation_type_id' => $_GET['operation_type_id'],
        'pk' => $pk));

    $app->response()->header("Content-Type", "application/json");

    $app->response()->body(json_encode($resDataUpdate));
});


/**
 *  * Okan CIRAN
 * @since 25-01-2016
 */
$app->get("/pkDelete_infoUsers/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoUsersBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->delete(array(
        'id' => $_GET['id'],
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
            "personIdNumber" => $_GET['personIdNumber'],
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
