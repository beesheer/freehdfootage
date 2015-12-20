<?php
/**
 * Manager class to handle student info during login, registration, quiz
 */

class Manager_Session_Student extends Manager_Abstract
{

    /*
     * track the request paramaters
     */
    public $hasLayout = false;

    public $hasQuiz = false;

    public $hasClient = false;

    /*
    Reference to the session object
    */
    public $studentSessionDetails;

    /**
     * The only available instance of Manager_Session_Student.
     *
     * @var Manager_Session_Student
     */
    protected static $_instance;

    /**
     * Returns an instance.
     *
     * Singleton pattern implementation.
     *
     * @return Manager_Session_Student
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
        $this->studentSessionDetails = new Zend_Session_Namespace('studentSessionDetails');
        $this->studentSessionDetails->setExpirationSeconds(86400);
    }

    /*
     * Set the url variables
     */
    public function saveRequestValues($requestObject,$view) {

        if( $requestObject->clientslug && $requestObject->titleslug ) {
            $this->hasLayout = true;
            if ( !isset( $this->studentSessionDetails->clientSlug ) ) {
                $this->studentSessionDetails->clientSlug = $requestObject->clientslug;
                $this->studentSessionDetails->titleSlug = $requestObject->titleslug;
                $this->studentSessionDetails->baseLayout = 'course' . DS . $this->studentSessionDetails->clientSlug . DS . $this->studentSessionDetails->titleSlug;
            }
            $view->clientslug = $this->studentSessionDetails->clientSlug;
            $view->titleslug = $this->studentSessionDetails->titleSlug;
        }
        if( $requestObject->clientid ) {
            $this->hasClient = true;
            if( !isset( $this->studentSessionDetails->clientId ) )  $this->studentSessionDetails->clientId = $requestObject->clientid;
            $view->clientid = $this->studentSessionDetails->clientId;
        }
        if( $requestObject->quizpageid ) {
            $this->hasQuiz = true;
            if( !isset( $this->studentSessionDetails->quizPageId ) )  $this->studentSessionDetails->quizPageId = $requestObject->quizpageid;
            $view->quizpageid = $this->studentSessionDetails->quizPageId;
        }
        if( $requestObject->cefeedbackpageid ) {
            if( !isset( $this->studentSessionDetails->ceFeedbackPageId ) )  $this->studentSessionDetails->ceFeedbackPageId = $requestObject->cefeedbackpageid;
            $view->cefeedbackpageid = $this->studentSessionDetails->ceFeedbackPageId;
        }
    }

    public function getStudentSession() {
        if( isset($this->studentSessionDetails->baseLayout) )   $this->hasLayout = true;
        if( isset($this->studentSessionDetails->clientId) )     $this->hasClient = true;
        if( isset($this->studentSessionDetails->quizPageId) )   $this->hasQuiz = true;
    }


    /*
     * test session object for property
     */

    protected function hasProperty($prop) {
        return property_exists( $this->studentSessionDetails, $prop);
    }

    /*
     * Set the logged in student id
     */
    public function setStudentId() {
        $this->studentSessionDetails->studentId = Zend_Auth::getInstance()->getIdentity()->id;
    }

    /*
     * Clear student id
     */
    public function clearStudentId() {
        unset( $this->studentSessionDetails->studentId );
    }

}