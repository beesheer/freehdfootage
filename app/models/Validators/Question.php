<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Validators_Question extends Zend_Validate_Abstract {

    const MISSING_SURVEY_ID = "missing_survey_id";
    const MISSING_ORDER_ID = "missing_order_id";
    const MISSING_TYPE_ID = "missing_type_id";
    const MISSING_CORRECT_ANSWER = "missing_correct_answer";
    const MISSING_RESPONSE_SCOPE = "missing_response_scope";
    const MISSING_QUESTION_ENGLISH = "missing_question_english";
    const MISSING_QUESTION_FRENCH = "missing_question_french";
    const MISSING_QUESTION_ID = "missing_question_id";
    const MISSING_IMAGE = "missing_image";
    const MISSING_VIDEO = "missing_video";

    protected $_messageTemplates = array(
        self::MISSING_SURVEY_ID => 'Please enter a value for surveyId (surveyId)',
        self::MISSING_ORDER_ID => 'Please enter a value for orderId (orderId)',
        self::MISSING_TYPE_ID => 'Please enter a value for typeId (typeId)',
        self::MISSING_CORRECT_ANSWER => 'Please enter a value for correct answer (correctAnswer)',
        self::MISSING_RESPONSE_SCOPE => 'Please enter a value for response scope (responseScope)',
        self::MISSING_QUESTION_ENGLISH => 'Please enter a value for the question in english (questionEnglish)',
        self::MISSING_QUESTION_FRENCH => 'Please enter a value for the question in french (questionFr)',
        self::MISSING_QUESTION_ID => 'Please enter a value for question id (questionId)',
        self::MISSING_IMAGE => 'Please enter a value for image (image)',
        self::MISSING_VIDEO => 'Please enter a value for video (video)'
    );

    public function isValid($value) {
        if (!isset($value['surveyId'])) {
            $this->_error(self::MISSING_SURVEY_ID);
            return false;
        }
        if (!isset($value['orderId'])) {
            $this->_error(self::MISSING_ORDER_ID);
            return false;
        }
        if (!isset($value['typeId'])) {
            $this->_error(self::MISSING_TYPE_ID);
            return false;
        }
        if (!isset($value['correctAnswer'])) {
            $this->_error(self::MISSING_CORRECT_ANSWER);
            return false;
        }
        if (!isset($value['responseScope'])) {
            $this->_error(self::MISSING_RESPONSE_SCOPE);
            return false;
        }
        if (!isset($value['questionEnglish']) || empty($value['questionEnglish'])) {
            $this->_error(self::MISSING_QUESTION_ENGLISH);
            return false;
        }
        if (!isset($value['questionFr'])) {
            $this->_error(self::MISSING_QUESTION_FRENCH);
            return false;
        }
        if (!isset($value['questionId'])) {
            $this->_error(self::MISSING_QUESTION_ID);
            return false;
        }
        if (!isset($value['image'])) {
            $this->_error(self::MISSING_IMAGE);
            return false;
        }
        if (!isset($value['video'])) {
            $this->_error(self::MISSING_VIDEO);
            return false;
        }
        return true;
    }

}
