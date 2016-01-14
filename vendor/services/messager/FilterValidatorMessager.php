<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Services\Messager;

/**
 * service manager layer for error messager functions
 * @author Mustafa Zeynel Dağlı
 * @version 13/01/2016
 */
class FilterValidatorMessager implements \Zend\ServiceManager\FactoryInterface {
    
    /**
     * service ceration via factory on zend service manager
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return Utill\Strip\Strip
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        // Create a message broker for usage
        $messager = new \Messager\FilterValidatorMessager();
        return $messager;

    }

}