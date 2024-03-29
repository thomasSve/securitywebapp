<?php

namespace tdt4237\webapp\models;

class User
{

    protected $userId = null;
    protected $username;
    protected $fullname;
    protected $address;
    protected $postcode;
    protected $hash;
    protected $salt;
    protected $email = null;
    protected $bio = 'Bio is empty.';
    protected $age;
    protected $isAdmin = 0;
    protected $isDoctor = 0;
    protected $balance = 0;
    protected $cardNumber;
    protected $cardNumberToDisplay;

    function __construct($username, $hash, $fullname, $address, $postcode, $salt)
    {
        $this->username = $username;
        $this->hash = $hash;
        $this->fullname = $fullname;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->salt = $salt;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

    }

    public function isAdmin()
    {
        return $this->isAdmin === '1';
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
        return $this;
    }

    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setIsDoctor($isDoctor)
    {
        $this->isDoctor = $isDoctor;
        return $this;
    }

    public function getIsDoctor()
    {
        return $this->isDoctor;
    }

    public function isDoctor()
    {
        return $this->isDoctor;
    }

    public function setBalance($balance){
        $this->balance = $balance;
        return $this;
    }

    public function getBalance(){
        return $this->balance;
    }

    public function changeBalance($value)
    {
        $this->balance += $value;
        return $this;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }
    public function setCardNumberToDisplay($cardNumber){
        $cardToDisplay = "";
        for($i = 0; $i < strlen($cardNumber); $i++){
            if((strlen($cardNumber) - $i) > 4){
                $cardToDisplay .= "*";
            }else{
                $cardToDisplay .= "$cardNumber[$i]";
            }
        }
        $this->cardNumberToDisplay = $cardToDisplay;
        return $this;
    }
    public function getCardNumberToDisplay(){
        return $this->cardNumberToDisplay;
    }
}
