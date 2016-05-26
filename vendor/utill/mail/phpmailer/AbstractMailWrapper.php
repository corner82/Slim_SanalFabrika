<?php

/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Mail\PhpMailer;

require_once '/../../../phpmailer/phpmailer/PHPMailerAutoload.php';

abstract class AbstractMailWrapper {

    protected $parameters = array();
    protected $recipients = array();

    /**
     * PhpMailer connection server
     * Specify main and backup SMTP servers
     * 'smtp1.example.com;smtp2.example.com'
     * @var string 
     */
    protected $server = 'mail.ostimteknoloji.com';

    /**
     * PhpMailer connection port
     * TCP port to connect to
     * @var int 
     */
    protected $port = 587;

    /**
     * PhpMailer user
     * SMTP username
     * @var string
     */
    protected $user = 'sanalfabrika@ostimteknoloji.com';

    /**
     * PhpMailer connection password
     * SMTP password
     * @var string
     */
    protected $password = '1q2w3e4r';

    /**
     * PhpMailer setFrom
     * Set who the message is to be sent from
     * @var string
     */
    protected $setFrom = 'sanalfabrika@ostimteknoloji.com';

    /**
     * PhpMailer setFromName
     * Set who the message is to be sent name from
     * @var string
     */
    protected $setFromName = 'SanalFabrika';

    /**
     * PhpMailer mail charset
     * @var string | null
     */
    protected $charset = 'UTF-8';

    /**
     * PhpMailer smtp_auth 
     *  enable SMTP authentication =true
     * @var boolean | null
     */
    protected $smtp_auth = true;

    /**
     * PhpMailer smtp_debug 
     * enables SMTP debug information (for testing)           
      0 = off (for production use)
      1 = client messages
      2 = client and server messages
     * @var int | null
     */
    protected $smtp_debug = 2;

    /**
     * PhpMailer smtp_secure 
     * Enable TLS encryption, `ssl` also accepted
     * @var string | null
     */
    protected $smtp_secure = 'SSL';

    /**
     * PhpMailer debug_output 
     * Ask for HTML-friendly debug output
     * @var string | null
     */
    protected $debug_output = 'html';

    /**
     * PhpMailer attachment      
     * Add attachments
     * @var string | null
     */
    protected $attachment;

    /**
     * PhpMailer uploadfile      
     * Attach the uploaded file
     * @var string | null
     */
    protected $uploadfile;

    /**
     * PhpMailer message
     * @var string | null
     */
    protected $message;

    /**
     * PhpMailer subject
     * subject line
     * @var string | null
     */
    protected $Subject;
    
    
    /**
     * PhpMailer language  
     * setLanguage   
     * @var string | null
     */
    protected $language = 'tr';
    /**
     * PhpMailer pathToLanguageDirectory  
     * setLanguage   
     * @var string | null
     */
    protected $pathToLanguageDirectory = '/optional/path/to/language/directory/' ;
    
     

    /**
     * PhpMailer smtp_Custom_Options
     * Custom connection options
     * @var string | null
     */
    protected $smtp_Custom_Options = array(
        'ssl' => array(
            'verify_peer' => true,
            'verify_depth' => 3,
            'allow_self_signed' => true,
            'peer_name' => 'smtp.example.com',
            'cafile' => '/etc/ssl/ca_cert.pem',
        )
    );

    /**
     * mailServerConfig
     */
    abstract public function mailServerConfig($params = array());

    /**
     * sendAuthorizingMail
     */
    abstract public function sendAuthorizingMail($params = array());

    /**
     * sendUserProblemMail
     */
    abstract public function sendUserProblemMail($params = array());

    /**
     * to set mail server prameters
     * @param array | null $parameters
     * @author Okan CIRAN
     * @since version 0.1 25.05.2016
     */
    public function setParameters($parameters = array()) {
        $this->parameters = $parameters;
    }

    /**
     * to set mail server prameters
     * @param array | null $parameters
     * @author Okan CIRAN
     * @since version 0.1 25.05.2016
     */
    public function getParameters() {
       return $this->params;
    }

    /**
     * set PhpMailer connection server
     * @param string | null $server
     */
    public function setServer($server = null) {
        $this->server = $server;
    }

    /**
     * get PhpMailer connection server
     * @return string | null
     */
    public function getServer() {
        return $this->server;
    }
      /**
     * set PhpMailer $port
     * @param string | null $server
     */
    public function setPort($port = null) {
        $this->server = $port;
    }

