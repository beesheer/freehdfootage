<?php
/**
 * Client user creates a new promote session.
 */
class Form_Client_Promote_Create extends Form_Abstract
{
    /**
     * Init function to set up form elements.
     *
     * @return void
     */
    public function init(){
        $this->setAttrib('id', 'client-promote-create-form');

        /**
         * User titles from user packages.
         *
         * @var Object_User
         */
        $user = isset($this->extraParams['user']) && is_a($this->extraParams['user'], 'Object_User') ? $this->extraParams['user'] : false;
        if (!$user) {
            throw new Zend_Exception('No valid user object is provided in client promote create form');
        }
        $packageOptions = $user->getPackageSelectOptions();

        // Session Subject
        $this->addElement('text','subject',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Subject',
            'required' => true
        ));

        // Select title
        $this->addElement('select','package', array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Select Package',
            'required' => true
        ));

        $this->getElement('package')->addMultiOptions(
            $packageOptions
        );

        // Users to invite
        $this->addElement('textarea','emails',array(
            'decorators' => $this->_standardElementDecorator,
            'class' => 'form-control',
            'label' => 'Invite Users',
            'description' => 'Enter emails to invite, one line per email',
            'rows' => 5,
            'required' => true
        ));
    }

    /**
     * Create session. Send out invite emails.
     *
     * @return array
     */
    public function createSession()
    {
        $subject = $this->getValue('subject');
        $packageId = $this->getValue('package');
        $userId = Zend_Auth::getInstance()->getIdentity()->id;
        $sessionId = Repo_PromoteSession::getInstance()->addNew($subject, $userId, $packageId);
        if ((int)$sessionId == 0) {
            throw new Zend_Exception('Promote session create failed');
        }
        $session = new Object_PromoteSession($sessionId);

        // Send out emails
        $emails = explode("\n", $this->getValue('emails'));
        $validate = new Zend_Validate_EmailAddress();
        $emailsSent = array();
        $t = Zend_Registry::getInstance()->translate;
        $emailBodyTemplate = $t->_('promote-invite-email-body');
        $emailSubject = $t->_('promote-invite-email-subject');
        $emailSubject = str_replace(
            array('{promoteSessionSubject}'),
            array($subject . ' ' . date('n/j/y G:i T')),
            $emailSubject
        );
        foreach ($emails as $_email) {
            $_sent = false;
            $_email = trim($_email);
            if (!$validate->isValid($_email)) {
                continue;
            }

            if (in_array($_email, $emailsSent)) {
                continue;
            }

            // Record invites
            $_inviteId = Repo_PromoteSessionInvite::getInstance()->addNew($session->getId(), $_email);
            if ((int)$_inviteId == 0) {
                continue;
            }
            $emailsSent[] = $_email;

            // Send Email
            $_invite = new Object_PromoteSessionInvite($_inviteId);
            $_emailAgent = new Mail_Mail();
            $_emailBody = str_replace(
                array('{promoteSessionLink}'),
                array(Functions_Common::hostUrl() . '/promote/viewer/key/' . $_invite->invite_key),
                $emailBodyTemplate
            );
            $_emailAgent->setBody($_emailBody)
                ->setSubject($emailSubject)
                ->setTo($_email);
            try {
                $_emailAgent->send();
                $_sent = true;
            } catch (Exception $e) {
                // Ignore for now
            }

            if (!$_sent) {
                continue;
            }
        }

        // Send an email to presenter himself
        $this->sendSelfEmail($sessionId);

        return array(
            'session' => $session,
            'emails' => $emailsSent
        );
    }

    /**
     * Send an email to presenter himself.
     *
     * @param integer $sessionId
     * return boolean
     */
    public function sendSelfEmail($sessionId)
    {
        $session = new Object_PromoteSession($sessionId);
        if (!$session->getId()) {
            return false;
        }

        $userEmail = Zend_Auth::getInstance()->getIdentity()->email;
        $t = Zend_Registry::getInstance()->translate;
        $emailBody = $t->_('promote-self-email-body');
        $emailBody = str_replace(
            array('{promoteSessionPresenterLink}'),
            array(Functions_Common::hostUrl() . '/promote/presenter/session/' . $session->ukey),
            $emailBody
        );
        $emailSubject = $t->_('promote-self-email-subject');
        $emailSubject = str_replace(
            array('{promoteSessionSubject}'),
            array($this->getValue('subject') . ' ' . date('n/j/y G:i T')),
            $emailSubject
        );
        $emailAgent = new Mail_Mail();
        $emailAgent->setBody($emailBody)
            ->setSubject($emailSubject)
            ->setTo($userEmail);
        try {
            $emailAgent->send();
        } catch (Exception $e) {
            // Ignore for now
        }
        return true;
    }
}
