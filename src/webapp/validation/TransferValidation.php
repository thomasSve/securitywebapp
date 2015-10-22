<?php
/**
 * Created by PhpStorm.
 * User: thomas
 * Date: 10/20/15
 * Time: 10:28 AM
 */

namespace tdt4237\webapp\validation;
class TransferValidation{
    private $validationErrors = [];

    public function isGoodToGo()
    {
        return \count($this->validationErrors) === 0;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    public function validateNewCardnumber($cardnumber)
    {
        if(!$this->validateCardNumber($cardnumber)){
            $this->validationErrors[] = "Card number must contain of numbers and have a length between 13 and 19";
        }
    }
    public function validateTransfer($doctor, $postAuthor){
        print("Validating transfer");
        if(!$this->validateCardNumber($doctor->getCardNumber()) || !$this->validateCardNumber($postAuthor->getCardNumber())){
            $this->validationErrors[] = "User or doctor do not have a valid cardnumber registered, and a transaction could therefore not take place";
        }
    }
    private function validateCardNumber($cardnumber)
    {
        if (!is_numeric($cardnumber) or strlen($cardnumber) > 19 or strlen($cardnumber) < 13) {
            return false;
        }
        return true;
    }
}