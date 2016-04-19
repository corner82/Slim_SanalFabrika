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
 * @since 09.02.2016
 */
$app->get("/pkInsert_infoFirmProfile/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
    
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];
    
   
 
    
  
    
  
    $vFoundationYear = $_GET['foundation_year'];
    $vBagkurSicilNo = $_GET['bagkur_sicil_no'];
    $vFirmNameEng = $_GET['firm_name_eng'];
    $vFirmNameShort = $_GET['firm_name_short']; 
    
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
    $vCountryId = 0;
    if (isset($_GET['country_id'])) {
        $stripper->offsetSet('country_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['country_id']));
    } 
    $vLogo = NULL;
    if (isset($_GET['logo'])) {
        $stripper->offsetSet('logo', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['logo']));
    }
    $vFirmName = NULL;
    if (isset($_GET['firm_name'])) {
        $stripper->offsetSet('firm_name', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['firm_name']));
    }
    $vWebAddress = NULL;
    if (isset($_GET['web_address'])) {
        $stripper->offsetSet('web_address', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['web_address']));
    }
    $vTaxOffice = NULL;
    if (isset($_GET['tax_office'])) {
        $stripper->offsetSet('tax_office', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['tax_office']));
    } 
    $vTaxNo = 0;
    if (isset($_GET['tax_no'])) {
        $stripper->offsetSet('tax_no', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['tax_no']));
    } 
    $vSgkSicilNo = NULL;
    if (isset($_GET['sgk_sicil_no'])) {
        $stripper->offsetSet('sgk_sicil_no', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['sgk_sicil_no']));
    } 
    $vOwnershipStatusId = NULL;
    if (isset($_GET['ownership_status_id'])) {
        $stripper->offsetSet('ownership_status_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['ownership_status_id']));
    } 
 
    
 
    
     
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     
    
    $resDataInsert = $BLL->insert(array(  
            
        //    'operation_type_id' => $fOperationTypeId,
        //    'active' => $fActive,        
        //    'act_parent_id' => $fActParentId,           
        //    'cons_allow_id' => $fConsAllowId, 
        //     'consultant_id'  => $fConsultantId,
        //    'consultant_confirm_type_id' => $fConsultantConfirmTypeId,
        //    'confirm_id' =>  $fConfirmId,
            'language_code' => $fLanguageCode,
            'firm_name' => $vFirmName ,             
            'profile_public' => $fProfilePublic,   
            'address_type_id' => $fAddressTypeId , 
            'address1' => $fAddress1 , 
            'address2' => $fAddress2 ,
            'postal_code' => $fPostalCode , 
            'country_id' => $fCountryId, 
            'city_id' => $fCityId ,
            'borough_id' => $fBoroughId ,
            'city_name' => $fCityName ,        
            'description' => $fDescription ,
            'description_eng' => $fDescriptionEng,  
            'logo' => $vLogo,
            'pk' => $pk,        
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
 * @since 09.02.2016
 */
$app->get("/pkUpdate_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $headerParams = $app->request()->headers();
    $vpk = $headerParams['X-Public'];    
    $vID =$_GET['id'];    
    $vProfilePublic = $_GET['profile_public'];    
    $vLanguageCode = $_GET['language_code'];   
    
    $vAddressTypeId = $_GET['address_type_id'];
    $vAddress1 = $_GET['address1'];
    $vAddress2 = $_GET['address2'];
    $vPostalCode = $_GET['postal_code'];    
    $vCountryId = $_GET['country_id'];
    $vCityId = $_GET['city_id'];
    $vBoroughId = $_GET['borough_id'];
    $vCityName = $_GET['city_name'];  
    $vDescription = $_GET['description'];   
    $vDescriptionEng = $_GET['description_eng'];    
    
    
    $vActive =0; 
    if (isset($_GET['active'])) {
        $vActive = $_GET['active'];
    }
    $vOperationTypeId = 1;
    if (isset($_GET['operation_type_id'])) {
        $vOperationTypeId = $_GET['operation_type_id'];
    }
    $vUserId = NULL;
    if (isset($_GET['user_id'])) {
        $vUserId = $_GET['user_id'];
    } 
    
    $vConsAllowId = 0;
    if (isset($_GET['cons_allow_id'])) {
        $vConsAllowId = $_GET['cons_allow_id'];
    } 
    $vActParentId = 0;
    if (isset($_GET['act_parent_id'])) {
        $vActParentId = $_GET['act_parent_id'];
    }  
    $vConsultantId = 0;
    if (isset($_GET['consultant_id'])) {
        $vConsultantId = $_GET['consultant_id'];
    } 
    
    $vConsultantConfirmTypeId = 0;
    if (isset($_GET['consultant_confirm_type_id'])) {
        $vConsultantConfirmTypeId = $_GET['consultant_confirm_type_id'];
    } 
    
    $vConfirmId = 0;
    if (isset($_GET['confirm_id'])) {
        $vConsultantConfirmTypeId = $_GET['confirm_id'];
    } 
    $vLogo = 'logo';
    if (isset($_GET['logo'])) {
        $vLogo = strtolower(trim($_GET['logo']));
    }

     
    
      
    $fID = $vID;   
    $fUserId = $vUserId ; 
    $fOperationTypeId = $vOperationTypeId;    
    $fActive =$vActive;
    $fActParentId =$vActParentId;
    $fLanguageCode = $vLanguageCode;
    $fProfilePublic = $vProfilePublic;
 
    $fConsAllowId = $vConsAllowId ; 
    $fConsultantId = $vConsultantId;
    $fConsultantConfirmTypeId = $vConsultantConfirmTypeId;
    $fConfirmId = $vConfirmId ; 
    $fAddressTypeId = $vAddressTypeId;
    $fAddress1 =$vAddress1;
    $fAddress2 = $vAddress2;
    $fPostalCode = $vPostalCode;    
    $fCountryId = $vCountryId;
    $fCityId = $vCityId;
    $fBoroughId = $vBoroughId;
    $fCityName = $vCityName;  
    $fDescription = $vDescription;   
    $fDescriptionEng = $vDescriptionEng;    
    $fpk = $vpk ; 
    
  
    
    /*
     * filtre işlemleri
     */
    
    $resDataUpdate = $BLL->update(array(
        'id' =>$fID,  
        'user_id' =>  $fUserId , 
        'operation_type_id' => $fOperationTypeId,
        'active' => $fActive,        
        'act_parent_id' => $fActParentId,
        'language_code' => $fLanguageCode,
        'profile_public' => $fProfilePublic,              
        'cons_allow_id' => $fConsAllowId,  
        'consultant_id'  => $fConsultantId,
        'consultant_confirm_type_id' => $fConsultantConfirmTypeId,
        'confirm_id' =>  $fConfirmId,
        
        'address_type_id' => $fAddressTypeId , 
        'address1' => $fAddress1 , 
        'address2' => $fAddress2 ,
        'postal_code' => $fPostalCode , 
        'country_id' => $fCountryId, 
        'city_id' => $fCityId ,
        'borough_id' => $fBoroughId ,
        'city_name' => $fCityName ,        
        'description' => $fDescription ,
        'description_eng' => $fDescriptionEng ,  
        'logo' => $vLogo , 
        'pk' => $fpk,
         ));

    $app->response()->header("Content-Type", "application/json");


    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

/**
 *  * Okan CIRAN
 * @since 09.02.2016
 */
$app->get("/pkDeletedAct_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $headerParams = $app->request()->headers();
    $vpk = $headerParams['X-Public'];
    $vID =$_GET['id'];  
    $vActParentId = 0;
    if (isset($_GET['act_parent_id'])) {
        $vActParentId = $_GET['act_parent_id'];
    }  
    $vOperationTypeId = 3;
    if (isset($_GET['operation_type_id'])) {
        $vOperationTypeId = $_GET['operation_type_id'];
    }
    
    $fpk = $vpk ; 
    $fID = $vID ; 
    $fActParentId = $vActParentId ; 
    $fOperationTypeId = $vOperationTypeId ; 
    
    
    $resDataUpdate = $BLL->deletedAct(array(
        'id' => $fID,        
        'operation_type_id' => $fActParentId,
        'act_parent_id' => $fOperationTypeId,
        'pk' => $fpk));
 
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
$app->get("/pkGetAll_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    
    $fPk = $vPk ; 
    
    $resDataGrid = $BLL->getAll(array('pk' => $fPk));

    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_names" => $flow["operation_names"],
            "firm_names" => $flow["firm_names"],
            "web_address" => $flow["web_address"],
            "tax_office" => $flow["tax_office"],
            "tax_no" => $flow["tax_no"],
            "sgk_sicil_no" => $flow["sgk_sicil_no"],
            "ownership_status_id" => $flow["ownership_status_id"],
            "owner_ships" => $flow["owner_ships"],
            "foundation_year" => $flow["foundation_year"],
            "act_parent_id" => $flow["act_parent_id"],
            "language_code" => $flow["language_code"],
            "language_id" => $flow["language_id"],
            "language_names" => $flow["language_names"],
            "active" => $flow["active"],
            "state_actives" => $flow["state_actives"],
            "deleted" => $flow["deleted"],
            "state_deleteds" => $flow["state_deleteds"],
            "op_user_id" => $flow["op_user_id"],
            "username" => $flow["username"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alows" => $flow["auth_alows"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allows" => $flow["cons_allows"],
            "language_parent_id" => $flow["language_parent_id"],
            "firm_name_short" => $flow["firm_name_short"],
            "country_id" => $flow["country_id"],
            "country_names" => $flow["country_names"],
            "descriptions" => $flow["descriptions"],
            "duns_number" => $flow["duns_number"],
            "owner_user_id" => $flow["owner_user_id"],
            "owner_username" => $flow["owner_username"],
            "network_key" => $flow["network_key"],   
            "logo" => $flow["logo"],
            
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
 * @since 09.02.2016
 */
$app->get("/pkFillUserAddressesTypes_infoFirmProfile/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
    $fPk =$vPk ; 
    
    $vLanguageCode =$_GET['language_code'] ; 
    
    $resCombobox = $BLL->fillUserAddressesTypes(array('pk' => $fPk , 
                                                        'language_code' => $vLanguageCode ));

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'open',
            "checked" => false,
            "attributes" => array("notroot" => true,   ),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
 
  
/**x
 * Okan CIRAN
 * @since 02-02-2016
 */
$app->get("/pktempFillGridSingular_infoFirmProfile/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];
    $vLanguageCode =$_GET['language_code'] ; 
    
    $fPkTemp = $vPkTemp ; 
    
    $resDataGrid = $BLL->fillGridSingularTemp(array('pktemp' => $fPkTemp,
                                                    'language_code' => $vLanguageCode ));

    $resTotalRowCount = $BLL->fillGridSingularRowTotalCountTemp(array('pktemp' => $fPkTemp,
                                                                    'language_code' => $vLanguageCode ));

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
             "id" => $flow["id"],
            "profile_public" => $flow["profile_public"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_names" => $flow["operation_names"],
            "firm_names" => $flow["firm_names"],
            "web_address" => $flow["web_address"],
            "tax_office" => $flow["tax_office"],
            "tax_no" => $flow["tax_no"],
            "sgk_sicil_no" => $flow["sgk_sicil_no"],
            "ownership_status_id" => $flow["ownership_status_id"],
            "owner_ships" => $flow["owner_ships"],
            "foundation_year" => $flow["foundation_year"],
            "act_parent_id" => $flow["act_parent_id"],
            "language_code" => $flow["language_code"],
            "language_id" => $flow["language_id"],
            "language_names" => $flow["language_names"],
            "active" => $flow["active"],
            "state_actives" => $flow["state_actives"],
            "deleted" => $flow["deleted"],
            "state_deleteds" => $flow["state_deleteds"],
            "op_user_id" => $flow["op_user_id"],
            "username" => $flow["username"],
            "auth_allow_id" => $flow["auth_allow_id"],
            "auth_alows" => $flow["auth_alows"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allows" => $flow["cons_allows"],
            "language_parent_id" => $flow["language_parent_id"],
            "firm_name_short" => $flow["firm_name_short"],
            "country_id" => $flow["country_id"],
            "country_names" => $flow["country_names"],
            "descriptions" => $flow["descriptions"],
            "duns_number" => $flow["duns_number"],
            "owner_user_id" => $flow["owner_user_id"],
            "owner_username" => $flow["owner_username"],
            "network_key" => $flow["network_key"],
            "logo" => $flow["logo"],
            
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }
  
    $app->response()->header("Content-Type", "application/json");
   // print_r($resTotalRowCount);
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});

/**x
 *  * Okan CIRAN
 * @since 02-02-2016
 */
$app->get("/pktempInsert_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
   
    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];
   // print_r($vPkTemp);
    
    
   
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }     
    $vDescriptionEng = '';
    if (isset($_GET['description_eng'])) {
        $vDescriptionEng = strtolower(trim($_GET['description_eng']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $vProfilePublic = strtolower(trim($_GET['profile_public']));
    }  
    $vCountryId = 91;
    if (isset($_GET['country_id'])) {
        $vCountryId = $_GET['country_id'];
    }   
    $vOwnershipStatusId = 0;
    if (isset($_GET['ownership_status_id'])) {
        $vOwnershipStatusId = $_GET['ownership_status_id'];
    } 
    $vFirmNameShort = "";
    if (isset($_GET['firm_name_short'])) {
        $vFirmNameShort = $_GET['firm_name_short'];
    } 
    $vFoundationYear= "";
    if (isset($_GET['foundation_year'])) {
        $vFoundationYear = $_GET['foundation_year'];
    } 
    $vFoundationYearx= "";
    if (isset($_GET['foundation_yearx'])) {
        $vFoundationYearx = $_GET['foundation_yearx'];
    } 
    $vDunsNumber= "";
    if (isset($_GET['duns_number'])) {
        $vDunsNumber = $_GET['duns_number'];
    } 
    $vLogo  = 'logo';
    if (isset($_GET['logo'])) {
        $vLogo = strtolower(trim($_GET['logo']));
    }
    
    
    $vFirmName = $_GET['firm_name'];
    $vTaxOffice = $_GET['tax_office'];
    $vTaxNo = $_GET['tax_no'];
    $vSgkSicilNo = $_GET['sgk_sicil_no'];   
    $vDescription = $_GET['description'];   
    $vWebAddress = $_GET['web_address']; 
    
    
    $fLanguageCode = $vLanguageCode;
    $fProfilePublic = $vProfilePublic;   
    $fFirmName = $vFirmName;
    $fTaxOffice =$vTaxOffice;
    $fTaxNo = $vTaxNo;
    $fSgkSicilNo = $vSgkSicilNo;    
    $fCountryId = $vCountryId;
    $fOwnershipStatusId = $vOwnershipStatusId; 
    $fFoundationYear = $vFoundationYear;  
    $fDescription = $vDescription;   
    $fDescriptionEng = $vDescriptionEng;
    $fWebAddress = $vWebAddress ;    
    $fFirmNameShort=$vFirmNameShort;
    $fDunsNumber=$vDunsNumber;
   
    print_r($vFoundationYearx);
    $resDataInsert = $BLL->insertTemp(array(  
            'language_code' => $fLanguageCode,
            'profile_public' => $fProfilePublic,        
            'firm_name' => $fFirmName , 
            'tax_office' => $fTaxOffice , 
            'tax_no' => $fTaxNo ,
            'sgk_sicil_no' => $fSgkSicilNo , 
            'ownership_status_id' => $fOwnershipStatusId,
            'country_id' => $fCountryId, 
            'foundation_year' => $fFoundationYear ,      
            'foundation_yearx' => $vFoundationYearx,   
            'description' => $fDescription ,
            'description_eng' => $fDescriptionEng , 
            'web_address'=> $fWebAddress,
            'firm_name_short'=> $fFirmNameShort,
            'duns_number'=>$fDunsNumber,
            'logo'=>$vLogo,
            'pktemp' => $vPkTemp,        
            ));


    $app->response()->header("Content-Type", "application/json");
 
    $app->response()->body(json_encode($resDataInsert));
}
); 

/**x
 *  * Okan CIRAN
 * @since 02-02-2016
 */
$app->get("/pktempUpdate_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];  
    
    $vID =$_GET['id'];    
    $vProfilePublic = $_GET['profile_public'];    
    $vLanguageCode = $_GET['language_code'];
 
    $vAddressTypeId = $_GET['address_type_id'];
    $vAddress1 = $_GET['address1'];
    $vAddress2 = $_GET['address2'];
    $vPostalCode = $_GET['postal_code'];    
    $vCountryId = $_GET['country_id'];
    $vCityId = $_GET['city_id'];
    $vBoroughId = $_GET['borough_id'];
    $vCityName = $_GET['city_name'];  
    $vDescription = $_GET['description'];   
    $vDescriptionEng = $_GET['description_eng'];   
     
  
      
    $fID = $vID;   
    
    $fLanguageCode = $vLanguageCode;
    $fProfilePublic = $vProfilePublic;
   
    $fAddressTypeId = $vAddressTypeId;
    $fAddress1 =$vAddress1;
    $fAddress2 = $vAddress2;
    $fPostalCode = $vPostalCode;    
    $fCountryId = $vCountryId;
    $fCityId = $vCityId;
    $fBoroughId = $vBoroughId;
    $fCityName = $vCityName;  
    $fDescription = $vDescription;   
    $fDescriptionEng = $vDescriptionEng;
    $fPkTemp = $vPkTemp ; 
   
    
    $resDataUpdate = $BLL->updateTemp(array(
        'id' =>$fID,         
        'language_code' => $fLanguageCode,
        'profile_public' => $fProfilePublic, 
        'address_type_id' => $fAddressTypeId , 
        'address1' => $fAddress1 , 
        'address2' => $fAddress2 ,
        'postal_code' => $fPostalCode , 
        'country_id' => $fCountryId, 
        'city_id' => $fCityId ,
        'borough_id' => $fBoroughId ,
        'city_name' => $fCityName ,        
        'description' => $fDescription ,
        'description_eng' => $fDescriptionEng , 
        'logo' => $vLogo,
        'pktemp' => $fPkTemp,
         )); 
  
    $app->response()->header("Content-Type", "application/json");


    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

/**x
 *  * Okan CIRAN
 * @since 02-02-2016
 */
$app->get("/pktempDeletedAct_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];  
    
    $vID =$_GET['id'];  
    $vActParentId = 0;
    if (isset($_GET['act_parent_id'])) {
        $vActParentId = $_GET['act_parent_id'];
    }  
    $vOperationTypeId = 3;
    if (isset($_GET['operation_type_id'])) {
        $vOperationTypeId = $_GET['operation_type_id'];
    }
    
    $fPkTemp = $vPkTemp ; 
    $fID = $vID ; 
    $fActParentId = $vActParentId ; 
    $fOperationTypeId = $vOperationTypeId ; 
    
    
    $resDataUpdate = $BLL->deletedActTemp(array(
        'id' => $fID,        
        'operation_type_id' => $fActParentId,
        'act_parent_id' => $fOperationTypeId,
        'pktemp' => $fPkTemp));
 
    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});
 

/** x 
 *  * Okan CIRAN
 * @since 02-02-2016
 */
$app->get("/pktempFillUserAddressesTypes_infoFirmProfile/", function () use ($app ) {

 
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
    $headerParams = $app->request()->headers();
    $vPkTemp = $headerParams['X-Public-Temp'];   
    $vLanguageCode =$_GET['language_code'] ; 
    //print_r($vPkTemp) ;
   
    $resCombobox = $BLL->fillUserAddressesTypesTemp(array('pktemp' => $vPkTemp , 
                                                        'language_code' => $vLanguageCode ));

  //  print_r('123123123123');
  // print_r($resCombobox);
    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'open',
            "checked" => false,
            "attributes" => array("notroot" => true,   ),
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
$app->get("/fillCompanyListsGuest_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');

    $resDataGrid = $BLL->fillCompanyListsGuest(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        ));

    $resTotalRowCount = $BLL->fillCompanyListsGuestRtc( );

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "pk" => $flow["pk"],
            "firm_names" => $flow["firm_names"],
            "web_address" => $flow["web_address"],
            "firm_name_short" => $flow["firm_name_short"],
            "country_id" => $flow["country_id"],
            "country_names" => $flow["country_names"],
            "descriptions" => $flow["descriptions"],            
            "logo" => $flow["logo"], 
            "attributes" => array("notroot" => true, ),
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
 * @since 23-03-2016
 */
$app->get("/fillCompanyInfoEmployeesGuest_infoFirmProfile/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoEmployeesGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "firm_names" => $flow["firm_names"],
            "web_address" => $flow["web_address"],             
            "firm_name_short" => $flow["firm_name_short"],
            "foundation_year" => $flow["foundation_year"],  
            "country_id" => $flow["country_id"],
            "country_names" => $flow["country_names"],
            "descriptions" => $flow["descriptions"],            
            "logo" => $flow["logo"], 
            "number_of_employees" => $flow["number_of_employees"],
            "number_of_worker" => $flow["number_of_worker"], 
            "number_of_technician" => $flow["number_of_technician"], 
            "number_of_engineer" => $flow["number_of_engineer"], 
            "number_of_administrative_staff" => $flow["number_of_administrative_staff"], 
            "number_of_sales_staff" => $flow["number_of_sales_staff"], 
            "number_of_foreign_trade_staff" => $flow["number_of_foreign_trade_staff"],
            "about" => $flow["about"], 
            "attributes" => array("notroot" => true, ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});




    /**
 *  * Okan CIRAN
 * @since 23-03-2016
 */
$app->get("/fillCompanyInfoSocialediaGuest_infoFirmProfile/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoSocialediaGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "socialmedia" => $flow["socialmedia"],
            "firm_link" => $flow["firm_link"],      
            "attributes" => array("notroot" => true, ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});



    /**
 *  * Okan CIRAN
 * @since 23-03-2016
 */
$app->get("/fillCompanyInfoReferencesGuest_infoFirmProfile/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoReferencesGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "ref_name" => $flow["ref_name"],    
            "ref_date" => $flow["ref_date"],    
            "network_key" => $flow["ref_network_key"],    
            "logo" => $flow["logo"],   
            "web_address" => $flow["web_address"],   
            "attributes" => array("notroot" => true,"active" => $flow["active"], ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});


    /**
 *  * Okan CIRAN
 * @since 23-03-2016
 */
$app->get("/fillCompanyInfoCustomersGuest_infoFirmProfile/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoCustomersGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "customer_names" => $flow["customer_names"],            
            "attributes" => array("notroot" => true, ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});


    /**
 *  * Okan CIRAN
 * @since 15-04-2016
 */
$app->get("/fillCompanyInfoProductsGuest_infoFirmProfile/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoProductsGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "id" => $flow["id"],    
            "product_name" => $flow["product_name"],    
            "product_description" => $flow["product_description"],    
            "gtip_no_id" => $flow["gtip_no_id"],   
            "product_picture" => $flow["product_picture"],  
            "product_video_link" => $flow["product_video_link"],              
            "attributes" => array("notroot" => true,"active" => $flow["active"], ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});

 
    /**
 *  * Okan CIRAN
 * @since 15-04-2016
 */
$app->get("/fillCompanyInfoSectorsGuest_infoFirmProfile/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
 
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
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('npk')) $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
 
     $result = $BLL->fillCompanyInfoSectorsGuest(array('language_code' => $vLanguageCode,
        'network_key' => $vNetworkKey,        
        ));
    
  
    $flows = array();
    foreach ($result['resultSet'] as $flow) {
        $flows[] = array(
            "id" => $flow["id"],    
            "sector_name" => $flow["sector_name"],  
            "logo" => $flow["logo"],              
            "attributes" => array("notroot" => true,"active" => $flow["active"], ),
        );
    }
 
    $app->response()->header("Content-Type", "application/json");    
    $app->response()->body(json_encode($flows));
});



$app->run();
