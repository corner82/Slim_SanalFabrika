<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Service\Manager;

/**
 * config class for zend service manager
 */
class config{
    
    /**
     * constructor
     */
    public function __construct() {
        
    }
    
    /**
     * config array for zend service manager config
     * @var array
     */
    protected $config= array(
        // Initial configuration with which to seed the ServiceManager.
        // Should be compatible with Zend\ServiceManager\Config.
         'service_manager' => array(
             'invokables' => array(
                 'test' => 'Utill\BLL\Test\Test'
             ),
             'factories' => [
                 'pgConnectFactory' => 'Services\Database\Postgresql\PostgreSQLConnectPDO',
                 'textBaseFilter' => 'Services\Filter\TextBaseFilter',
                 'textBaseFilterNotToLowerCase' => 'Services\Filter\TextBaseFilterNotToLowerCase',
                 'textBaseFilterWithSQLReservedWords' => 'Services\Filter\TextBaseFilterWithSQLReservedWords',
                 'filterSQLReservedWords' => 'Services\Filter\FilterSQLReservedWords',
                 'filterHTMLTagsAdvanced' => 'Services\Filter\FilterHTMLTagsAdvanced',
                 'filterHexadecimalBase' => 'Services\Filter\FilterHexadecimalBase',
                 'filterHexadecimalAdvanced' => 'Services\Filter\FilterHexadecimalAdvanced',
                 
             ],  

         ),
     );
    
    /**
     * return config array for zend service manager config
     * @return array | null
     * @author Mustafa Zeynel Dağlı
     */
    public function getConfig() {
        return $this->config['service_manager'];
    }

}


