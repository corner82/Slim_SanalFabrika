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
 * @since 15-02-2016
 */
$app->get("/pkFillMachineToolGroups_sysMachineToolGroups/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysMachineToolGroupsBLL');
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }
     $vParentId = 0;
    if (isset($_GET['parent_id'])) {
        $vParentId = strtolower(trim($_GET['parent_id']));
    }
    $vState =NULL;
    if (isset($_GET['state'])) {
        $vState = strtolower(trim($_GET['state']));
    }
    $vLastNode =NULL;
    if (isset($_GET['last_node'])) {
        $vLastNode = strtolower(trim($_GET['last_node']));
    }
    $vMachine= NULL;
     if (isset($_GET['machine'])) {
        $vMachine = strtolower(trim($_GET['machine']));
    }

    if (isset($_GET['parent_id']) && $_GET['parent_id'] != "") {
        $resCombobox = $BLL->fillMachineToolGroups(array('parent_id' => $vParentId,
                                                         'language_code' => $vLanguageCode, 
                                                         'state' => $vState,
                                                         'last_node' => $vLastNode,
                                                         'machine' => $vMachine,
            
                                                                ));
    } else {
        $resCombobox = $BLL->fillMachineToolGroups(array('language_code' => $vLanguageCode));
    }

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => $flow["state_type"], //   'closed',
            "checked" => false,
            "icon_class"=>$flow["icon_class"], 
            "attributes" => array("root" => $flow["root_type"], "active" => $flow["active"]
                ,"machine" => $flow["machine"],"last_node" => $flow["last_node"]),
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
 * @since 07-01-2016
 */
$app->run();
