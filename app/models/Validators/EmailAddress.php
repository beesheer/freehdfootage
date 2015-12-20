<?php
/**
 * @todo this class is not included yet, we need to support a global error message for our forms does not matter if is api or PL
 * @author Gheorghe
 *
 */
class Validators_EmailAddress extends Zend_Validate_EmailAddress 
{
   
    public function isValid($value) 
    {
       $valid = parent::isValid($value);
       if($valid == false)
       {
           $this->_error(self::INVALID);
           return false;
       }
       return  true;
    }
}
?>