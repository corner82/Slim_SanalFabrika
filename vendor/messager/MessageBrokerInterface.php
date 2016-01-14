<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Messager;

interface MessageBrokerInterface{
    public function compareFilteredValue($valuenew = null, $valueold = null, $filterName = null);
    public function compareValidatedValue($valuenew = null, $valueold = null, $validationName = null);
}

