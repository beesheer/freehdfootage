<?php
/**
 * This class show image preview if an previous image source is set.
 *
 * @author: beesheer
 * @version: 0.1b
 */
class Form_Decorator_ImagePreview extends Zend_Form_Decorator_Abstract {
    const PREVIEW_MAX_WIDTH = 400;
    public function render($content) {
        $element = $this->getElement();
        $imageSource = $element->getAttrib('imageSource');
        if($imageSource) {
            return $content . '<div class="filePreviewBlock"><img class="previewImage" src="' . $imageSource . '" style="max-width: ' . self::PREVIEW_MAX_WIDTH . 'px;" /></div>';
        } else {
            return $content . '<div class="filePreviewBlock">Preview image not available yet.</div>';
        }
    }
}
