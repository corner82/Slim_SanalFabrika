<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
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
  class MiddlewareSecurity extends \Slim\Middleware\MiddlewareHMAC 
{
    
    /**
     * Constructor
     * @param  array  $settings
     */
    public function __construct($settings = array())
    {
        parent::__construct();
    }
    
   
    
    
    
    /**
     * Call
     */
    public function call()
    {
        //print_r('--middlewareHMAC call()--');
        //fopen('zeyn.txt');
        /*$this->evaluateExpireTime();
        $this->evaluateHash();
        $this->next->call();*/  
    }
    
   
    
    protected function calcExpireTime() {
        
    }
    
    
     
     /**
     * get info to calculate HMAC security measures
     * @author Mustafa Zeynel Dağlı
     */
    private function evaluateHash() {
        $this->getHmacObj();
        $this->hmacObj->setRequestParams($this->getAppRequestParams());
        $this->hmacObj->setPublicKey($this->getRequestHeaderData()['X-Public']);
        $this->hmacObj->setNonce($this->getRequestHeaderData()['X-Nonce']);
        // bu private key kısmı veri tabanından alınır hale gelecek
        $BLLLogLogout = $this->app->getBLLManager()->get('blLoginLogoutBLL');
        
        /**
         * private key due to public key,
         * if public key not found request redirected
         * @author Mustafa Zeynel Dağlı
         * @since 05/01/2016
         */
        $resultset = $BLLLogLogout->pkControl(array('pk'=>$this->getRequestHeaderData()['X-Public']));
        print_r($resultset);
        print_r($resultset[0]['sf_private_key_value']);
        $publicNotFoundForwarder = new \Utill\Forwarder\publicNotFoundForwarder();
        //if(empty($resultset[0])) $publicNotFoundForwarder->redirect();
        
        
        $this->hmacObj->setPrivateKey($resultset[0]['sf_private_key_value']);
        //$this->hmacObj->setPrivateKey('zze249c439ed7697df2a4b045d97d4b9b7e1854c3ff8dd668c779013653913572e');
        $this->hmacObj->makeHmac();
        //print_r($hmacObj->getHash()); 
        
        if($this->hmacObj->getHash() != $this->getRequestHeaderData()['X-Hash'])  {
            //print_r ('-----hash eşit değil----');
            $this->publishMessage();
            $hashNotMatchForwarder = new \Utill\Forwarder\hashNotMatchForwarder();
            $hashNotMatchForwarder->redirect();
            
        } else {
           //print_r ('-----hash eşit ----'); 
        }
    }

}