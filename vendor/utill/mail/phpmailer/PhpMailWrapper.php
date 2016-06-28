<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Utill\Mail\PhpMailer;

// require_once '../../../phpmailer/phpmailer/PhpMailerAutoload.php';

class PhpMailWrapper extends \Utill\Mail\AbstractMailWrapper implements \Utill\Mail\AuthMailInterface,
                                                            \Utill\Mail\InfoMailInterface{

    /**
     * PHPmailer obj
     * @var \PHPMailser
     */
    protected $mailObj;
    
                                                         
    /**
     *  enables SMTP debug information (for testing) 
     *  1 = errors and messages   
     *  2 = messages only                                               
     */
    protected $DebugMode = 1;


    public function __construct() {
        $this->mailObj = new \PHPMailer();
    }
    
    /**
     * set debug mode
     * @param int $debugMode
     */
    public function setDebugMode($debugMode = null) {
        $this->DebugMode = $debugMode;
    }

    /**
     * get debug mode
     * @return int
     */
    public function getDebugMode() {
        return $this->DebugMode;
    }

    /**
     * send auth mails
     * @param array $params
     */
    public function sendAuthMail(array $params = null) {
        
    }

    /**
     * send info mails
     * @param array $params
     */
    public function sendInfoMail(array $params = null) {
        $body  = ' ıı öö ğğ işş çç  <b>ŞŞŞŞ İİĞ ĞĞ !</b>';
        $body  = eregi_replace("[\]",'',$body);
        $this->mailObj->CharSet=  $this->getCharset();
        //$mail->headerLine($headers, $value);
        $this->mailObj->IsSMTP(); // telling the class to use SMTP 
        $this->mailObj->Host       = $this->getSMTPServerHost(); // SMTP server 
        $this->mailObj->SMTPDebug  = 1;                      // enables SMTP debug information (for testing) 
                                                    // 1 = errors and messages
                                                    // 2 = messages only
        $this->mailObj->SMTPAuth   = true;                  // enable SMTP authentication
        $this->mailObj->Host       = "mail.ostimteknoloji.com"; // sets the SMTP server
        //$mail->SMTPSecure = 'SSL';   
        $this->mailObj->SMTPSecure = 'TLS';   
        $this->mailObj->Port       = 587;                        // set the SMTP port for the GMAIL server
        $this->mailObj->Username   = "sanalfabrika@ostimteknoloji.com"; // SMTP account username
        $this->mailObj->Password   = "1q2w3e4r";             // SMTP account password
        $this->mailObj->SetFrom('sanalfabrika@ostimteknoloji.com', '11 deneme');
        //$mail->AddReplyTo("311corner82@gmail.com","8.  deneme");
        $this->mailObj->Subject    = "cc9 bık bık içerik değişti 11 deneme";

        //$mail->AltBody    = " ıı öö ğğ işş çç !"; // optional, comment out and test

        $this->mailObj->MsgHTML($body);
        $address = "311corner82@gmail.com";
        //$mail->addCC('bahram.metu@gmail.com');
        //$mail->addBCC('311corner82@gmail.com'); 
        $this->mailObj->AddAddress($address, "z cddccd ");
        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
        if(!$this->mailObj->Send()) {
        echo "Mailer Error: " . $this->mailObj->ErrorInfo;
        } else {
        echo "Message sent!";
        } 

    }

    public function sendInfoMailSMTP(array $params = null) {
        
        $body  = ' ıı öö ğğ işş çç  <b>ŞŞŞŞ İİĞ ĞĞ !</b>';
        $body  = eregi_replace("[\]",'',$body);
        $this->mailObj->CharSet='UTF-8';
        //$mail->headerLine($headers, $value);
        $this->mailObj->IsSMTP(); // telling the class to use SMTP 
        $this->mailObj->Host       = "mail.ostimteknoloji.com"; // SMTP server 
        $this->mailObj->SMTPDebug  = $this->getDebugMode(); // enables SMTP debug information (for testing) 
                                                    // 1 = errors and messages
                                                    // 2 = messages only
        $this->mailObj->SMTPAuth   = true;                  // enable SMTP authentication
        //$mail->SMTPSecure = 'SSL';   
        $this->mailObj->SMTPSecure = $this->getSMTPServerSecureProtocol();   
        $this->mailObj->Port       = $this->getSMTPServerPort();                        // set the SMTP port for the GMAIL server
        $this->mailObj->Username   = $this->getSMTPServerUser(); // SMTP account username
        $this->mailObj->Password   = $this->getSMTPServerUserPassword();             // SMTP account password
        $this->mailObj->SetFrom($this->getFromUserName(), 'bu mail php mail wrapper içinden atılmıştır');
        //$mail->AddReplyTo("311corner82@gmail.com","8.  deneme");
        $this->mailObj->Subject    = "cc9 bık bık içerik değişti 11 deneme";

        //$mail->AltBody    = " ıı öö ğğ işş çç !"; // optional, comment out and test

        $this->mailObj->MsgHTML($body);
        $address = "311corner82@gmail.com";
        //$mail->addCC('bahram.metu@gmail.com');
        //$mail->addBCC('311corner82@gmail.com'); 
        $this->mailObj->AddAddress($address, "z cddccd ");
        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
        if(!$this->mailObj->Send()) {
        echo "Mailer Error: " . $this->mailObj->ErrorInfo;
        } else {
        echo "Message sent!";
        }       
    }

}
