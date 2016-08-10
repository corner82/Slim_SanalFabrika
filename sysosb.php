<?php
// test commit for branch slim2
require 'vendor/autoload.php';
 
use \Services\Filter\Helper\FilterFactoryNames as stripChainers;



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




   

/**
 *  *  
  *  * zeynel dağlı
 * @since 11-09-2014
 */
$app->get("/pkGetAll_sysOsb/", function () use ($app ) {

    
    $BLL = $app->getBLLManager()->get('sysOsbBLL'); 
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
 
 
    $resCombobox = $BLL->getAll (array(   'pk' => $vPk
                                             ));  
 
        
 
    $menus = array();
    foreach ($resCombobox as $menu){
        $menus[]  = array(
            
        "id" => $menu["id"],
        "country_id" => $menu["country_id"],
        "country_name" => $menu["country_name"],  
        "name" => $menu["name"],  
        "name_eng" => $menu["name_eng"],
        "deleted" => $menu["deleted"],
        "state_deleted" => $menu["state_deleted"],                
        "active" => $menu["active"], 
	"state_active" => $menu["state_active"],                   
        "language_code" => $menu["language_code"], 
	"language_name" => $menu["language_name"], 
        "op_user_id" => $menu["op_user_id"], 
	"username" => $menu["username"],                
        "city_id" => $menu["city_id"],      
           
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
 * @since 09-08-2016
 
 */
$app->get("/pkFillOsbDdlist_sysOsb/", function () use ($app ) {  
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('sysOsbBLL');
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkFillOsbDdlist_sysOsb" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public']; 
   
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                                                                $app, 
                                                                $_GET['language_code']));
    }
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    } 
    $resCombobox = $BLL->fillOsbDdlist(array('language_code' => $vLanguageCode,));
    
    $flows = array();
    $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Lütfen Seçiniz",);
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "text" => html_entity_decode($flow["name"]),
            "value" => intval($flow["id"]),
            "selected" => false,
            "description" => html_entity_decode($flow["name_eng"]),
            // "imageSrc"=>$flow["logo"],             
            "attributes" => array(                 
                    "active" => $flow["active"],
                   
            ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});




$app->run();