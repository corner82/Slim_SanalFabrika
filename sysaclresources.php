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
 * @since 13-01-2016
 */
$app->get("/pkFillComboBoxMainResources_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    //print_r('--****************get parent--' );  
    $resCombobox = $BLL->fillComboBoxMainResources();
    //print_r($resDataMenu);


    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'open',
            "checked" => false,
            "attributes" => array("notroot" => true, "active" => $flow["active"], "deleted" => $flow["deleted"]),
        );
    }
    //   print_r($flows);

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkFillComboBoxFullResources_sysAclResources/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    $resCombobox = $BLL->fillComboBoxFullResources();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'closed',
            "checked" => false,
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkFillGrid_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];
    //print_r($resDataMenu);


    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'pk' => $pk));
    //print_r($resDataGrid);

    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
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
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
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
 * @since 13-01-2016
 */
$app->get("/pkInsert_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');
    $errorcode = 0;
    $hatasayisi = 0;
    $hatasayisi1 = 0;
    $hatasayisi2 = 0;
    $hatasayisi3 = 0;
    ////******************Filters ******************//////////
    // Filters are called from service manager
    $filterDefault = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_DEFAULT);
    $filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);
    $filterHTMLTagsAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    $filterLowerCase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_LOWER_CASE);
    $filterPregReplace = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_PREG_REPLACE);
    $filterSQLReservedWords = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS);
    $filterRemoveText = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_REMOVE_TEXT);
    $filterRemoveNumber = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_REMOVE_NUMBER);
    $filterToNull = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_TONULL);
    $filterAlpha = new \Zend\I18n\Filter\Alnum(array('allowWhiteSpace' => true));

    ////******************Filters ******************//////////
    ////******************Validators ******************//////////   
    $validatorAlpha = new Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true));
    $validatorStringLength = new Zend\Validator\StringLength(array('min' => 3, 'max' => 20));
    $validatorNotEmptyString = new Zend\Validator\NotEmpty();


    $vName = $_GET['name'];
    $vIconClass = $_GET['icon_class'];
    $vParent = $_GET['parent'];
    $vUserId = $_GET['user_id'];
    $vDescription = $_GET['description'];



    if ($errorcode == 0) {
        $headerParams = $app->request()->headers();
        $vPk = $headerParams['X-Public'];



        $resDataInsert = $BLL->insert(array('name' => $vName,
            'icon_class' => $vIconClass,
            'parent' => $vParent,
            'user_id' => $vUserId,
            'description' => $vDescription,
            'pk' => $vPk));
        // print_r($resDataInsert);    



        $app->response()->header("Content-Type", "application/json");



        /* $app->contentType('application/json');
          $app->halt(302, '{"error":"Something went wrong"}');
          $app->stop(); */

        $app->response()->body(json_encode($resDataInsert));
    }
}
);
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkUpdate_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->update($_GET['id'], array('name' => $_GET['name'],
        'icon_class' => $_GET['icon_class'],
        'active' => $_GET['active'],
        'start_date' => $_GET['start_date'],
        'end_date' => $_GET['end_date'],
        'parent' => $_GET['parent'],
        'user_id' => $_GET['user_id'],
        'description' => $_GET['description'],
        'root' => $_GET['root'],
        'pk' => $pk));
    //print_r($resDataGrid);    

    $app->response()->header("Content-Type", "application/json");



    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});
/**
 *  * Okan CIRAN
 * @since 11-01-2016
 */
$app->get("/pkGetAll_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');


    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];
    //print_r($resDataMenu);


    $resDataGrid = $BLL->getAll();
    //print_r($resDataGrid);

    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
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
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
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
 * @since 13-01-2016
 */
$app->get("/pkDelete_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');


    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->delete($_GET['id'], array(
        'user_id' => $_GET['user_id'],
        'pk' => $pk));


    $app->response()->header("Content-Type", "application/json");



    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

$app->run();
