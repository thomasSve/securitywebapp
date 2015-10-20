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
        $this->validateCardNumber($cardnumber);
    }

    private function validateCardNumber($cardnumber)
    {
        if(! is_numeric($cardnumber) or strlen($cardnumber) > 19 or strlen($cardnumber) < 13){
            $this->validationErrors[] = "Card number must contain of numbers and have a length between 13 and 19";
        }
    }
    public function validateDeposit($amount){
        if(! is_numeric($amount) or $amount > 0){
            $this->validationErrors[] = "Amount to deposit must contain of numbers and be larger than 0";
        }
    }
}