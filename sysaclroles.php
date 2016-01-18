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
 * @since 07-01-2016
 */
$app->get("/pkFillComboBoxMainRoles_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');

    //print_r('--****************get parent--' );  
    $resCombobox = $BLL->fillComboBoxMainRoles();
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
 * @since 07-01-2016
 */
$app->get("/pkFillComboBoxFullRoles_sysAclRoles/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');

    $resCombobox = $BLL->fillComboBoxFullRoles();

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
 * @since 07-01-2016
 */
$app->get("/pkFillGrid_sysAclRoles/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');


    $vSearchName = '';
    $stripper->offsetSet(array_search($_GET['url'], $_GET), new \Utill\Strip\Chain\StripChainer($app, $_GET['url'], array(
        \Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS,
    //    \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED,
    )));

    if (isset($_GET['search_name']) && $_GET['search_name'] != "") {
        //    print_r($_GET['search_name']);
        $stripper->offsetSet(array_search($_GET['search_name'], $_GET), new \Utill\Strip\Chain\StripChainer($app, $_GET['search_name'], array(
            \Services\Filter\FilterServiceNames::FILTER_DEFAULT,
            \Services\Filter\FilterServiceNames::FILTER_PARENTHESES,
            //  \Services\Filter\FilterServiceNames::FILTER_TONULL,
            \Services\Filter\FilterServiceNames::FILTER_LOWER_CASE,
            \Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED,
   //         \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED,
            \Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS,
            \Services\Filter\FilterServiceNames::FILTER_PREG_REPLACE,
        )));
    }

    $stripper->strip();
    $vUrl = $stripper->offsetGet(array_search($_GET['url'], $_GET))->getFilterValue();
    if (isset($_GET['search_name']) && $_GET['search_name'] != "") {
        $vSearchName = $stripper->offsetGet(array_search($_GET['search_name'], $_GET))->getFilterValue();
        print_r('>>>>'.$vSearchName.'<<<');
    }

    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];


    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'search_name' => $vSearchName,
        'pk' => $pk));
    //print_r($resDataGrid);

    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */
    $resTotalRowCount = $BLL->fillGridRowTotalCount(array('search_name' => $vSearchName));

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
 * @since 07-01-2016
 */
