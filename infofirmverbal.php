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

   
/**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/pkInsert_infoFirmVerbal/", function () use ($app ) {  
   $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmVerbalBLL');    
    $headerParams = $app->request()->headers();  
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkInsert_infoFirmVerbal" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    } 
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    }  
    $vFirmId = NULL;
    if (isset($_GET['firm_id'])) {
        $stripper->offsetSet('firm_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['firm_id']));
    }
    $vAbout = NULL;
    if (isset($_GET['about'])) {
         $stripper->offsetSet('about',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['about']));
    }   
    $vAboutEng = NULL;
    if (isset($_GET['about_eng'])) {
         $stripper->offsetSet('about_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['about_eng']));
    }   
    $vVerbal1Title = NULL;
    if (isset($_GET['verbal1_title'])) {
         $stripper->offsetSet('verbal1_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_title']));
    }   
    $vVerbal1 = NULL;
    if (isset($_GET['verbal1'])) {
         $stripper->offsetSet('verbal1',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1']));
    }        
    $vVerbal2Title = NULL;
    if (isset($_GET['verbal2_title'])) {
         $stripper->offsetSet('verbal2_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_title']));
    }   
    $vVerbal2 = NULL;
    if (isset($_GET['verbal2'])) {
         $stripper->offsetSet('verbal2',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2']));
    }        
    $vVerbal3Title = NULL;
    if (isset($_GET['verbal3_title'])) {
         $stripper->offsetSet('verbal3_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_title']));
    }   
    $vVerbal3 = NULL;
    if (isset($_GET['verbal3'])) {
         $stripper->offsetSet('verbal3',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3']));
    }
    $vVerbal1TitleEng = NULL;
    if (isset($_GET['verbal1_title_eng'])) {
         $stripper->offsetSet('verbal1_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_title_eng']));
    }
    $vVerbal1Eng = NULL;
    if (isset($_GET['verbal1_eng'])) {
         $stripper->offsetSet('verbal1_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_eng']));
    }
    $vVerbal2TitleEng = NULL;
    if (isset($_GET['verbal2_title_eng'])) {
         $stripper->offsetSet('verbal2_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_title_eng']));
    }
    $vVerbal2Eng = NULL;
    if (isset($_GET['verbal2_eng'])) {
         $stripper->offsetSet('verbal2_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_eng']));
    }
    $vVerbal3TitleEng = NULL;
    if (isset($_GET['verbal3_title_eng'])) {
         $stripper->offsetSet('verbal3_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_title_eng']));
    }
    $vVerbal3Eng = NULL;
    if (isset($_GET['verbal3_eng'])) {
         $stripper->offsetSet('verbal3_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_eng']));
    }
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('firm_id')) {
        $vFirmId = $stripper->offsetGet('firm_id')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('about')) {
        $vAbout = $stripper->offsetGet('about')->getFilterValue();
    }
    if ($stripper->offsetExists('about_eng')) {
        $vAboutEng = $stripper->offsetGet('about_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_title')) {
        $vVerbal1Title = $stripper->offsetGet('verbal1_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1')) {
        $vVerbal1 = $stripper->offsetGet('verbal1')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_title')) {
        $vVerbal2Title = $stripper->offsetGet('verbal2_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2')) {
        $vVerbal2 = $stripper->offsetGet('verbal2')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_title')) {
        $vVerbal3Title = $stripper->offsetGet('verbal3_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3')) {
        $vVerbal3 = $stripper->offsetGet('verbal3')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_title_eng')) {
        $vVerbal1TitleEng = $stripper->offsetGet('verbal1_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_eng')) {
        $vVerbal1Eng = $stripper->offsetGet('verbal1_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_title_eng')) {
        $vVerbal2TitleEng = $stripper->offsetGet('verbal2_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_eng')) {
        $vVerbal2Eng = $stripper->offsetGet('verbal2_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_title_eng')) {
        $vVerbal3TitleEng = $stripper->offsetGet('verbal3_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_eng')) {
        $vVerbal3Eng = $stripper->offsetGet('verbal3_eng')->getFilterValue();
    }
      
    $resDataInsert = $BLL->insert(array(   
            'language_code' => $vLanguageCode,
            'firm_id'=> $vFirmId,  
            'profile_public'=> $vProfilePublic,
            'about'=> $vAbout,
            'about_eng'=> $vAboutEng,
            'verbal1_title'=> $vVerbal1Title,
            'verbal1'=> $vVerbal1,
            'verbal2_title'=> $vVerbal2Title,
            'verbal2'=> $vVerbal2,
            'verbal3_title'=> $vVerbal3Title,
            'verbal3'=> $vVerbal3,            
            'verbal1_title_eng'=> $vVerbal1TitleEng,
            'verbal1_eng'=> $vVerbal1Eng,
            'verbal2_title_eng'=> $vVerbal2TitleEng,
            'verbal2_eng'=> $vVerbal2Eng,
            'verbal3_title_eng'=> $vVerbal3TitleEng,
            'verbal3_eng'=> $vVerbal3Eng,
            'pk' => $pk,        
            ));

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataInsert));
}
); 

/**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/pkUpdate_infoFirmVerbal/", function () use ($app ) {
   $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmVerbalBLL');    
    $headerParams = $app->request()->headers();  
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkInsert_infoFirmVerbal" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    } 
    $vId = 0;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }  
    $vActive = 0;
    if (isset($_GET['active'])) {
        $stripper->offsetSet('active', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['active']));
    } 
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    }  
    $vFirmId = NULL;
    if (isset($_GET['firm_id'])) {
        $stripper->offsetSet('firm_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['firm_id']));
    }
    $vAbout = NULL;
    if (isset($_GET['about'])) {
         $stripper->offsetSet('about',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['about']));
    }   
    $vAboutEng = NULL;
    if (isset($_GET['about_eng'])) {
         $stripper->offsetSet('about_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['about_eng']));
    }   
    $vVerbal1Title = NULL;
    if (isset($_GET['verbal1_title'])) {
         $stripper->offsetSet('verbal1_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_title']));
    }   
    $vVerbal1 = NULL;
    if (isset($_GET['verbal1'])) {
         $stripper->offsetSet('verbal1',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1']));
    }        
    $vVerbal2Title = NULL;
    if (isset($_GET['verbal2_title'])) {
         $stripper->offsetSet('verbal2_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_title']));
    }   
    $vVerbal2 = NULL;
    if (isset($_GET['verbal2'])) {
         $stripper->offsetSet('verbal2',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2']));
    }        
    $vVerbal3Title = NULL;
    if (isset($_GET['verbal3_title'])) {
         $stripper->offsetSet('verbal3_title',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_title']));
    }   
    $vVerbal3 = NULL;
    if (isset($_GET['verbal3'])) {
         $stripper->offsetSet('verbal3',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3']));
    }
    $vVerbal1TitleEng = NULL;
    if (isset($_GET['verbal1_title_eng'])) {
         $stripper->offsetSet('verbal1_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_title_eng']));
    }
    $vVerbal1Eng = NULL;
    if (isset($_GET['verbal1_eng'])) {
         $stripper->offsetSet('verbal1_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal1_eng']));
    }
    $vVerbal2TitleEng = NULL;
    if (isset($_GET['verbal2_title_eng'])) {
         $stripper->offsetSet('verbal2_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_title_eng']));
    }
    $vVerbal2Eng = NULL;
    if (isset($_GET['verbal2_eng'])) {
         $stripper->offsetSet('verbal2_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal2_eng']));
    }
    $vVerbal3TitleEng = NULL;
    if (isset($_GET['verbal3_title_eng'])) {
         $stripper->offsetSet('verbal3_title_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_title_eng']));
    }
    $vVerbal3Eng = NULL;
    if (isset($_GET['verbal3_eng'])) {
         $stripper->offsetSet('verbal3_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['verbal3_eng']));
    }
    
    $stripper->strip();
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    }
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('firm_id')) {
        $vFirmId = $stripper->offsetGet('firm_id')->getFilterValue();
    }
    if ($stripper->offsetExists('active')) {
        $vActive = $stripper->offsetGet('active')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }
    if ($stripper->offsetExists('about')) {
        $vAbout = $stripper->offsetGet('about')->getFilterValue();
    }
    if ($stripper->offsetExists('about_eng')) {
        $vAboutEng = $stripper->offsetGet('about_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_title')) {
        $vVerbal1Title = $stripper->offsetGet('verbal1_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1')) {
        $vVerbal1 = $stripper->offsetGet('verbal1')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_title')) {
        $vVerbal2Title = $stripper->offsetGet('verbal2_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2')) {
        $vVerbal2 = $stripper->offsetGet('verbal2')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_title')) {
        $vVerbal3Title = $stripper->offsetGet('verbal3_title')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3')) {
        $vVerbal3 = $stripper->offsetGet('verbal3')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_title_eng')) {
        $vVerbal1TitleEng = $stripper->offsetGet('verbal1_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal1_eng')) {
        $vVerbal1Eng = $stripper->offsetGet('verbal1_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_title_eng')) {
        $vVerbal2TitleEng = $stripper->offsetGet('verbal2_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal2_eng')) {
        $vVerbal2Eng = $stripper->offsetGet('verbal2_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_title_eng')) {
        $vVerbal3TitleEng = $stripper->offsetGet('verbal3_title_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('verbal3_eng')) {
        $vVerbal3Eng = $stripper->offsetGet('verbal3_eng')->getFilterValue();
    }
      
    $resDataInsert = $BLL->update(array( 
            'id' => $vId,
            'language_code' => $vLanguageCode,
            'firm_id'=> $vFirmId,  
            'profile_public'=> $vProfilePublic,
            'active'=> $vActive,
            'about'=> $vAbout,
            'about_eng'=> $vAboutEng,
            'verbal1_title'=> $vVerbal1Title,
            'verbal1'=> $vVerbal1,
            'verbal2_title'=> $vVerbal2Title,
            'verbal2'=> $vVerbal2,
            'verbal3_title'=> $vVerbal3Title,
            'verbal3'=> $vVerbal3,            
            'verbal1_title_eng'=> $vVerbal1TitleEng,
            'verbal1_eng'=> $vVerbal1Eng,
            'verbal2_title_eng'=> $vVerbal2TitleEng,
            'verbal2_eng'=> $vVerbal2Eng,
            'verbal3_title_eng'=> $vVerbal3TitleEng,
            'verbal3_eng'=> $vVerbal3Eng,
            'pk' => $pk,        
            ));

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataInsert));
}
); 

/**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/pkFillGrid_infoFirmVerbal/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmVerbalBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillGrid_infoFirmVerbal" end point, X-Public variable not found');
    }
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
     $vPage = NULL;
    if (isset($_GET['page'])) {
         $stripper->offsetSet('page',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['page']));
    }       
    $vRows = NULL;
    if (isset($_GET['rows'])) {
         $stripper->offsetSet('rows',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['rows']));
    }   
    $vSort = NULL;
    if (isset($_GET['sort'])) {
        $stripper->offsetSet('sort', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['sort']));
    }
    $vOrder = NULL;
    if (isset($_GET['order'])) {
        $stripper->offsetSet('order', $stripChainerFactory->get(stripChainers::FILTER_ONLY_ORDER,
                                                $app,
                                                $_GET['order']));
    }    
 
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }    
    if ($stripper->offsetExists('page')) {
        $vPage = $stripper->offsetGet('page')->getFilterValue();
    } 
    if ($stripper->offsetExists('rows')) {
        $vRows = $stripper->offsetGet('rows')->getFilterValue();
    }        
    if ($stripper->offsetExists('sort')) {
        $vSort = $stripper->offsetGet('sort')->getFilterValue();
    }    
    if ($stripper->offsetExists('order')) {
        $vOrder = $stripper->offsetGet('order')->getFilterValue();
    }
      
    $resDataGrid = $BLL->fillGrid(array(
        'language_code' => $vLanguageCode,
        'page' => $vPage,
        'rows' => $vRows,
        'sort' => $vSort,
        'order' => $vOrder,   
    ));
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array(
        'language_code' => $vLanguageCode,
    ));
 
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "firm_id" => $flow["firm_id"],
            "firm_name" => $flow["firm_name"],
            "firm_name_eng" => $flow["firm_name_eng"],
            "about" => $flow["about"],
            "about_eng" => $flow["about_eng"],
            
            "verbal1_title" => $flow["verbal1_title"],
            "verbal1_title_eng" => $flow["verbal1_title_eng"],
            "verbal1" => $flow["verbal1"],         
            "verbal1_eng" => $flow["verbal1_eng"],
            
            "verbal2_title" => $flow["verbal2_title"],
            "verbal2_title_eng" => $flow["verbal2_title_eng"],
            "verbal2" => $flow["verbal2"],         
            "verbal2_eng" => $flow["verbal2_eng"],
            
            "verbal3_title" => $flow["verbal3_title"],
            "verbal3_title_eng" => $flow["verbal3_title_eng"],
            "verbal3" => $flow["verbal3"],
            "verbal3_eng" => $flow["verbal3_eng"],
            
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],                     
            "network_key" => $flow["network_key"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "consultant_id" => $flow["consultant_id"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],            
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "language_id" => $flow["language_id"],
            "language_name" => $flow["language_name"],
            "op_user_id" => $flow["op_user_id"],
            "op_user_name" => $flow["op_user_name"],
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
 * @since 26-04-2016
 */
