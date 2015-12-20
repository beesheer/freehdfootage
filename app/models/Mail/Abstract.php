<?php
class Mail_Abstract
{
    /**
     * emails
     *
     * @var array
     */
    public static $emails = array(
        'account' => 'noreply@lifelearn.com'
    );
    public $fromName = 'LifeLearn Meeting';
    protected $_mail = null;
    protected $_transport = null;
    protected $_emailLayout = null;
    protected $_message = '';
    protected $_fromType;
    protected $_toEmail;
    protected $_toName;
    protected $_subject;
    protected $_replyTo;

    /**
     * Mail object construct.
     *
     * @param string $fromType
     * @return Mail_Abstract
     */
    public function __construct($template = 'simple_text', $setDefaultFrom = false)
    {
        $this->_mail = new Zend_Mail('UTF-8');
        // set up template/layout
        $registry = Zend_Registry::getInstance();
        $this->_emailLayout = new Zend_Layout();
        $this->_emailLayout->setLayoutPath(APPLICATION_PATH . 'layouts' . DS . 'email' . DS);
        $this->_emailLayout->setLayout($template);

        if ($setDefaultFrom) {
            // Set up default from
            $configFrom = $registry->config->smtp->from;
            if (!$configFrom) {
                $configFrom = self::$emails['account'];
            }
            $this->setFrom($configFrom, $configFrom);
        }
    }

    /**
     * Get the mail content layout.
     *
     * @return Zend_View_Layout
     */
    public function getLayout()
    {
        return $this->_emailLayout;
    }

    /**
     * Set from type.
     *
     * @param string $fromType
     * @return Mail_Abstract
     */
    public function setFrom($fromName, $fromEmail)
    {
        $this->_mail->setFrom($fromEmail, $fromName);
        $this->_mail->setReplyTo($fromEmail, $fromName);
        return $this;
    }

     /**
     * Set cc.
     *
     * @param string $ccName
     * @param string $ccEmail
     * @return Mail_Abstract
     */
    public function setCc($ccName, $ccEmail)
    {
        $this->_mail->addCc($ccEmail, $ccName);
        return $this;
    }

    /**
     * Set mail body message.
     *
     * @param string $message
     * @return Mail_Abstract
     */
    public function setBody($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * Set to.
     *
     * @param string $toEmail
     * @param string $toName
     * @return Mail_Abstract
     */
    public function setTo($toEmail, $toName = false)
    {
        $this->_toEmail = $toEmail;
        $toName = $toName ? $toName : $toEmail;
        $this->_toName = $toName;
        $this->_mail->addTo($toEmail, $toName);
        $this->_emailLayout->toEmail = $toEmail;
        $this->_emailLayout->toName = $toName;
        return $this;
    }

    public function setToList($emailList = array()) {
    	if (count($emailList)) {
	    	foreach ($emailList as $toName => $toEmail) {
	    		if (!is_numeric($toName)) {
	    			$toName = $toEmail;
	    		}

		        $this->_mail->addTo($toEmail, $toName);
	    	}
    	}
    	return $this;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     * @return Mail_Abstract
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        $this->_mail->setSubject($subject);
        return $this;
    }

    /**
     * Send the mail.
     *
     * @throws Zend_Exception
     * @return void
     */
    public function send()
    {
        $this->_emailLayout->content = $this->_message;
        $this->_mail->setBodyHtml($this->_emailLayout->render());
        $this->_mail->setBodyText(strip_tags($this->_message));
        try {
            $this->_mail->send($this->_transport);
        } catch(Exception $e) {
            throw new Zend_Exception('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Add attachment.
     *
     * @param string $filePath
     * @param string $fileName
     * @return boolean
     */
    public function addAttachment($filePath, $fileName)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        $fileBinary = file_get_contents($filePath);
        if (!$fileBinary) {
            return false;
        }
        $at = new Zend_Mime_Part($fileBinary);
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding = Zend_Mime::ENCODING_BASE64;
        $at->filename = $fileName;
        return $this->_mail->addAttachment($at);
    }
}