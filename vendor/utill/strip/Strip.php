<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Strip;

use Iterator;
use ArrayAccess;
use Countable;

class Strip extends \Strip\AbstractStrip implements Iterator, Countable {
    
    const SQL_STRATEGY    = 'sql_strategy';
    const HEX_STRATEGY    = 'hex_strategy';
    const HEX_ADVANCED_STRATEGY  = 'hex_advanced_strategy';
    const CDATA_STRATEGY = 'cdata_strategy';
    const ALL_HTML_STRATEGY  = 'all_html_strategy';
    const BASE_STRATEGY  = 'base_strategy';
    
    private $stripStrategies;
    
    public function __construct() {
        //return new \ArrayIterator($this->stripStrategies);
    }

    public function current() {
        return current($this->stripStrategies);
    }

    public function key() {
        return key($this->stripStrategies);
    }

    public function next() {
        return next($this->stripStrategies);
    }

    public function rewind() {
        reset($this->stripStrategies);
    }

    public function valid() {
        return (current($this->stripStrategies) !== false);
    }

    public function count($mode = 'COUNT_NORMAL') {
        return count($this->stripStrategies);
    }

    public function stripStrategyExists($offset) {
        return (isset($this->stripStrategies[$offset]));
    }

    public function getStripStrategy($offset) {
        return (isset($this->stripStrategies[$offset])) ? $this->stripStrategies[$offset] : null;
    }

    public function setStripStrategy($offset,$value) {
        if($value instanceof \Strip\AbstractStripStrategy) {
            $this->stripStrategies[$offset] = $value;
        } else {
            throw new Exception('invalid strip strategy class!!');
        }
    }

    public function removeStripStrategy($offset) {
        unset($this->stripStrategies[$offset]);
        return $this;
    }
    
    public function strip($value=null) {
        $strippedText = $value;
        foreach ($this->stripStrategies as $key=>$strategy) {
            if($strategy instanceof \Strip\AbstractStripStrategy) {
                $strippedText = $strategy->strip($strippedText);
            }
        }
        return $strippedText;
    }

}

