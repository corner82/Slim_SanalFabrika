<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */
namespace DAL\Factory\PDO;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @author Okan CIRAN
 * created date : 21.06.2015
 */
class SysOsbClustersFactory  implements \Zend\ServiceManager\FactoryInterface{
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $sysOsbClusters  = new \DAL\PDO\SysOsbClusters();
        $slimapp = $serviceLocator->get('slimapp');
        $sysOsbClusters -> setSlimApp($slimapp);
        return $sysOsbClusters;
      
    }
    
    
}