<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */
namespace Messager;

/**
 * abstract error messager class
 * @author Mustafa Zeynel Dağlı
 * @since 14/01/2016
 */
abstract class AbstractMessager implements
                    \Messager\Filter\FilterMessagerInterface,
                    \Messager\Validator\ValidationMessagerInterface,
                    \Messager\MessageBrokerInterface{
    
    /**
     * filter operations message holder
     * @var string
     */
    protected $filterMessage;
    
    /**
     * validation operations holder
     * @var string
     */
    protected $validationMessage;

    /**
     * returns filter operations message
     * @return string
     */
    public function getFilterMessage() {
        return $this->filterMessage;
    }
    
    /**
     * set filter operations message
     * @param string $filterMessage
     */
    public function setFilterMessage($filterMessage = null) {
        $this->filterMessage = $filterMessage;
    }

    /**
     * add new message part to filter messager
     * @param string $filterMessage
     */
    public function addFilterMessage($filterMessage = null) {
        //print_r('zeynel');
        $this->filterMessage.=$filterMessage;
    }
    
    /**
     * add new message part to validation messager
     * @param mixed string | null $validationMessage
     */
    public function addValidationMessage($validationMessage = null) {
        $this->validationMessage.=$validationMessage;
    }

    /**
     * returns validation operations message
     * @return string
     */
    public function getValidationMessage() {
        return $this->validationMessage;
    }
    
    /**
     * set validation operations message
     * @param type $validationMessage
     */
    public function setValidationMessage($validationMessage = null) {
        $this->validationMessage = $validationMessage;
    }

    /**
     * compare filtered values and add filter message if necessary
     * @param mixed $valuenew
     * @param mixed $valueold
     * @param mixed $filterName
     */
    public function compareFilteredValue($valuenew = null, $valueold = null, $filterName = null) {
        if(strcmp($valuenew, $valueold)!=0) $this->addFilterMessage ('[:::]old->'.$valueold.'[:]new->'.$valuenew.'[:]filter->'.$filterName);
        //$this->addFilterMessage ('[:::]old->'.$valueold.'[:]new->'.$valuenew.'[:]filter->'.$filterName);
    }
    
    /**
     * compare validated values and add validation message if necessary
     * @param mixed $valuenew
     * @param mixed $valueold
     * @param mixed $validationName
     */
    public function compareValidatedValue($valuenew = null, $valueold = null, $validationName = null) {
        if(strcmp($valuenew, $valueold)!=0) $this->addValidationMessage ('[:::]old->'.$valueold.'[:]new->'.$valuenew.'[:]val.->'.$validationName);
    }

}

