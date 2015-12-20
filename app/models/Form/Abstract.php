<?php
/**
 * This is the basic form extends from Zend_Form
 *
 * @author beesheer
 * @version 1.0
 */
class Form_Abstract extends Zend_Form {
    const ALLOWED_IMAGE_EXTENSION = 'jpg,png,gif,jpeg';

    public $extraParams = array();

    //Define our own form element decorators
    protected $_standardElementDecorator = array(
        array('ViewHelper'),
        array('Label', array('class' => 'formElementLabel')),
        array('Description', array('placement'=>Zend_Form_Decorator_Abstract::APPEND, 'tag'=>'span', 'escape'=>false)),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'formElementBlock form-group'))
    );

    //Define multi checkboxes
    protected $_multiCheckboxDecorator = array(
        array('ViewHelper'),
        array('Label', array('class' => 'formElementLabel blockElement')),
        array('Description', array('placement'=>Zend_Form_Decorator_Abstract::APPEND, 'tag'=>'span', 'escape'=>false)),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'formElementBlock form-group multiCheckbox'))
    );

    //Define our own form element decorators
    protected $_standardRadioDecorator = array(
        array('ViewHelper'),
        array(array('AddDivSeparator' => 'HtmlTag'), array('tag' => 'div', 'class' => 'radioOptionBlock')),
        array('Label', array('tag'=>'div', 'class'=>'radioGroupLabelDiv')),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'radioGroup formElementBlock  form-group'))
    );

    //Define checkbox decorator
    protected $_standardCheckboxDecorator = array(
        array('ViewHelper'),
        array('Label', array('escape'=>false)),
        array('Description', array('escape'=>false, 'tag'=>'span')),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'checkboxDesc formElementBlock  form-group')),
    );


	//Define checkbox decorator: checkbox to left of label
	protected $_leftCheckboxDecorator = array(
		array('ViewHelper'),
		array('Label', array('escape'=>false, 'tag' => 'span', 'placement' => 'APPEND')),
		array('Description', array('escape'=>false, 'tag'=>'span')),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'checkboxDesc formElementBlock  form-group')),
	);

    //Define checkbox decorator: bootstrap checkbox
    protected $_bootstrapCheckboxDecorator = array(
        array('ViewHelper'),
        array('Label', array('escape'=>false, 'placement' => 'IMPLICIT_APPEND')),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'formElementBlock checkbox')),
    );

    //Define captcha decorator
    protected $_standardCaptchaDecorator = array(
        array('Label'),
        array('Description', array('placement'=>Zend_Form_Decorator_Abstract::APPEND, 'tag'=>'span')),
        array('SimpleError')
    );

    //Define the button decorators
    protected $_buttonElementDecorator = array(
        'ViewHelper',
    );

    //Define file decorators
    protected $_fileElementDecorator = array(
        array('File'),
        array('Label'),
        array('Description', array('placement'=>Zend_Form_Decorator_Abstract::APPEND, 'tag'=>'span', 'escape'=>false)),
        array('SimpleError'),
        array('ImagePreview'),
        array('HtmlTag', array('tag'=>'div', 'class' => 'formElementBlock fileBlock'))
    );

    //Define file decorators
    protected $_simpleFileElementDecorator = array(
        array('File'),
        array('Label', array('class' => 'formElementLabel')),
        array('Description', array('placement'=>Zend_Form_Decorator_Abstract::APPEND, 'tag'=>'span', 'escape'=>false)),
        array('SimpleError'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'formElementBlock form-group'))
    );

    protected $_submitElementDecorator = array(
        array('ViewHelper'),
        array('HtmlTag', array('tag'=>'div', 'class'=>'submitButtonDiv')),
    );

    //Define the hidden element decorators
    protected $_hiddenElementDecorator = array(
        'ViewHelper',
    );

    //Override the contruction function
    public function __construct($options = null, $extraParams = null) {
        //Save the extra parameters
        if($extraParams) {
            $this->extraParams = $extraParams;
            //var_dump($this->extraParams); die;
        }

        //Add the custom decorator path
        $this->addElementPrefixPath('Form_Decorator','Form/Decorator','decorator');

        // Add translate
        $this->setTranslator(Zend_Registry::getInstance()->translate);

        parent::__construct($options);
        //Add our custom initilization
        $this->setAttrib('accept-charset', 'UTF-8');
        $this->setDecorators(array(
            'FormElements',
            'Form',
        ));
    }

    /**
     * Loop through errors and build a message.
     *
     * @return string
     */
    public function getErrorMessageString()
    {
        $errors = $this->getMessages();
        $errorsArray = array();
        foreach ($errors as $_ele => $_message) {
            $_m = $_ele . ' - ';
            foreach ($_message as $_msg) {
                $_m .= $_msg . '; ';
            }
            $errorsArray[] = $_m;
        }
        return implode(' | ', $errorsArray);
    }
}
