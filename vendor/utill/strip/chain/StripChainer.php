<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */
namespace Utill\Strip\Chain;

class StripChainer extends AbstractStripChainer implements \Services\Filter\FilterInterface {
    
    /**
     * value to be filtered
     * @var mixed any type
     */
    protected $filterValue;
    
    public function __construct($valueToFilter, $filters) {
        
        if(empty($valueToFilter)) throw new Exception ('filter edilecek value bulunamamıştır');
        
        if(empty($filters)) throw new Exception ('filter class name array boştur');
        
        foreach ($filters as $key =>$value) {
            
        }
    }

    public function getFilter($name = null) {
        
    }
    
    public function setFilter($params = null) {
        if(!$this->offsetExists($offset)) {
            $this->offsetSet($params['key'], $params['filter']);
            return true;
        }
        return false;
    }

    public function setFilterValue($value) {
        $this->filterValue = $value;
    }
    
     public function getFilterValue() {
        return $this->filterValue;
    }

}