    /**
     * get PhpMailer $port
     * @return string | null
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * set PhpMailer connection user
     * @param string | null $user
     */
    public function setUser($user = null) {
        $this->user = $user;
    }

    /**
     * get PhpMailer connection user
     * @return string | null
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * set PhpMailer connection password
     * @param string | null $password
     */
    public function setPassword($password = null) {
        $this->password = $password;
    }

    /**
     * get PhpMailer connection password
     * @return string | null
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * set PhpMailer connection password
     * @param string | null $password
     */
    public function setFrom($setFrom = null) {
        $this->setFrom = $setFrom;
    }

    /**
     * get PhpMailer connection password
     * @return string | null
     */
    public function getFrom() {
        return $this->setFrom;
    }

    /**
     * set PhpMailer setFromName
     * @param string | null $setFromName
     */
    public function setFromName($setFromName = null) {
        $this->setFromName = $setFromName;
    }

    /**
     * get PhpMailer FromName
     * @return string | null
     */
    public function getFromName() {
        return $this->setFromName;
    }

    /**
     * set PhpMailer charset
     * @param string | null $charset
     */
    public function setCharset($charset = null) {
        $this->charset = $charset;
    }

    /**
     * get PhpMailer charset
     * @return string | null
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * set PhpMailer smtp_auth
     * @param boolean | null $smtp_auth
     */
    public function setSmtpAuth($smtp_auth = null) {
        $this->smtp_auth = $smtp_auth;
    }

    /**
     * get PhpMailer smtp_auth
     * @return boolean | null
     */
    public function getSmtpAuth() {
        return $this->smtp_auth;
    }

    /**
     * set PhpMailer smtp_debug
     * @param int | null $smtp_debug
     */
    public function setSmtpDebug($smtp_debug = null) {
        $this->smtp_debug = $smtp_debug;
    }

    /**
     * get PhpMailer smtp_debug
     * @return int | null
     */
    public function getSmtpDebug() {
        return $this->smtp_debug;
    }

    /**
     * set PhpMailer smtp_secure
     * @param string | null $smtp_secure
     */
    public function setSmtpSecure($smtp_secure = null) {
        $this->smtp_secure = $smtp_secure;
    }

    /**
     * get PhpMailer smtp_secure
     * @return string | null
     */
    public function getSmtpSecure() {
        return $this->smtp_secure;
    }

    /**
     * set PhpMailer debug_output
     * @param string | null $debug_output
     */
    public function setDebugOutput($debug_output = null) {
        $this->debug_output = $debug_output;
    }

    /**
     * get PhpMailer debug_output
     * @return string | null
     */
    public function getDebugOutput() {
        return $this->debug_output;
    }

    /**
     * set PhpMailer attachment
     * @param string | null $attachment
     */
    public function setattachment($attachment = null) {
        $this->attachment = $attachment;
    }

    /**
     * get PhpMailer attachment
     * @return string | null
     */
    public function getAttachment() {
        return $this->attachment;
    }

    /**
     * set PhpMailer uploadfile
     * @param string | null $uploadfile
     */
    public function setUploadFile($uploadfile = null) {
        $this->uploadfile = $uploadfile;
    }

    /**
     * get PhpMailer uploadfile
     * @return string | null
     */
    public function getUploadFile() {
        return $this->uploadfile;
    }

    /**
     * set PhpMailer Subject
     * @param string | null $Subject
     */
    public function setSubject($Subject = null) {
        $this->Subject = $Subject;
    }

    /**
     * get PhpMailer Subject
     * @return string | null
     */
    public function getSubject() {
        return $this->Subject;
    }

    
    /**
     * set PhpMailer language
     * @param string | null $language
     */
    public function setLanguage($language = null) {
        $this->language = $language;
    }

    /**
     * get PhpMailer language
     * @return string | null
     */
    public function getLanguage() {
        return $this->language;
    }
    
     /**
     * set PhpMailer PathToLanguageDirectory
     * @param string | null $pathToLanguageDirectory
     */
    public function setPathToLanguageDirectory($pathToLanguageDirectory = null) {
        $this->language = $pathToLanguageDirectory;
    }

    /**
     * get PhpMailer PathToLanguageDirectory
     * @return string | null
     */
    public function getPathToLanguageDirectory() {
        return $this->pathToLanguageDirectory;
    }
    
    
    
    
    /**
     * set PhpMailer message object
     * @param string | null $message
     */
    public function setMessage($message = null) {
        $this->message = $message;
    }

