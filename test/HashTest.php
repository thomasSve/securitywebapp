<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace tdt4237\webapp;

/**
 * Description of HashTest
 *
 * @author tor
 */
class HashTest extends \PHPUnit_Framework_TestCase
{
    private $hash;
    
    function setUp()
    {
        $this->hash = new Hash;
    }
    
    public function testHash()
    {
        $password = 'qwerty';
        $salt = 'sdfnyfg783njdsflnsfd';
        $hash = $this->hash->make($password, $salt);
        
        $this->assertTrue($this->hash->check($password, $salt, $hash));
        
    }
}
