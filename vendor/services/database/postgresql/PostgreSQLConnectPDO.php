<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Services\Database\Postgresql;


/**
 * service manager layer for database connection
 * @author Mustafa Zeynel Dağlı
 */
class PostgreSQLConnectPDO implements \Zend\ServiceManager\FactoryInterface {
    
    /**
     * service ceration via factory on zend service manager
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return boolean|\PDO
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        try {
            $pdo = new \PDO('pgsql:dbname=ecoman_01_10;host=88.249.18.205;',
                            'postgres',
                            '1q2w3e4r',
                            PostgreSQLConnectPDOConfig::getConfig());
            return $pdo;
        } catch (PDOException $e) {
            return false;
        } 


    }

}
