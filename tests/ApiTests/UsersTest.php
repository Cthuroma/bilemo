<?php

namespace App\Tests\ApiTests;

use App\Tests\Helpers\Helpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersTest extends WebTestCase
{
    public function testGetUser()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/users/15', null, $token);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson(
            $return,
            json_encode(
                [
                    "name" => "User 15",
                    "mail" => "user15@mail.com",
                    "registered_at" => "2021-04-16 09:28:20"
                ]
            )
        );
    }

    public function testGetUsersNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request($client, 'GET', '/api/users/15', null, null);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }

    public function testGetUserNotExists()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/users/690', null, $token);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetUserNotBelonging()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/users/25', null, $token);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testPostUserNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request(
            $client,
            'POST',
            '/api/users',
            [
                "name" => "test",
                "mail" => "test@test.test",
                "registered_at" => "2021-04-15 15:00:00"
            ]
        );
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }

    public function testPostUser()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request(
            $client,
            'POST',
            '/api/users',
            [
                "name" => "test",
                "mail" => "test@test.test",
                "registered_at" => "2021-04-15 15:00:00"
            ],
            $token
        );
        $this->assertResponseStatusCodeSame(201);
        $this->assertJson(
            $return,
            json_encode(
                ["name" => "test", "mail" => "test@test.test", "registered_at" => "2021-04-15 15:00:00"]
            )
        );
    }

    public function testPostUserWrongData()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request(
            $client,
            'POST',
            '/api/users',
            ["name" => "test", "mail" => "test@test.test", "registered_at" => "azer"],
            $token
        );
        $this->assertResponseStatusCodeSame(400);
    }

    public function testPostUserNoData()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'POST', '/api/users', null, $token);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeleteUserNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request($client, 'DELETE', '/api/users/6', null, null);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }

    public function testDeleteUser()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'DELETE', '/api/users/19', null, $token);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteUserNotBelonging()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'DELETE', '/api/users/26', null, $token);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteUserNotExists()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'DELETE', '/api/users/286', null, $token);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testListUsers()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/users', null, $token);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson(
            $return,
            json_encode(
                [
                    'page' => 1,
                    'limit' => 10,
                    '_links' => [
                        'self' => [
                            'href' => '/api/users?page=1&limit=10',
                        ],
                        'first' => [
                            'href' => '/api/users?page=1&limit=10',
                        ],
                        'next' => [
                            'href' => '/api/users?page=2&limit=10',
                        ],
                    ],
                    '_embedded' => [
                        'items' => [
                            0 => [
                                'name' => 'User 1',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/1',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            1 => [
                                'name' => 'User 2',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/2',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            2 => [
                                'name' => 'User 3',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/3',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            3 => [
                                'name' => 'User 4',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/4',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            4 => [
                                'name' => 'User 5',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/5',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            5 => [
                                'name' => 'User 6',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/6',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            6 => [
                                'name' => 'User 7',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/7',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            7 => [
                                'name' => 'User 8',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/8',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            8 => [
                                'name' => 'User 9',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/9',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                            9 => [
                                'name' => 'User 10',
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/users/10',
                                    ],
                                    'all' => [
                                        'href' => '/api/users',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );
    }

    public function testListUsersNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request($client, 'GET', '/api/users', null, null);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }
}
