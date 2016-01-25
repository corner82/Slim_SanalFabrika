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
 * @since 25-01-2016
 */
$app->get("/fillMainDefinitions_sysSpecificDefinitions/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL'); 
    $resCombobox = $BLL->fillMainDefinitions();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "text" => $flow["name"],
            "state" => 'open',
            "checked" => false,
            "attributes" => array("notroot" => true),
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
 * @since 25-01-2016
 */
$app->get("/fillFullDefinitions_sysSpecificDefinitions/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL');

    $resCombobox = $BLL->fillFullDefinitions();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
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
 * @since 25-01-2016
 */
$app->get("/fillCommunicationsTypes_sysSpecificDefinitions/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL');

    $resCombobox = $BLL->fillCommunicationsTypes();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
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
 * @since 25-01-2016
 */
$app->get("/fillBuildingType_sysSpecificDefinitions/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL');

    $resCombobox = $BLL->fillBuildingType();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
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
 * @since 25-01-2016
 */
$app->get("/fillOwnershipType_sysSpecificDefinitions/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL');

    $resCombobox = $BLL->fillOwnershipType();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
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
 * @since 25-01-2016
 */
$app->get("/fillPersonnelTypes_sysSpecificDefinitions/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysSpecificDefinitionsBLL');

    $resCombobox = $BLL->fillPersonnelTypes();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
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



$app->run();
