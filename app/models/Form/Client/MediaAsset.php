<?php
/**
 * Client media asset form.
 */
class Form_Client_MediaAsset extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'client-media-asset-form');

        $clients = Repo_Client::getInstance()->getSelectArray();

        // Name
        $this->addElement('text','name',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Name',
            'required' => true
        ));

        // Client
        $this->addElement('select','client', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Client',
            'required' => true
        ));

        $this->getElement('client')->addMultiOptions(
            $clients
        );

        // Description
        $this->addElement('textarea','description',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Description',
            'rows' => 3
        ));

        // Uploaded file
        $this->addElement('text', 'filepath', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Uploaded File',
            'description' => '<div id="filepath-image-preview"></div>'
        ));

        // Tags
        $this->addElement('text', 'tag', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Tags',
            'ng-model' => 'tagSelected',
            'typeahead' => 'tag for tag in getTags($viewValue)',
            'typeahead-loading' => 'loadingTags',
            'typeahead-on-select' => 'addTag($item, $model, $label)',
            'description' => '<i ng-show="loadingTags" class="glyphicon glyphicon-refresh"></i><div><span class="label label-info tag-item" ng-repeat="mediaTag in mediaTags">{{mediaTag.name}} <span class="glyphicon glyphicon-remove" ng-click="removeTag(mediaTag.name)"></span></li></ul>'
        ));

        // Set page values if a page is passed in.
        if (isset($this->extraParams['object']) && $object = $this->extraParams['object']) {
            $this->setObject($object);
        }
    }

    /**
     * Set initial data.
     *
     * @param Object_MediaAsset $object
     * @return boolean
     */
    public function setObject($object)
    {
        if (!is_a($object, 'Object_MediaAsset')) {
            return false;
        }

        $this->getElement('name')->setValue($object->name);
        $this->getElement('client')->setValue($object->client_id);
        $this->getElement('filepath')->setValue($object->file_path)->setDescription('<span class="btn btn-success fileinput-button"><span>Upload New File</span><input type="file" name="files[]" id="fileUpload"></span>');
        $this->getElement('description')->setValue($object->description);
    }

    /**
     * Update with the form data.
     *
     * @param Object_MediaAsset $object
     * @return boolean
     */
    public function updateObject($object)
    {
        if (!is_a($object, 'Object_MediaAsset')) {
            return false;
        }

        if ($object->file_path != $this->getValue('filepath')) {
            unlink($object->getFilePath());
            $object->file_path = $this->getValue('filepath');
        }
        $object->name = $this->getValue('name');
        $object->description = $this->getValue('description');
        $object->modified_datetime = date('Y-m-d H:i:s');
        return $object->save();
    }
}
