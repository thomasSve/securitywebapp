<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\Post;

class PostValidation {

    private $validationErrors = [];

    public function __construct($author, $title, $content, $doctor, $user) {
        return $this->validate($author, $title, $content, $doctor, $user);
    }

    public function isGoodToGo()
    {
        return \count($this->validationErrors) ===0;
    }

    public function getValidationErrors()
    {
    return $this->validationErrors;
    }

    public function validate($author, $title, $content, $doctor, $user)
    {
        if ($author == null) {
            $this->validationErrors[] = "Author needed";

        }
        if ($title == null) {
            $this->validationErrors[] = "Title needed";
        }

        if ($content == null) {
            $this->validationErrors[] = "Text needed";
        }
        if($doctor == 1){
            $this->validateTransaction($user);
        }else if($doctor == null){
            $this->validationErrors[] = "Unanswered question: Ask doctor?";
        }

        return $this->validationErrors;
    }

    private function validateTransaction($user){
        print($user->getCardNumber());
        if($user->getCardNumber() == Null){
            $this->validationErrors[] = "You haven't registered a bankcard!";
        }
    }
}
