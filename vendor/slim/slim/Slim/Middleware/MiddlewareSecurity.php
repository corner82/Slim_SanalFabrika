<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Slim\Middleware;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
 
 /**
  * Flash
  *
  * This is middleware for a Slim application that enables
  * Flash messaging between HTTP requests. This allows you
  * set Flash messages for the current request, for the next request,
  * or to retain messages from the previous request through to
  * the next request.
  *
  * @package    Slim
  * @author     Josh Lockhart
  * @since      1.6.0
  */
  class MiddlewareSecurity extends \Slim\Middleware\MiddlewareHMAC implements \Security\Forwarder\PrivateKeyNotFoundInterface,
                                                                \Security\Forwarder\PublicKeyRequiredInterface,
                                                                \Security\Forwarder\PublicKeyNotFoundInterface,
                                                                \Security\Forwarder\UserNotRegisteredInterface
{
    
      /**
     * determine if private key not found
     * @var boolean | null
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    protected $isPrivateKeyNotFoundRedirect = true;
    
    /**
     * determine if public key not found
     * @var boolean | null
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    protected $isPublicKeyNotFoundRedirect = true;
    
    /**
     * determine if user not registered
     * @var boolean | null
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    protected $isUserNotRegisteredRedirect = true;
      
    /**
     * Constructor
     * @param  array  $settings
     */
    public function __construct($settings = array())
    {
        parent::__construct();
    }
    
    /**
     * get if to redirect due to user not registered  process
     * @return boolean
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function getUserNotRegisteredRedirect() {
        return $this->isUserNotRegisteredRedirect;
    }
    
    /**
     * set if to redirect due to user not registered  process
     * @param boolean $boolean
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function setUserNotRegisteredRedirect($boolean = null) {
        $this->isUserNotRegisteredRedirect = $boolean;
    }
    
    /**
     * user not registered process is being evaluated here
     * inherit classes
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function userNotRegisteredRedirect() {
        if($this->app->isServicePkRequired && $this->isUserNotRegisteredRedirect) {
            $forwarder = new \Utill\Forwarder\UserNotRegisteredForwarder();
            $forwarder->redirect();
        } else {
            return true;
        }
    }
    
    /**
     * get if to redirect due to public key not found process
     * @return boolean 
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function getPublicKeyNotFoundRedirect() {
        return $this->isPublicKeyNotFoundRedirect;
    }
    
    /**
     * set if to redirect due to public key not found process
     * @param boolean | null $boolean 
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function setPublicKeyNotFoundRedirect($boolean = null) {
        $this->isPublicKeyNotFoundRedirect = $boolean;
    }

    /**
     * public key not found process is being evaluated here
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function publicKeyNotFoundRedirect() {
        if($this->app->isServicePkRequired && $this->isPublicKeyNotFoundRedirect) {
             $forwarder = new \Utill\Forwarder\PublicNotFoundForwarder();
             $forwarder->redirect();  
         } else {
             return true;
         }
    }

    /**
     * get if to redirect due to private key not found process
     * @return type
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function getPrivateKeyNotFoundRedirect() {
        return $this->isPrivateKeyNotFoundRedirect;
    }
    
    /**
     * set if to redirect due to private key not found process
     * @param boolean $boolean
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function setPrivateKeyNotFoundRedirect($boolean = null) {
        $this->isPrivateKeyNotFoundRedirect = $boolean;
    }
    
    /**
     * private key not found process is being evaluated here
     * @author Mustafa Zeynel Dağlı
     * @since version 0.3
     */
    public function privateKeyNotFoundRedirect() {
        if($this->app->isServicePkRequired && $this->isPrivateKeyNotFoundRedirect) {
            $forwarder = new \Utill\Forwarder\PrivateNotFoundForwarder();
            $forwarder->redirect();
        } else {
            return true;
        }
    }

    /**
      * set if public / private key controler to be worked
      * @return boolean
      * @author Mustafa Zeynel Dağlı
      * @since version 0.3
      */
    public function servicePkRequired() {
        if($this->app->isServicePkRequired == null) {
             $params = $this->getAppRequestParams();
             //print_r($params);
             if(substr(trim($params['url']),0,2) == 'pk') {
                $this->app->isServicePkRequired = true;
                return $this->app->isServicePkRequired ;
             }
             $this->app->isServicePkRequired = false;
             $this->app->isServicePkRequired;
         } else {
             return $this->app->isServicePkRequired;
         }
    }

    /**
     * Call
     */
    public function call()
    {
        $this->servicePkRequired();
        $params = $this->getAppRequestParams();
        $requestHeaderParams = $this->getRequestHeaderData();
        /**
         * controlling public key if public key is necessary for this service and
         * public key not found forwarder is in effect then making forward
         * @since version 0.3 06/01/2016
         */
        if((!isset($requestHeaderParams['X-Public']) || $requestHeaderParams['X-Public']==null) && ($this->app->isServicePkRequired) ) {
            $this->publicKeyNotFoundRedirect();
        }
        
        /**
         * getting public key if user registered    
         * @author Mustafa Zeynel Dağlı
         * @since 06/01/2016 version 0.3
         */
        if(isset($requestHeaderParams['X-Public']) &&  $this->app->isServicePkRequired) {
            $resultSet = $this->app->getBLLManager()->get('blLoginLogoutBLL')->pkIsThere(array('pk' => $requestHeaderParams['X-Public']));
            //print_r($resultSet);
            if(!isset($resultSet[0]['?column?'])) $this->userNotRegisteredRedirect();
        }
        
        /**
         * getting private key due to public key
         * @author Mustafa Zeynel Dağlı
         * @since 05/01/2016 version 0.3
         */
        if(isset($requestHeaderParams['X-Public']) && $this->app->isServicePkRequired) { 
            $resultSet = $this->app->getBLLManager()->get('blLoginLogoutBLL')->pkControl(array('pk'=>$requestHeaderParams['X-Public']));
            //print_r($resultSet);
            if($resultSet[0]['sf_private_key_value'] == null) $this->privateKeyNotFoundRedirect();
        }
        $this->next->call();
    }
}