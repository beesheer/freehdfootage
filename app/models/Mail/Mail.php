<?php
/**
 * The actual mail class.
 */
class Mail_Mail extends Mail_Abstract
{
    public function __construct($template = 'simple_text', $setDefaultFrom = false)
    {
		parent::__construct($template,$setDefaultFrom);
        $config = Zend_Registry::getInstance()->config;
        // Always use smtp
        $this->_transport = new Zend_Mail_Transport_Smtp($config->smtp->host, $config->smtp->toArray());
        /*if (APPLICATION_ENV == 'development') {

        } else {
            $this->_transport = new Zend_Mail_Transport_Sendmail();
        }*/
    }
}
