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
  * @author     Mustafa Zeynel Dağlı
  * @since      1.6.0 27/03/2016
  */
  class MiddlewareInsertUpdateDeleteLog extends \Slim\Middleware implements  
                                        \Slim\Interfaces\interfaceRequestCustomHeaderData,
                                        \Slim\Interfaces\interfaceRequest,
                                        \Slim\Interfaces\interfaceRequestParams

{
   
    /**
     * request header data
     * @var array
     */
    protected $requestHeaderData;
    
    /**
     * App request object
     * @var \Slim\Http\Request
     */
    protected $requestObj;
    
    /**
     * App request parameters
     * @var array
     */
    protected $appRequestParams = array();
    
    
    /**
     * Constructor
     * @param  array  $settings
     */
    public function __construct($settings = array())
    {
        
    }
    
    /**
     * get request params
     * @return array
     * @author Mustafa Zeynel Dağlı
     * @since 27/03/2016
     */
    public function getAppRequestParams() {
        if(empty($this->appRequestParams)) $this->appRequestParams = $this->setAppRequestParams();
        return $this->appRequestParams;
    }

    /**
     * 
     * @param array $appRequestParams
     * @return array
     * @author Mustafa Zeynel Dağlı
     * @since 27/03/2016
     */
    public function setAppRequestParams($appRequestParams = array()) {
        $requestHeaderData = [];
        $request = $this->app->container['request'];
        return $request->params();
        //return $this->app['request']->params();
    }
    
    /**
     * get Application request object
     * @return \Slim\Http\Request
     * @author Mustafa Zeynel Dağlı
     */
    public function getAppRequest() {
        if($this->requestObj == null) $this->requestObj = $this->setAppRequest();
        return $this->requestObj;
    }

    /**
     * set Application request object
     * @return \Slim\Http\Request
     * @author Mustafa Zeynel Dağlı
     */
    public function setAppRequest(\Slim\Http\Request $request = null) {
        return $this->app->container['request'];
        
    }
    
    
    
    /**
     * get request custom header info
     * @return array | null
     * @author Mustafa Zeynel Dağlı
     */
    public function getRequestHeaderData()  {
        if($this->requestHeaderData == null)   {
            $this->setRequestHeaderData();
            return $this->requestHeaderData;
        } else {
            return $this->requestHeaderData;
        }
    }
    
    /**
     * set request custom header info into array
     * @return array
     * @author Mustafa Zeynel Dağlı
     * @link http://php.net/manual/en/function.getallheaders.php
     */
    public function setRequestHeaderData($requestHeaderData = array())  {
        $requestObj = $this->getAppRequest();
        return $this->requestHeaderData = $requestObj->headers();
    }
    
    /**
     * Call
     */
    public function call()
    {
        $this->getRequestHeaderData();
        print_r($this->getAppRequestParams());
        //print_r('--middlewareInsertUpdateDeleteLog call()--');
        //print_r($this->requestHeaderData);
        /*print_r($this->requestHeaderData['X-Updateoperationlogged']);
        print_r($this->requestHeaderData['X-Insertoperationlogged']);
        print_r($this->requestHeaderData['X-Deleteoperationlogged']);*/
        
        $MQMAnager = $this->app->getMQManager();
        $serviceManager = $this->app->getServiceManager();
        if($this->requestHeaderData['X-Updateoperationlogged'] == 'true') {
          print_r('--testt--');
          $this->controlUrlParam('update');
        }
        
        if($this->requestHeaderData['X-Inserteoperationlogged'] == 'true') {
          print_r('--testt--');
          $this->controlUrlParam('insert');
        }
        
        if($this->requestHeaderData['X-Deleteeoperationlogged'] == 'true') {
          print_r('--testt--');
          $this->controlUrlParam('delete');
        }

        $this->next->call();
    }
    
    private function controlUrlParam($wordTosearch = null) {
        $requestParams = $this->getAppRequestParams();
        if(isset($requestParams['url'])) {
            $found = stripos($requestParams['url'], $wordTosearch);
            if($found !== false) {
                
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * message wrapper function
     * @param \Exception $e
     * @author Mustafa Zeynel Dağlı
     */
    public function publishMessage($e = null, array $params = array()) {
        $exceptionMQ = new \Utill\MQ\hashMacMQ();
        //print_r('---------'.$this->app->container['settings']['hmac.rabbitMQ.queue.name'].'------');
        $exceptionMQ->setChannelProperties(array('queue.name' => $this->app->container['settings']['hmac.rabbitMQ.queue.name']));
        $message = new \Utill\MQ\MessageMQ\MQMessage();
        ;
        //$message->setMessageBody(array('testmessage body' => 'test cevap'));
        //$message->setMessageBody($e);
       
        $message->setMessageBody(array('message' => 'Hash not matched', 
                                       'time'  => date('l jS \of F Y h:i:s A'),
                                       'serial' => $this->app->container['settings']['request.serial'],
                                       'ip' => \Utill\Env\serverVariables::getClientIp(),
                                       'logFormat' => $this->app->container['settings']['hmac.rabbitMQ.logging']));
        $message->setMessageProperties(array('delivery_mode' => 2,
                                             'content_type' => 'application/json'));
        $exceptionMQ->setMessage($message->setMessage());
        $exceptionMQ->basicPublish();
    }
    
   
    
   
 
    
   

   

 

}