<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */
namespace Utill\MQ\Factory;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @author Mustafa Zeynel Dağlı
 */
class FactoryServiceRestEntryLog implements \Zend\ServiceManager\FactoryInterface {
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $serviceLogMQ = new \Utill\MQ\restEntryMQ();
        $slimApp = $serviceLocator->get('slimApp');
        $serviceLogMQ->setChannelProperties(array('queue.name' => \Utill\MQ\abstractMQ::SERVICE_ENTRY_LOG_QUEUE_NAME));
        $message = new \Utill\MQ\MessageMQ\MQMessage();
        ;
        
       
        $message->setMessageBody(array('message' => 'Rest service has been used', 
                                       'time'  => date('l jS \of F Y h:i:s A'),
                                       'serial' => $slimApp->container['settings']['request.serial'],
                                       'ip' => \Utill\Env\serverVariables::getClientIp(),
                                       'url' => $slimApp->request()->getUrl(),
                                       'path' => $slimApp->request()->getPath(),
                                       'method' => $slimApp->request()->getMethod(),
                                       'params' => json_encode($slimApp->request()->params()),
                                       'logFormat' => $slimApp->container['settings']['restEntry.rabbitMQ.logging']));
        $message->setMessageProperties(array('delivery_mode' => 2,
                                             'content_type' => 'application/json'));
        $serviceLogMQ->setMessage($message->setMessage());
        $serviceLogMQ->basicPublish();
        return $serviceLogMQ;
    }

}

