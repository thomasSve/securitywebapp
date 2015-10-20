<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    
    private $validationErrors = [];
    private $userRepository;
    
    public function __construct($username, $password, $fullname, $address, $postcode)
    {
        return $this->validate($username, $password, $fullname, $address, $postcode);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }

    public function addValidationError($error) {
        $this->validationErrors[] = $error;
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $fullname, $address, $postcode)
    {
        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);

        if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            $this->validationErrors[] = "Password must be 8 characters including 1 uppercase, 1 lowercase and 1 number";
        }

        if(strlen($password) > 20) {
            $this->validationErrors[] = "Password can't exceed length of 20 characters";
        }

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != "4") {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        if (strlen($username) < 3) {
            $this->validationErrors[] = "Username must be atleast 3 characters";
        }

        if (strlen($username) > 50) {
            $this->validationErrors[] = "Username can't exceed length of 50 characters";
        }

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }
    }
}
