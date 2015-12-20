<?php
/**
 * Client user creates a new promote session.
 */
class Form_Client_Meeting_Create extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'client-meeting-create-form');

        
        // Name
        $this->addElement('text','meeting_title',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Title',
            'required' => true
        ));

        // Parent
        $this->addElement('text','meeting_startDate', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control date-picker-control',
            'label' => 'Start Date',
            'required' => true
        ));

        // Users to invite
        $this->addElement('textarea','meeting_invites',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control contact-picker-control',
            'label' => 'Invite People To Your Online Session',
            'rows' => 3,
            'required' => false
        ));

        // Users to invite
        $this->addElement('textarea','meeting_invitationMessage',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Invitation Message',
            'rows' => 5,
            'required' => false
        ));
    }

    /**
     * Set initial client data.
     *
     * @param Object_Client $client
     * @return boolean
     */
    public function setMeeting($meeting)
    {
        if (!is_a($meeting, 'Object_Meeting')) {
            return false;
        }

        $this->getElement('meeting_title')->setValue($meeting->title);
        $this->getElement('meeting_startDate')->setValue($meeting->startDate);
    }
}
