<?php

namespace tdt4237\webapp;

use Symfony\Component\Config\Definition\Exception\Exception;

class Hash
{
    public function __construct()
    {
    }
    public static function make($plaintext, $salt)
    {
        return hash('sha256', $plaintext, $salt);
    }

    public function check($plaintext, $salt, $hash)
    {
        usleep(rand(0, 200));    //Microseconds
        return $this->make($plaintext, $salt) === $hash;
    }

}
