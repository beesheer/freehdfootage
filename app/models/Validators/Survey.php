<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Validators_Survey extends Zend_Validate_Abstract {

    const MISSING_PAGE_ID = "missing_page_id";
    const MISSING_SURVEY_NAME = "missing_survey_name";
    const MISSING_SURVEY_TYPE_ID = "missing_survey_type_id";
    const MISSING_PASS_SCORE = "missing_pass_score";
    const MISSING_CE = "missing_ce";
    const MISSING_MAX_TIME = "missing_max_time";
    const MISSING_IS_RANDOM = "missing_is_random";
    const MISSING_RESPONSE_TYPE_ID = "missing_response_type_id";
    const PASS_SCORE_RANGE = "pass_score_range";
    const PAGE_ID_IS_NOT_NUMERIC = "page_id_is_not_numeric";
    const SURVEY_TYPE_ID_IS_NOT_NUMERIC = "survey_type_id_is_not_numeric";
    const MAX_TIME_IS_NOT_NUMERIC = "max_time_is_not_numeric";
    const RESPONSE_TYPE_ID_IS_NOT_NUMERIC = "response_type_id_is_not_numeric";

    protected $_messageTemplates = array(
        self::MISSING_PAGE_ID => "Please enter a pageId value (pageId)",
        self::MISSING_SURVEY_NAME => "Please enter a survey name (name)",
        self::MISSING_SURVEY_TYPE_ID => "Please enter a survey type id (surveyTypeId)",
        self::MISSING_PASS_SCORE => "Please enter a pass score (passScore)",
        self::PASS_SCORE_RANGE => "Passing score must be over 50 in increments of 5",
        self::MISSING_CE => "Please enter a ce (ce)",
        self::MISSING_MAX_TIME => "Please enter a max time (maxTime)",
        self::MISSING_IS_RANDOM => "Please enter a value for is random (isRandom)",
        self::MISSING_RESPONSE_TYPE_ID => "Please enter a value for response type id (responseTypeId)",
        self::PAGE_ID_IS_NOT_NUMERIC => "Please enter a numeric value for page id (pageId)",
        self::SURVEY_TYPE_ID_IS_NOT_NUMERIC => "Please enter a numeric value for survey type id (surveyTypeId)",
        self::MAX_TIME_IS_NOT_NUMERIC => "Please enter a numeric value for max time (maxTime)",
        self::RESPONSE_TYPE_ID_IS_NOT_NUMERIC => "Please enter a numeric value for response type id (responseTypeId)"
    );

    public function isValid($value) {
        if (!isset($value['pageId'])) {
            $this->_error(self::MISSING_PAGE_ID);
            return false;
        }
        if (isset($value['pageId']) && !is_numeric($value['pageId'])) {
            $this->_error(self::PAGE_ID_IS_NOT_NUMERIC);
            return false;
        }
        if (!isset($value['name']) || empty($value['name'])) {
            $this->_error(self::MISSING_SURVEY_NAME);
            return false;
        }
        if (!isset($value['surveyTypeId'])) {
            $this->_error(self::MISSING_SURVEY_TYPE_ID);
            return false;
        }
        if (isset($value['surveyTypeId']) && !is_numeric($value['surveyTypeId'])) {
            $this->_error(self::SURVEY_TYPE_ID_IS_NOT_NUMERIC);
            return false;
        }

        if (!isset($value['passScore'])) {
            $this->_error(self::MISSING_PASS_SCORE);
            return false;
        }
        if (!isset($value['ce'])) {
            $this->_error(self::MISSING_CE);
            return false;
        }
        if (!isset($value['maxTime'])) {
            $this->_error(self::MISSING_MAX_TIME);
            return false;
        }
        if (!isset($value['isRandom'])) {
            $this->_error(self::MISSING_IS_RANDOM);
            return false;
        }
        if (!isset($value['responseTypeId'])) {
            $this->_error(self::MISSING_RESPONSE_TYPE_ID);
            return false;
        }
        if (isset($value['passScore']) && ($value['passScore'] < 50 || $value['passScore'] > 90)) {
            $this->_error(self::PASS_SCORE_RANGE);
            return false;
        }
        if (isset($value['passScore']) && ($value['passScore'] != 50 && $value['passScore'] != 60 && $value['passScore'] != 70 && $value['passScore'] != 80 && $value['passScore'] != 90)) {
            $this->_error(self::PASS_SCORE_RANGE);
            return false;
        }
        return true;
    }

}
