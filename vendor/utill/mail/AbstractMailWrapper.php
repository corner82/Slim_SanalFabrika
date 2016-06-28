<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Mail;


abstract class AbstractMailWrapper {

    /**
     * SMTP server connection port
     * TCP port to connect to
     * @var int 
     */
    protected $smtpServerPort = 587;



    /**
     * mail from user password
     * @var string | null
     */
    protected $fromMailUserPassword ;


    /**
     * mail from user name
     * @var type string | null
     */
    protected $fromUserName;
    
    /**
     * SMTP Server user 
     * @var type string | null
     */
    protected $smtpServerUser;
    
    /**
     * SMTP Server user password
     * @var type string | null
     */
    protected $smtpServerUserPassword;

    /**
     *  mail charset
     * @var string | null
     */
    protected $charset = 'UTF-8';
    
    /**
     * SMTP server host
     * @var string | null
     */
    protected $smtpServerHost;

    /**
     * SMTP server security protocol 
     * Enable TLS encryption, `ssl` also accepted
     * @var string | null
     */
    protected $smtpSecureProtocol = 'TLS';

    /**
     * mail message
     * @var string | null
     */
    protected $mailMessage;

    /**
     * PhpMailer subject
     * subject line
     * @var string | null
     */
    protected $subject;
    
    /**
     * set mail subject
     * @param string | null $Subject
     */
    public function setSubject($subject = null) {
        $this->subject = $subject;
    }

    /**
     * get mail subject
     * @return string | null
     */
    public function getSubject() {
        return $this->subject;
    }
    
    /**
     * set mail charset
     * @param string | null $charset
     */
    public function setCharset($charset = null) {
        $this->charset = $charset;
    }

    /**
     * get mail charset
     * @return string | null
     */
    public function getCharset() {
        return $this->charset;
    }
    
    /**
     * set SMTP server host
     * @param string | null $mailHost
     */
    public function setSMTPServerHost($smtpServerHost = null) {
        $this->smtpServerHost = $smtpServerHost;
    }

    /**
     * get SMTP server  host
     * @return string | null
     */
    public function getSMTPServerHost() {
        return $this->smtpServerHost;
    }
    
    /**
     * set mail from user name
     * @param string | null $user
     */
    public function setFromUserName($fromUserName = null) {
        $this->fromUserName = $fromUserName;
    }

    /**
     * get mail from user name
     * @return string | null
     */
    public function getFromUserName() {
        return $this->fromUserName;
    }
    
    /**
     * set mail from user password
     * @param string | null $password
     */
    public function setFromUserPassword($fromUserPassword = null) {
        $this->fromMailUserPassword = $fromUserPassword;
    }

    /**
     * mail from user password
     * @return string | null
     */
    public function getFromUserPassword() {
        return $this->fromMailUserPassword;
    }
    
    
    /**
     * set SMTP Server user
     * @param string | null $user
     */
    public function setSMTPServerUser($smtpServerUser = null) {
        $this->smtpServerUser = $smtpServerUser;
    }

    /**
     * get SMTP server user 
     * @return string | null
     */
    public function getSMTPServerUser() {
        return $this->smtpServerUser;
    }
    
    /**
     * set SMTP Server user password
     * @param string | null $smtpServerUserPassword
     */
    public function setSMTPServerUserPassword($smtpServerUserPassword = null) {
        $this->smtpServerUserPassword = $smtpServerUserPassword;
    }

    /**
     * get SMTP server user password 
     * @return string | null
     */
    public function getSMTPServerUserPassword() {
        return $this->smtpServerUserPassword;
    }
   
      /**
     * set SMTP server port
     * @param string | null $smtpServerPort
     */
    public function setSMTPServerPort($smtpServerPort = null) {
        $this->smtpServerPort = $smtpServerPort;
    }

    /**
     * get SMTP server port
     * @return int | null
     */
    public function getSMTPServerPort() {
        return $this->smtpServerPort;
    }


    /**
     * set mail message
     * @param string | null $message
     */
    public function setMessage($message = null) {
        $this->message = $message;
    }

    /**
     * get mail message
     * @return string | null
     */
    public function getMessage() {
        return $this->message;
    }
    
      /**
     * set SMTP server security protocol
     * @param string | null $smtpServerSecureProtocol
     */
    public function setSMTPServerSecureProtocol($smtpServerSecureProtocol = null) {
        $this->smtpSecureProtocol = $smtpServerSecureProtocol;
    }

    /**
     * get SMTP server secure protocol
     * @return int | null
     */
    public function getSMTPServerSecureProtocol() {
        return $this->smtpSecureProtocol;
    }

    

}