$app->get("/pkFillUsersFirmVerbalNpk_infoFirmVerbal/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmVerbalBLL');
    $headerParams = $app->request()->headers(); 
     if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillUsersFirmVerbalNpk_infoFirmVerbal" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vNetworkKey = NULL;
    if (isset($_GET['npk'])) {
        $stripper->offsetSet('npk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['npk']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('npk')) {
        $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
    } 
    $resDataGrid = $BLL->fillUsersFirmVerbalNpk(array(
        'language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,  
        'pk'=> $pk,
    ));
    
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
           "id" => $flow["id"],
            "firm_id" => $flow["firm_id"],
            "firm_name" => $flow["firm_name"],
            "firm_name_eng" => $flow["firm_name_eng"],
            "about" => $flow["about"],
            "about_eng" => $flow["about_eng"],
            
            "verbal1_title" => $flow["verbal1_title"],
            "verbal1_title_eng" => $flow["verbal1_title_eng"],
            "verbal1" => $flow["verbal1"],         
            "verbal1_eng" => $flow["verbal1_eng"],
            
            "verbal2_title" => $flow["verbal2_title"],
            "verbal2_title_eng" => $flow["verbal2_title_eng"],
            "verbal2" => $flow["verbal2"],         
            "verbal2_eng" => $flow["verbal2_eng"],
            
            
            "verbal3_title" => $flow["verbal3_title"],
            "verbal3_title_eng" => $flow["verbal3_title_eng"],
            "verbal3" => $flow["verbal3"],
            "verbal3_eng" => $flow["verbal3_eng"],
            
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],                     
            "network_key" => $flow["network_key"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "consultant_id" => $flow["consultant_id"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],            
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "language_id" => $flow["language_id"],
            "language_name" => $flow["language_name"],
            "op_user_id" => $flow["op_user_id"],
            "op_user_name" => $flow["op_user_name"],
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();    
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

 /**
 *  * Okan CIRAN
 * @since 26-04-2016
 */
$app->get("/fillUsersFirmVerbalNpkGuest_infoFirmVerbal/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmVerbalBLL');
    $headerParams = $app->request()->headers(); 
     
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vNetworkKey = NULL;
    if (isset($_GET['npk'])) {
        $stripper->offsetSet('npk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['npk']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('npk')) {
        $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
    } 
    $resDataGrid = $BLL->fillUsersFirmVerbalNpkGuest(array(
        'language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,  
      
    ));
    
    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "firm_id" => $flow["firm_id"],
            "firm_name" => $flow["firm_name"],
            "firm_name_eng" => $flow["firm_name_eng"],
            "about" => $flow["about"],
            "about_eng" => $flow["about_eng"],
            
            "verbal1_title" => $flow["verbal1_title"],
            "verbal1_title_eng" => $flow["verbal1_title_eng"],
            "verbal1" => $flow["verbal1"],
            "verbal1_eng" => $flow["verbal1_eng"],
            
            "verbal2_title" => $flow["verbal2_title"],
            "verbal2_title_eng" => $flow["verbal2_title_eng"],
            "verbal2" => $flow["verbal2"],
            "verbal2_eng" => $flow["verbal2_eng"],
            
            "verbal3_title" => $flow["verbal3_title"],
            "verbal3_title_eng" => $flow["verbal3_title_eng"],
            "verbal3" => $flow["verbal3"],
            "verbal3_eng" => $flow["verbal3_eng"],   
            
            "language_id" => $flow["language_id"],
            "language_name" => $flow["language_name"],
            
        
	 
            
            "attributes" => array("notroot" => true, ),
        );
    }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();    
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

$app->run();