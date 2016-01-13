<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Strip;

 class Strip extends AbstractStrip implements \Services\Filter\FilterChainInterface {
    
    public function __construct($params) {
        
        if(empty($params))throw new Exception('strip class constructor parametre hatası');
        
        
    }

    public function getFilterChain($name = null) {
        
    }

    public function setFilterChain(\Utill\Strip\Chain\AbstractStripChainer $filterChainer) {
        
    }

}

