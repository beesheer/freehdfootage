<?php
/**
 * This class show error right after the form element
 *
 * @author: beesheer
 * @version: 0.1b
 */
class Form_Decorator_SimpleError extends Zend_Form_Decorator_Abstract {
    public function render($content) {
        $element = $this->getElement();
        $errors = $element->getMessages();
        if($errors) {
            //Show only the first error
            foreach($errors as $key=>$error) {
                return $content . ' <span class="errors">' . $error . '</span>';
            }
            //return $content . ' <span class="errors">' . implode('; ',$errors) . '</span>';

        } else {
            return $content;
        }
    }
}
