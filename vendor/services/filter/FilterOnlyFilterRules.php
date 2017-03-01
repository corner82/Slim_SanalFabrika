<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Services\Filter;


/**
 * service manager layer for filter functions for custom html tags
 * @author Okan CIRAN
 * @version 13.02.2017
 */
class FilterOnlyFilterRules implements \Zend\ServiceManager\FactoryInterface {
    
    /**
     * service ceration via factory on zend service manager
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return boolean|\PDO
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        // Create a filter chain and filter for usage
        $filterChain = new \Zend\Filter\FilterChain();  
        $filterChain->attach(new \Zend\Filter\HtmlEntities(array('quotestyle' => ENT_NOQUOTES,'charset' => 'UTF-8',  'encoding' => 'UTF-8')))
                    ->attach(new \Zend\Filter\StripTags())
                    ->attach(new \Zend\Filter\StringTrim())
                    ->attach(new \Zend\Filter\StripNewlines())                    
                     ;
        $filterChain ->attach(new \Zend\Filter\PregReplace(array(
                        'pattern'     => array(
                            
                                             
                                               "/([^A-Za-z0-9])*(iframe)([^A-Za-z0-9])+/i",
                                               "/(SRC=)|(src =)|(src%3d)/i",
                                               "/(SRC=)|(src =)|(src%3d)/i",
                                               "/(href=)|(href =)|(href%3d)|(href)/i",
                                               "/SRC=/i",
                                               "/<EMBED/i",
                                               "/(#)|(%23)/",
                                         //      "/(\{)|(%7b)/",                                               
                                               "/(!--)|(&#33;&#95;&#95;)/",
                                               "/(<)[^A-Za-z0-9]*(img)/i",
                                               "/([^A-Za-z0-9](eval))|((eval)[^A-Za-z0-9]+)/i",  
                                               ),
                        'replacement' => '',
                    ), 200));
        return $filterChain;

    }

}
