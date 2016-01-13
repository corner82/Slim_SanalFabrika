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
    
    public function __construct($slimApp, $valueToFilter, $filters) {
        
        if(!$slimApp instanceof \Slim\Slim ) throw new Exception ('no slim app found in StripChainer');
        $this->setSlimApp($slimApp);
        
        if(empty($valueToFilter)) throw new Exception ('no value to filter in StripChainer class');
        $this->setFilterValue($valueToFilter);
        
        if(empty($filters)) throw new Exception ('iflter class name is empty in StripChainer class');
        
        foreach ($filters as $key =>$value) {
            $filter = $this->getSlimApp()->getServiceManager()->get($value);
            //print_r($filter);
            $this->setFilter(array($value => $filter));
        }
    }
    
    public function strip() {
        foreach ($this->chainer as $key => $value) {
          print_r('-key-'.$key.'--');
          //print_r('-filter-'.$value.'--');
          if(method_exists($value, 'filter')) { 
            $this->filterValue = $value->filter($this->filterValue);
            } else {
                throw new \Exception('invalid filter  method for \Zend\Filter\AbstractFilter');
            }
        }
        print_r('--value filtered-->'.$this->filterValue);
    }

    public function getFilter($name = null) {
        
    }
    
    public function setFilter($params = null) {
        print_r(key($params));
        $key = key($params);
        if(!$this->offsetExists($key)) {
            print_r('--test--');
            $this->offsetSet($key, $params[$key]);
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