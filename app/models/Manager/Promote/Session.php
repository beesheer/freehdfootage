<?php
/**
 * Manager class to for a promote session / meeting.
 */
class Manager_Promote_Session extends Manager_Abstract
{
    /**
     * The only available instance of Manager_Promote_Session.
     *
     * @var Manager_Promote_Session
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Promote_Session
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Initialize some of the basic properties.
     *
     * @return void
     */
    protected function init()
    {

    }

    /**
     * Remove a contact from a session.
     *
     * @param mixed $sessionId
     * @param mixed $contactId
     * @return boolean
     */
    public function removeContact($sessionId, $contactId)
    {
        $row = Repo_PromoteSessionContact::getInstance()->getRow(array(
            array(
                'where' => 'promote_session_id = ?',
                'bind' => $sessionId
            ),
            array(
                'where' => 'contact_id = ?',
                'bind' => $contactId
            )
        ), true);
        $row->delete();

        // Send out an email
        $contact = new Object_Contact($contactId);
        $session = new Object_PromoteSession($sessionId);
        $emailAgent = new Mail_Mail();
        $emailAgent->setBody('The meeting ' . $session->subject . ' no longer exists.')
            ->setSubject($session->subject . ' no longer exists')
            ->setTo($contact->email);
        try {
            $emailAgent->send();
        } catch (Exception $e) {
            // Ignore for now
        }
        return true;
    }

    /**
     * Send out invitation emails to the contacts in the meeting.
     *
     * @param integer $sessionId
     * @return boolean
     */
    public function sendInvitationEmails($sessionId, $updated = false, $sendSelfEmail = true, $contactIds = false)
    {
        $session = new Object_PromoteSession($sessionId);
        if (!$session->getId()) {
            return false;
        }

        $t = Zend_Registry::getInstance()->translate;
        $emailBodyTemplate = $t->_('promote-invite-email-body');
        $emailSubject = $t->_('promote-invite-email-subject');
        $timestamp = substr($session->start_datetime, 0, 10);
        $emailSubject = str_replace(
            array('{promoteSessionSubject}'),
            array($session->subject . ' ' . date('n/j/y G:i T', $timestamp)),
            $emailSubject
        );

        if ($updated) {
            $emailSubject = 'UPDATED - ' . $emailSubject;
        }

        $contacts = Repo_PromoteSessionContact::getInstance()->getSessionContacts($session->getId(), $contactIds);

        foreach ($contacts as $_contact) {
            // Send Email
            $_emailAgent = new Mail_Mail();
            $_emailBody = str_replace(
                array(
                    '{promoteSessionLink}',
                    '{promoteSessionIcsLink}'
                ),
                array(
                    Functions_Common::hostUrl() . '/promote/viewer/key/' . $_contact->invite_key,
                    Functions_Common::hostUrl() . '/promote/ics/key/' . $_contact->invite_key
                ),
                $emailBodyTemplate
            );
            $_emailBody = '<p>' . nl2br($session->invite_message) . '</p>' . $_emailBody;
            $_emailAgent->setBody($_emailBody)
                ->setSubject($emailSubject)
                ->setTo($_contact->email);
            try {
                $_emailAgent->send();
            } catch (Exception $e) {
                // Ignore for now
            }
        }

        if ($sendSelfEmail) {
            // Send an email to presenter himself
            $this->sendSelfEmail($sessionId);
        }

        return true;
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

        $user = new Object_User($session->user_id);
        $t = Zend_Registry::getInstance()->translate;
        $emailBody = $t->_('promote-self-email-body');
        $emailBody = str_replace(
            array('{promoteSessionPresenterLink}'),
            array(Functions_Common::hostUrl() . '/promote/presenter/session/' . $session->ukey),
            $emailBody
        );
        $emailSubject = $t->_('promote-self-email-subject');
        $timestamp = substr($session->start_datetime, 0, 10);
        $emailSubject = str_replace(
            array('{promoteSessionSubject}'),
            array($session->subject . ' ' . date('n/j/y G:i T', $timestamp)),
            $emailSubject
        );
        $emailAgent = new Mail_Mail();
        $emailAgent->setBody($emailBody)
            ->setSubject($emailSubject)
            ->setTo($user->email);
        try {
            $emailAgent->send();
        } catch (Exception $e) {
            // Ignore for now
        }
        return true;
    }

    /**
     * Create an ics file.
     *
     */
    public function createIcsFile($session, $key)
    {
        $zone = date_default_timezone_set('UTC');
        $timestamp = DateTime::createFromFormat('Y-m-d H:i:s', $session->created_datetime);
        $start = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', substr($session->start_datetime, 0, 10)));
        $end = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', substr($session->start_datetime, 0, 10) + 30*60));
        $sessionLink = Functions_Common::hostUrl() . '/promote/viewer/key/' . $key;
        $sessionLinkContent = ' Join the meeting: ' . Functions_Common::hostUrl() . '/promote/viewer/key/' . $key;

        // Start part
        $fileContent = <<<ICS
BEGIN:VCALENDAR
CALSCALE:GREGORIAN
VERSION:2.0
PRODID:-//Lifelearn//Meetings//EN
BEGIN:VEVENT
UID:$key
DTSTAMP:{$timestamp->format('Y')}{$timestamp->format('m')}{$timestamp->format('d')}T{$timestamp->format('H')}{$timestamp->format('i')}{$timestamp->format('s')}Z
DTSTART:{$start->format('Y')}{$start->format('m')}{$start->format('d')}T{$start->format('H')}{$start->format('i')}{$start->format('s')}Z
DTEND:{$end->format('Y')}{$end->format('m')}{$end->format('d')}T{$end->format('H')}{$end->format('i')}{$end->format('s')}Z
SUMMARY:{$session->subject}
SEQUENCE:{$session->update_sequence}
ICS;
        // Add organizer
        $presenter = new Object_User($session->user_id);
        $fileContent .= "\n" . 'ORGANIZER;CN=' . $presenter->firstname . ' ' . $presenter->surname . ':MAILTO:' . $presenter->email . "\n";

        // Add attendees
        $contacts = Repo_PromoteSessionContact::getInstance()->getSessionContacts($session->id);
        foreach ($contacts as $_contact) {
            $fileContent .= 'ATTENDEE;CN=' . $_contact->firstname . ' ' . $_contact->surname . ':MAILTO:' . $_contact->email . "\n";
        }

        // End part
        $fileContent .= <<<ICS
DESCRIPTION: {$session->invite_message}{$sessionLinkContent}
URL:{$sessionLink}
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR
ICS;
        return $fileContent;
    }
}
