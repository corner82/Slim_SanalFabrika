<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Mail\PhpMailer;

require_once '../../../phpmailer/phpmailer/PhpMailerAutoload.php';

abstract  class AbstractMailWrapper{
    
      protected $parameters = array();
       
    /**
     * mailServerConfig
     */
    abstract public function mailServerConfig();
    
    /**
     * sendAuthorizingMail
     */
    abstract public function sendAuthorizingMail();
    /**
     * sendUserProblemMail
     */
    abstract public function sendUserProblemMail();
      
    /**
     * to set mail server prameters
     * @param array | null $parameters
     * @author Okan CIRAN
     * @since version 0.1 25.05.2016
     */
    public function setParameters($parameters = array()) {
        $this->parameters = $parameters;
    }
}
