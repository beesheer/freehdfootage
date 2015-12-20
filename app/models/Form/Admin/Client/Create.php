<?php
/**
 * Admin create client form.
 */
class Form_Admin_Client_Create extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'create-new-client-form');

        $clientTypes = Repo_ClientType::getInstance()->getSelectArray();

        $clientTree = Repo_Client::getInstance()->getClientTree();

        // Build up the flat option array
        Functions_Common::$tempCache = array();
        Functions_Common::flattenOptionTree($clientTree, Functions_Common::$tempCache, 'id', 'name', 'children', '');
        $clientTreeOptions = array(' ' => ' ') + Functions_Common::$tempCache;

        // Name
        $this->addElement('text','client_name',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Client Name',
            'required' => true
        ));

        // Parent
        $this->addElement('select','client_parent', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Parent Client',
            'required' => false
        ));

        $this->getElement('client_parent')->addMultiOptions(
            $clientTreeOptions
        );

        // Type
        $this->addElement('select','client_type', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Type',
            'required' => false
        ));

        $this->getElement('client_type')->addMultiOptions(
            $clientTypes
        );

        // ID
        $this->addElement('hidden','client_id',array(
            'decorators' => $this->_buttonElementDecorator,
            'required' => false
        ));
    }

    /**
     * Set initial client data.
     *
     * @param Object_Client $client
     * @return boolean
     */
    public function setClient($client)
    {
        if (!is_a($client, 'Object_Client')) {
            return false;
        }

        $this->getElement('client_id')->setValue($client->getId());
        $this->getElement('client_name')->setValue($client->name);
        $this->getElement('client_type')->setValue($client->type_id);
        $this->getElement('client_parent')->setValue($client->parent_id);
    }
}
