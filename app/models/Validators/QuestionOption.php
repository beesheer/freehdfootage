<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Validators_QuestionOption extends Zend_Validate_Abstract {

    const MISSING_QUESTION = "missing_question";
    const MISSING_OPTIONTYPE = "missing_optiontype";
    const MISSING_ORDER = "missing_order";
    const MISSING_NEXT_QUESTION = "missing_next_question";
    const MISSING_TEXT = "missing_text";
    const MISSING_IMAGE = "missing_image";
    const MISSING_VIDEO = "missing_video";
    const MISSING_PREFIX = "missing_prefix";

    protected $_messageTemplates = array(
        self::MISSING_QUESTION => 'Please enter a value for question (question)',
        self::MISSING_OPTIONTYPE => 'Please enter a value for option type (optionType)',
        self::MISSING_ORDER => 'Please enter a value for order (order)',
        self::MISSING_NEXT_QUESTION => 'Please enter a value for next question (nextQuestion)',
        self::MISSING_TEXT => 'Please enter a value for text (text)',
        self::MISSING_VIDEO => 'Please enter a value for video (video)',
        self::MISSING_IMAGE => 'Please enter a value for image (image)',
        self::MISSING_PREFIX => 'Please enter a value for prefix (prefix)'
    );

    public function isValid($value) {
        if (!isset($value['question'])) {
            $this->_error(self::MISSING_QUESTION);
            return false;
        }
        if (!isset($value['optionType'])) {
            $this->_error(self::MISSING_OPTIONTYPE);
            return false;
        }
        if (!isset($value['order'])) {
            $this->_error(self::MISSING_ORDER);
            return false;
        }
        if (!isset($value['nextQuestion'])) {
            $this->_error(self::MISSING_NEXT_QUESTION);
            return false;
        }
        if (!isset($value['text']) || empty($value['text'])) {
            $this->_error(self::MISSING_TEXT);
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
        if (!isset($value['prefix'])){
            $this->_error(self::MISSING_PREFIX);
            return false;
        }
        return true;
    }

}