$app->get("/pkInsert_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');
    $errorcode = 0;
    $hatasayisi = 0;
    $hatasayisi1 = 0;
    $hatasayisi2 = 0;
    $hatasayisi3 = 0;
    ////******************Filters ******************//////////
    // Filters are called from service manager
   // $filterDefault = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_DEFAULT);
  //  $filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);
  //  $filterHTMLTagsAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
  //  $filterLowerCase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_LOWER_CASE);
 //   $filterPregReplace = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_PREG_REPLACE);
  //  $filterSQLReservedWords = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS);
 //   $filterRemoveText = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_REMOVE_TEXT);
  //  $filterRemoveNumber = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_REMOVE_NUMBER);
 //   $filterToNull = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_TONULL);
  //  $filterAlpha = new \Zend\I18n\Filter\Alnum(array('allowWhiteSpace' => true));

    ////******************Filters ******************//////////
    ////******************Validators ******************//////////   
    $validatorAlpha = new Zend\I18n\Validator\Alnum(array('allowWhiteSpace' => true));
    $validatorStringLength = new Zend\Validator\StringLength(array('min' => 3, 'max' => 20));
    $validatorNotEmptyString = new Zend\Validator\NotEmpty();


    $vName = $_GET['name'];
    $vIconClass = $_GET['icon_class'];
    $vStartDate = $_GET['start_date'];
    $vEndDate = $_GET['end_date'];
    $vParent = $_GET['parent'];
    $vUserId = $_GET['user_id'];
    $vDescription = $_GET['description'];
    $vRoot = $_GET['root'];
    $vName = $_GET['name']; //$filterDefault->filter($vName);



   // $filterSQLReservedWordsData = $vName . $vIconClass . $vStartDate . $vEndDate .  $vParent . $vUserId . $vDescription . $vRoot;

  //  $filterSQLReservedWordsData = $filterLowerCase->filter($filterSQLReservedWordsData);
  //  $filterSQLReservedWordsData1 = ($filterSQLReservedWords->filter($filterSQLReservedWordsData) );


    //print_r('xxxxxx'.$filterSQLReservedWordsData.'----');
    // print_r($filterSQLReservedWordsData1.'xxxx');
    // print_r( strlen($filterSQLReservedWordsData). 'dddd' . strlen ($filterSQLReservedWordsData1) ) ; 

  //  if (strlen($filterSQLReservedWordsData) != strlen($filterSQLReservedWordsData1)) {
  //      print_r('xxxxxx' . $filterSQLReservedWordsData . '----');
  //      $errorcode = 999;
  //  }



    $messageName = 'Role Adı ';
    $controlMessage = $messageName;
    // echo $filterAlpha->filter($vName);
    //echo $filterAlpha->filter("This....!!!As is (my) content: 123");
    //  if (  strlen($filterAlpha->filter($vName)) != strlen($vName)  ) {   
/*
    if (!$validatorAlpha->isValid($vName)) {
        $vName = $filterAlpha->filter($vName);
        $controlMessage = $controlMessage . ' içerisinde alfabetik olmayan değer var!!! // ';
        $errorcode = 3;
        $hatasayisi = $hatasayisi + 1;
    }

    if (!$validatorNotEmptyString->isValid($vName)) {
        $result = $validatorNotEmptyString->isValid($vName);
        $controlMessage = $controlMessage . '  Boş Değer //';
        $errorcode = 2;
        $hatasayisi = $hatasayisi + 1;
    }

    $validatorStringLength->setMessages(array('stringLengthTooShort' => $messageName . 'en az ' . $validatorStringLength->getMin() . ' karakter olmak zorunda...',
        'stringLengthTooLong' => $messageName . 'en fazla ' . $validatorStringLength->getMax() . ' karakter olmak zorunda...')
    );
    if (!$validatorStringLength->isValid($vName)) {
        $messages = $validatorStringLength->getMessages();
        $controlMessage = $controlMessage . current($messages);
        $errorcode = 1;
        $hatasayisi = $hatasayisi + 1;
    }

    //////////////////////////////////////////////////////////////////////////

    $vParent = trim($_GET['parent']);
    $messageName = 'Parent Değerinde ';
    $controlMessage1 = $messageName;

    if (!$validatorAlpha->isValid($vParent)) {
        $vParent = $filterAlpha->filter($vParent);
        $controlMessage1 = $controlMessage1 . ' içerisinde alfabetik olmayan değer var!!! // ';
        $errorcode = 6;
        $hatasayisi1 = $hatasayisi1 + 1;
    }

    if (!$validatorNotEmptyString->isValid($vParent)) {
        $result = $validatorNotEmptyString->isValid($vParent);
        $controlMessage1 = $controlMessage1 . '  Boş Değer //';
        $errorcode = 5;
        $hatasayisi1 = $hatasayisi1 + 1;
    }
    $validatorStringLength->setMin(1);
    $validatorStringLength->setMax(20);
    $validatorStringLength->setMessages(array('stringLengthTooShort' => $controlMessage1 . 'en az ' . $validatorStringLength->getMin() . ' karakter olmak zorunda...',
        'stringLengthTooLong' => $controlMessage1 . 'en fazla ' . $validatorStringLength->getMax() . ' karakter olmak zorunda...')
    );

    if (!$validatorStringLength->isValid($vParent)) {
        $messages = $validatorStringLength->getMessages();
        $controlMessage1 = $controlMessage1 . current($messages);
        $errorcode = 4;
        $hatasayisi1 = $hatasayisi1 + 1;
    }



    //////////////////////////////////////////////////////////////////////////

    $vRoot = trim($_GET['root']);
    $messageName = 'Root Değerinde ';
    $controlMessage2 = $messageName;

    if (!$validatorAlpha->isValid($vRoot)) {
        $vRoot = $filterAlpha->filter($vRoot);
        $controlMessage2 = $controlMessage2 . ' içerisinde alfabetik olmayan değer var!!! // ';
        $errorcode = 9;
        $hatasayisi2 = $hatasayisi2 + 1;
    }

    if (!$validatorNotEmptyString->isValid($vRoot)) {
        $result = $validatorNotEmptyString->isValid($vRoot);
        $controlMessage2 = $controlMessage2 . '  Boş Değer //';
        $errorcode = 8;
        $hatasayisi2 = $hatasayisi2 + 1;
    }
    $validatorStringLength->setMin(1);
    $validatorStringLength->setMax(20);
    $validatorStringLength->setMessages(array('stringLengthTooShort' => $controlMessage2 . 'en az ' . $validatorStringLength->getMin() . ' karakter olmak zorunda...',
        'stringLengthTooLong' => $controlMessage2 . 'en fazla ' . $validatorStringLength->getMax() . ' karakter olmak zorunda...')
    );

    if (!$validatorStringLength->isValid($vRoot)) {
        $messages = $validatorStringLength->getMessages();
        $controlMessage2 = $controlMessage2 . current($messages);
        $errorcode = 7;
        $hatasayisi2 = $hatasayisi2 + 1;
    }


    //////////////////////////////////////////////////////////////////////////


    $vUserId = trim($_GET['user_id']);
    $messageName = 'User Değerinde ';
    $controlMessage3 = $messageName;

    if (!$validatorAlpha->isValid($vUserId)) {
        $vUserId = $filterAlpha->filter($vUserId);
        $controlMessage3 = $controlMessage3 . ' içerisinde alfabetik olmayan değer var!!! // ';
        $errorcode = 12;
        $hatasayisi3 = $hatasayisi3 + 1;
    }

    if (!$validatorNotEmptyString->isValid($vUserId)) {
        $result = $validatorNotEmptyString->isValid($vUserId);
        $controlMessage3 = $controlMessage3 . '  Boş Değer //';
        $errorcode = 11;
        $hatasayisi3 = $hatasayisi3 + 1;
    }
    $validatorStringLength->setMin(1);
    $validatorStringLength->setMax(20);
    $validatorStringLength->setMessages(array('stringLengthTooShort' => $controlMessage2 . 'en az ' . $validatorStringLength->getMin() . ' karakter olmak zorunda...',
        'stringLengthTooLong' => $controlMessage2 . 'en fazla ' . $validatorStringLength->getMax() . ' karakter olmak zorunda...')
    );

    if (!$validatorStringLength->isValid($vUserId)) {
        $messages = $validatorStringLength->getMessages();
        $controlMessage3 = $controlMessage3 . current($messages);
        $errorcode = 10;
        $hatasayisi3 = $hatasayisi3 + 1;
    }


*/


/*
    if ($hatasayisi > 0)
        print_r($hatasayisi . ' adet hatanız var. ' . $controlMessage);
    if ($hatasayisi1 > 0)
        print_r($hatasayisi1 . ' adet hatanız var. ' . $controlMessage1);
    if ($hatasayisi2 > 0)
        print_r($hatasayisi2 . ' adet hatanız var. ' . $controlMessage2);
    if ($hatasayisi3 > 0)
        print_r($hatasayisi3 . ' adet hatanız var. ' . $controlMessage3);



    switch ($errorcode) {
        case 1:
            //  print_r($hatasayisi .' adet hatanız var. '. $controlMessage ) ;          
            break;
        case 2:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        case 3:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        case 4:
            //    print_r($hatasayisi .' adet hatanız var. '. $controlMessage1 ) ;          
            break;
        case 5:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage1 );
            break;
        case 6:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage1 );
            break;
        case 7:
            //  print_r($hatasayisi .' adet hatanız var. '. $controlMessage ) ;          
            break;
        case 8:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        case 9:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        case 10:
            //  print_r($hatasayisi .' adet hatanız var. '. $controlMessage ) ;          
            break;
        case 11:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        case 12:
            //    print_r($hatasayisi .' adet hatanız var. '.$controlMessage );
            break;
        default:

            break;
    }
*/

    //  $result =  $validatorStringLength->isValid($vName)  ;
    //  $validatorNotEmpty = Zend\Validator\NotEmpty::STRING;
    //  $result =  $validatorNotEmpty->isValid($vName)  ;
    // print_r($result) ;
    //$vName->addValidator(new Zend_Validate_NotEmpty());
    // $vName->setAllowEmpty(false);
    // print_r($vName) ;

   $vName = $_GET['name'];
    $vIconClass = $_GET['icon_class'];
    $vStartDate = $_GET['start_date'];
    $vEndDate = $_GET['end_date'];
    $vParent = $_GET['parent'];
    $vUserId = $_GET['user_id'];
    $vDescription = $_GET['description'];
    $vRoot = $_GET['root'];
    $vName =$_GET['name'];
    $errorcode = 0 ;
    if ($errorcode == 0) {
        $headerParams = $app->request()->headers();
        $vPk = $headerParams['X-Public'];
        //print_r($resDataMenu);
        //   print_r('---'.strlen($vName).'***');
        //default filters
        //   $vName = $filterToNull->filter(trim($vName));
        // print_r('///---'.strlen($vName).'***');
        ///  print_r ('empty degeri = *//--'.empty(' '). '--/**/*/');
        //  if  (empty($vName)) { print_r (empty($vName));}
        //  else { print_r (' empty deger 123  ');}
        //print_r('123') ; 
        // if (strlen($vName) > 0) {
        //  print_r('---s///'.strlen($vName).'***');
        //   $vName = urlencode  ($vName);
    /*    $vName = urldecode(trim($vName));
        $vName = $filterLowerCase->filter($vName);
        //  print_r('========'.$vName.'======') ; 
        $vName = $filterDefault->filter($vName);
        $vName = $filterHexadecimalAdvanced->filter($vName);
        $vName = $filterHTMLTagsAdvanced->filter($vName);
        $vName = $filterPregReplace->filter($vName);
        $vName = $filterSQLReservedWords->filter($vName);
        // $vName = $filterRemoveNumber->filter($vName);


        $vIconClass = urldecode(trim($vIconClass));
        $vIconClass = $filterLowerCase->filter($vIconClass);
        $vIconClass = $filterDefault->filter($vIconClass);
        $vIconClass = $filterHexadecimalAdvanced->filter($vIconClass);
        $vIconClass = $filterHTMLTagsAdvanced->filter($vIconClass);
        $vIconClass = $filterPregReplace->filter($vIconClass);
        $vIconClass = $filterSQLReservedWords->filter($vIconClass);
        // $vIconClass = $filterRemoveNumber->filter($vIconClass);

        $vStartDate = urldecode(trim($vStartDate));
        $vStartDate = $filterLowerCase->filter($vStartDate);
        $vStartDate = $filterDefault->filter($vStartDate);
        $vStartDate = $filterHexadecimalAdvanced->filter($vStartDate);
        $vStartDate = $filterHTMLTagsAdvanced->filter($vStartDate);
        $vStartDate = $filterPregReplace->filter($vStartDate);
        $vStartDate = $filterSQLReservedWords->filter($vStartDate);
        // $vStartDate = $filterRemoveNumber->filter($vIconClass);

        $vEndDate = urldecode(trim($vEndDate));
        $vEndDate = $filterLowerCase->filter($vEndDate);
        $vEndDate = $filterDefault->filter($vEndDate);
        $vEndDate = $filterHexadecimalAdvanced->filter($vEndDate);
        $vEndDate = $filterHTMLTagsAdvanced->filter($vEndDate);
        $vEndDate = $filterPregReplace->filter($vEndDate);
        $vEndDate = $filterSQLReservedWords->filter($vEndDate);
        // $vEndDate = $filterRemoveNumber->filter($vEndDate);


        $vParent = urldecode(trim($vParent));
        $vParent = $filterLowerCase->filter($vParent);
        $vParent = $filterDefault->filter($vParent);
        $vParent = $filterHexadecimalAdvanced->filter($vParent);
        $vParent = $filterHTMLTagsAdvanced->filter($vParent);
        $vParent = $filterPregReplace->filter($vParent);
        $vParent = $filterSQLReservedWords->filter($vParent);
        // $vParent = $filterRemoveNumber->filter($vParent);

        $vUserId = urldecode(trim($vUserId));
        $vUserId = $filterLowerCase->filter($vUserId);
        $vUserId = $filterDefault->filter($vUserId);
        $vUserId = $filterHexadecimalAdvanced->filter($vUserId);
        $vUserId = $filterHTMLTagsAdvanced->filter($vUserId);
        $vUserId = $filterPregReplace->filter($vUserId);
        $vUserId = $filterSQLReservedWords->filter($vUserId);
        // $vUserId = $filterRemoveNumber->filter($vUserId);

        $vDescription = urldecode(trim($vDescription));
        $vDescription = $filterLowerCase->filter($vDescription);
        $vDescription = $filterDefault->filter($vDescription);
        $vDescription = $filterHexadecimalAdvanced->filter($vDescription);
        $vDescription = $filterHTMLTagsAdvanced->filter($vDescription);
        $vDescription = $filterPregReplace->filter($vDescription);
        $vDescription = $filterSQLReservedWords->filter($vDescription);

        $vRoot = urldecode(trim($vRoot));
        $vRoot = $filterLowerCase->filter($vRoot);
        $vRoot = $filterDefault->filter($vRoot);
        $vRoot = $filterHexadecimalAdvanced->filter($vRoot);
        $vRoot = $filterHTMLTagsAdvanced->filter($vRoot);
        $vRoot = $filterPregReplace->filter($vRoot);
        $vRoot = $filterSQLReservedWords->filter($vRoot);
        // $vRoot = $filterRemoveNumber->filter($vRoot);
*/

        $resDataInsert = $BLL->insert(array('name' => $vName,
            'icon_class' => $vIconClass,
            'start_date' => $vStartDate,
            'end_date' => $vEndDate,
            'parent' => $vParent,
            'user_id' => $vUserId,
            'description' => $vDescription,
            'root' => $vRoot,
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
 * @since 07-01-2016
 */
$app->get("/pkUpdate_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');

    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->update($_GET['id'], array('name' => $_GET['name'],       
        'active' => $_GET['active'],       
        'user_id' => $_GET['user_id'],   
        'id' => $_GET['id'],
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
 * @since 13-01-2016
 */
$app->get("/pkUpdateChild_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->updateChild(array(
        'active' => $_GET['active'],
        'user_id' => $_GET['user_id'],
        'id' => $_GET['id'],
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
$app->get("/pkGetAll_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');


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
 * @since 07-01-2016
 */
$app->get("/pkDelete_sysAclRoles/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclRolesBLL');


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
