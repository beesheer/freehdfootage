<?php
/**
 * Create video.
 */
class Form_Video extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'create-new-client-form');

        // Name
        $this->addElement('text','name',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Name',
            'required' => true
        ));

        // Name
        $this->addElement('textarea','description',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Description',
            'required' => false
        ));

        // Name
        $this->addElement('text','youtube',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Youtube ID',
            'required' => true
        ));

        // Name
        $this->addElement('submit','submit',array(
            'class' => 'btn btn-primary',
            'label' => 'Save'
        ));

        // Set page values if a page is passed in.
        if (isset($this->extraParams['object']) && $object = $this->extraParams['object']) {
            $this->setObject($object);
        }
    }

    /**
     * Set initial client data.
     *
     * @param Object_Client $client
     * @return boolean
     */
    public function setObject($object)
    {
        if (!is_a($client, 'Object_Video')) {
            return false;
        }

        $this->getElement('name')->setValue($object->name);
        $this->getElement('description')->setValue($object->description);
    }
}
