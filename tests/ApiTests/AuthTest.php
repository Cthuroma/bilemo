<?php

namespace App\Tests\ApiTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helpers\Helpers;

class AuthTest extends WebTestCase
{
    public function testLoginGoodCreds()
    {
        $client = static::createClient();
        $return = Helpers::request($client,'POST','/api/login', ['name' => 'Client 1', 'password' => 'client1']);
        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('token', json_decode($return, true));
    }

    public function testLoginBadCreds()
    {
        $client = static::createClient();
        $return = Helpers::request($client,'POST','/api/login', ['name' => 'Client 1', 'password' => 'wrongpw']);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Wrong credentials"]), $return);
    }
}
