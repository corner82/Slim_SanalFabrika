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
 * @since 17.05.2016
 */
$app->get("/pkFillManufacturerList_sysManufacturer/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory(); 
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');
    
    $componentType = 'ddslick';
    if (isset($_GET['component_type'])) {
        $componentType = strtolower(trim($_GET['component_type']));
    }
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkFillManufacturerList_sysManufacturer" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
        
    $resCombobox = $BLL->FillManufacturerList(array(                                   
                                    'language_code' => $vLanguageCode,
                        ));    

    $flows = array();
 
    $flows[] = array("text" => "Lütfen Seçiniz", "value" => 0, "selected" => true, "imageSrc" => "", "description" => "Please Choose",); 
    foreach ($resCombobox as $flow) {
        $flows[] = array(            
            "text" => $flow["manufacturer_name"],
            "value" =>  intval($flow["id"]),
            "selected" => false,
            "description" => $flow["abbreviation_eng"],
           // "imageSrc"=>$flow["logo"],             
            "attributes" => array(  "abbreviation" => $flow["abbreviation"], 
                                    "active" => $flow["active"],                                                    
                                    
                ),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($flows));
});
 
/**
 *  * Okan CIRAN
 * @since 17.05.2016
 */
$app->get("/pkInsert_sysManufacturer/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');   
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkInsert_sysManufacturer" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];  
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }           
    $vManufacturerName = NULL;
    if (isset($_GET['manufacturer_name'])) {
         $stripper->offsetSet('manufacturer_name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['manufacturer_name']));
    } 
    $vDescription = NULL;
    if (isset($_GET['description'])) {
         $stripper->offsetSet('description',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description']));
    }   
    $vDescriptionEng = NULL;
    if (isset($_GET['description_eng'])) {
         $stripper->offsetSet('description_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description_eng']));
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
    $vAbbreviation = NULL;
    if (isset($_GET['abbreviation'])) {
         $stripper->offsetSet('abbreviation',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['abbreviation']));
    }  
    $vAbbreviationEng = NULL;
    if (isset($_GET['abbreviation_eng'])) {
         $stripper->offsetSet('abbreviation_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['abbreviation_eng']));
    }  
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }  
    if ($stripper->offsetExists('manufacturer_name')) {
        $vManufacturerName = $stripper->offsetGet('manufacturer_name')->getFilterValue();
    }
    if ($stripper->offsetExists('description')) {
        $vDescription = $stripper->offsetGet('description')->getFilterValue();
    }
    if ($stripper->offsetExists('description_eng')) {
        $vDescriptionEng = $stripper->offsetGet('description_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('about')) {
        $vAbout = $stripper->offsetGet('about')->getFilterValue();
    }
    if ($stripper->offsetExists('about_eng')) {
        $vAboutEng = $stripper->offsetGet('about_eng')->getFilterValue();
    }    
    if ($stripper->offsetExists('abbreviation')) {
        $vAbbreviation = $stripper->offsetGet('abbreviation')->getFilterValue();
    }
    if ($stripper->offsetExists('abbreviation_eng')) {
        $vAbbreviationEng = $stripper->offsetGet('abbreviation_eng')->getFilterValue();
    }
    
    $resData = $BLL->insert(array(  
            'language_code' => $vLanguageCode, 
            'manufacturer_name' => $vManufacturerName,
            'description'=> $vDescription, 
            'description_eng'=> $vDescriptionEng, 
            'about' => $vAbout,
            'about_eng' => $vAboutEng,
            'abbreviation' => $vAbbreviation,
            'abbreviation_eng' => $vAbbreviationEng,
            'pk' => $pk,
           
            ));

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
 
/**
 *  * Okan CIRAN
 * @since 17.05.2016
 */
$app->get("/pkUpdate_sysManufacturer/", function () use ($app ) {    
     $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');   
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkUpdate_sysManufacturer" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];  
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vId = NULL;
    if (isset($_GET['id'])) {
         $stripper->offsetSet('id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }  
    $vManufacturerName = NULL;
    if (isset($_GET['manufacturer_name'])) {
         $stripper->offsetSet('manufacturer_name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['manufacturer_name']));
    } 
    $vDescription = NULL;
    if (isset($_GET['description'])) {
         $stripper->offsetSet('description',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description']));
    }   
    $vDescriptionEng = NULL;
    if (isset($_GET['description_eng'])) {
         $stripper->offsetSet('description_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description_eng']));
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
    $vAbbreviation = NULL;
    if (isset($_GET['abbreviation'])) {
         $stripper->offsetSet('abbreviation',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['abbreviation']));
    }  
    $vAbbreviationEng = NULL;
    if (isset($_GET['abbreviation_eng'])) {
         $stripper->offsetSet('abbreviation_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['abbreviation_eng']));
    }  
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }  
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    } 
    if ($stripper->offsetExists('manufacturer_name')) {
        $vManufacturerName = $stripper->offsetGet('manufacturer_name')->getFilterValue();
    }
    if ($stripper->offsetExists('description')) {
        $vDescription = $stripper->offsetGet('description')->getFilterValue();
    }
    if ($stripper->offsetExists('description_eng')) {
        $vDescriptionEng = $stripper->offsetGet('description_eng')->getFilterValue();
    }
    if ($stripper->offsetExists('about')) {
        $vAbout = $stripper->offsetGet('about')->getFilterValue();
    }
    if ($stripper->offsetExists('about_eng')) {
        $vAboutEng = $stripper->offsetGet('about_eng')->getFilterValue();
    }    
    if ($stripper->offsetExists('abbreviation')) {
        $vAbbreviation = $stripper->offsetGet('abbreviation')->getFilterValue();
    }
    if ($stripper->offsetExists('abbreviation_eng')) {
        $vAbbreviationEng = $stripper->offsetGet('abbreviation_eng')->getFilterValue();
    }
    
    $resData = $BLL->update(array(  
            'id' => $vId, 
            'language_code' => $vLanguageCode, 
            'manufacturer_name' => $vManufacturerName,
            'description'=> $vDescription, 
            'description_eng'=> $vDescriptionEng, 
            'about' => $vAbout,
            'about_eng' => $vAboutEng,
            'abbreviation' => $vAbbreviation,
            'abbreviation_eng' => $vAbbreviationEng,
            'pk' => $pk,
           
            ));

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
 
   
/**
 *  * Okan CIRAN
 * @since 17.05.2016
 */
$app->get("/pkFillGrid_sysManufacturer/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) 
        throw new Exception ('rest api "pkFillGrid_sysManufacturer" end point, X-Public variable not found');
    //$pk = $headerParams['X-Public'];      
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }    
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }  
    
    $resDataGrid = $BLL->fillGrid(array(              
            'language_code' => $vLanguageCode,
            ));
    
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array(              
            'language_code' => $vLanguageCode,
            ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],            
            'name' => $flow["name"],
            'name_eng'=> $flow["name_eng"],
            'link' => $flow["link"],
            'logo' => $flow["logo"],
            'abbreviation' => $flow["abbreviation"],
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

 /**x
 *  * Okan CIRAN
 * @since 17.05.2016
 */
$app->get("/pkUpdateMakeActiveOrPassive_sysManufacturer/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');
    if(!isset($headerParams['X-Public'])) 
       throw new Exception ('rest api "pkUpdateMakeActiveOrPassive_sysManufacturer" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];          
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {$vId = $stripper->offsetGet('id')->getFilterValue(); }
    $resData = $BLL->makeActiveOrPassive(array(                  
            'id' => $vId ,    
            'pk' => $pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 

/**x
 *  * Okan CIRAN
 * @since 17.05.2016
 */
$app->get("/pkDelete_sysManufacturer/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('sysManufacturerBLL');   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];  
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 
    $stripper->strip(); 
    if ($stripper->offsetExists('id')) 
        {$vId = $stripper->offsetGet('id')->getFilterValue(); }  
        
    $resDataDeleted = $BLL->Delete(array(                  
            'id' => $vId ,    
            'pk' => $Pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataDeleted));
}
); 

$app->run();