    /**
     * get PhpMailer message object
     * @return string | null
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * set PhpMailer smtp_Custom_Options
     * @param array $smtp_Custom_Options
     */
    public function setSmtpCustomOptions(array $smtp_Custom_Options = array()) {
        $this->smtp_Custom_Options = array_merge($this->smtp_Custom_Options, $smtp_Custom_Options);
    }

    /**
     * set PhpMailer channel properties
     * @return array
     */
    public function getSmtpCustomOptions() {
        return $this->smtp_Custom_Options;
    }

    /**
     * kişiye email  göndermek  için hazırlandı.      
     * @version 0.1  26.05.2016
     */
    public function send_email(
                                $params = array(), 
                                $recipients = array(), 
                                $recipientsBcc = array(), 
                                $recipientsCc = array(), 
                                $attachment = array())    {
        print_r('send mail e geldik ');
        
        
        $mail = new Utill\Mail\PhpMailer;
      
        
        
      
        $valueHostAddress =$this ->getServer();
        $valuePort = AbstractMailWrapper::getPort();
        $valueUsername = AbstractMailWrapper::getUser();
        $valuePsswrd = AbstractMailWrapper::getPassword();
        $valueCharset = AbstractMailWrapper::getCharset();
        $valueSmtpAuth = AbstractMailWrapper::getSmtpAuth();
        $valueSmtpDebug = AbstractMailWrapper::getSmtpDebug();
        $valueSmtpSecure = AbstractMailWrapper::getSmtpSecure();
        $valueSetFrom = AbstractMailWrapper::getFrom();
        $valueSetFromName = AbstractMailWrapper::getFromName();

        if ((isset($params['language_code']) && $params['language_code'] != "")) {
            $valueLanguage = $params['language_code'];
            //$valueLanguage = $this->getLanguage();
            $mail->setLanguage($valueLanguage, $this->getPathToLanguageDirectory());
        }
        if ((isset($params['set_from']) && $params['set_from'] != "")) {
            $valueSetFrom = $params['set_from'];
        }
        if ((isset($params['set_from_name']) && $params['set_from_name'] != "")) {
            $valueSetFromName = $params['set_from_name'];
        } 

        $mail->CharSet = $valueCharset;
        $mail->IsSMTP();                    // telling the class to use SMTP 
        $mail->Host = $valueHostAddress;    //"mail.ostimteknoloji.com"; // SMTP server 
        $mail->SMTPDebug = $valueSmtpDebug; //2;                      // enables SMTP debug information (for testing) 
                                            // 1 = errors and messages
                                            // 2 = messages only
        $mail->SMTPAuth = $valueSmtpAuth;   //true;                  // enable SMTP authentication
        //$mail->Host = "mail.ostimteknoloji.com"; // sets the SMTP server
        $mail->SMTPSecure = $valueSmtpSecure; //'SSL';
        $mail->Port = $valuePort;             //587;                        // set the SMTP port for the GMAIL server
        $mail->Username = $valueUsername;     //"sanalfabrika@ostimteknoloji.com"; // SMTP account username
        $mail->Password = $valuePsswrd;       //"1q2w3e4r";             // SMTP account password
        $mail->SetFrom($valueSetFrom, $valueSetFromName); //  ('sanalfabrika@ostimteknoloji.com', '8 deneme');
        $mail->AddReplyTo("okan.ciran@ostimteknoloji.com", "SanalFabrika.com");
        $ValueSuject = "SanalFabrika";
        if ((isset($params['subject']) && $params['subject'] != "")) {
            $ValueSuject = $params['subject'];
        }
        $mail->Subject = $ValueSuject;
        $mail->MsgHTML($body);
        // $mail->msgHTML(file_get_contents('contents.html'), dirname('/../../phpmailer/phpmailer/examples'));
        
        if (isset($recipients)) {            
                foreach ($recipients as $email => $name) {
                    $mail->AddAddress($email, $name); 
                }
        }      
        if (isset($recipientsBcc)) {            
                foreach ($recipientsBcc as $emailBcc => $nameBcc) {
                    $mail->addCC($emailBcc, $nameBcc);                
                }
        }
        if (isset($recipientsCc)) {            
                foreach ($recipientsCc as $emailCc => $nameCc) {
                    $mail->addBCC($emailCc, $nameCc);                
                }
        }
        if (isset($attachment)) {            
                foreach ($attachment as $attachmentFile) {
                    $mail->AddAttachment($attachmentFile);                
                }
        }
      
        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            //  throw new \phpmailerException($mail->ErrorInfo);
        } else {
            echo "Message sent!";
        }
       
    }

}
