<?php

namespace App\Tests\ApiTests;

use App\Tests\Helpers\Helpers;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsTest extends WebTestCase
{
    public function testListProductsNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request($client, 'GET', '/api/products', null, null);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }

    public function testListProducts()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/products', null, $token);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson(
            $return,
            json_encode(
                [
                    'page' => 1,
                    'limit' => 10,
                    'pages' => 2,
                    'total' => null,
                    '_links' => [
                        'self' => [
                            'href' => '/api/products?page=1&limit=10',
                        ],
                        'first' => [
                            'href' => '/api/products?page=1&limit=10',
                        ],
                        'last' => [
                            'href' => '/api/products?page=2&limit=10',
                        ],
                        'next' => [
                            'href' => '/api/products?page=2&limit=10',
                        ],
                    ],
                    '_embedded' => [
                        'items' => [
                            0 => [
                                'name' => 'Product 1',
                                'price' => 514.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/1',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            1 => [
                                'name' => 'Product 2',
                                'price' => 315.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/2',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            2 => [
                                'name' => 'Product 3',
                                'price' => 383.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/3',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            3 => [
                                'name' => 'Product 4',
                                'price' => 221.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/4',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            4 => [
                                'name' => 'Product 5',
                                'price' => 248.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/5',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            5 => [
                                'name' => 'Product 6',
                                'price' => 538.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/6',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            6 => [
                                'name' => 'Product 7',
                                'price' => 329.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/7',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            7 => [
                                'name' => 'Product 8',
                                'price' => 446.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/8',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            8 => [
                                'name' => 'Product 9',
                                'price' => 184.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/9',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                            9 => [
                                'name' => 'Product 10',
                                'price' => 504.0,
                                '_links' => [
                                    'self' => [
                                        'href' => '/api/products/10',
                                    ],
                                    'all' => [
                                        'href' => '/api/products',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );
    }

    public function testGetProductNotLoggedIn()
    {
        $client = static::createClient();
        $return = Helpers::request($client, 'GET', '/api/products/6', null, null);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJson(json_encode(['message' => "Authentication Required"]), $return);
    }

    public function testGetProduct()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/products/6', null, $token);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($return, json_encode([
                                                   'name' => 'Product 6',
                                                   'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                                                   'tech_specs' => [
                                                       'cpu' => 'Qualcomm SnapDragon 466',
                                                       'ram' => '5GB',
                                                       'rom' => '196GB',
                                                       'battery' => '1549 mAh',
                                                   ],
                                                   'price' => 538.0,
                                                   '_links' => [
                                                       'self' => [
                                                           'href' => '/api/products/6',
                                                       ],
                                                       'all' => [
                                                           'href' => '/api/products',
                                                       ],
                                                   ],
                                               ]));
    }

    public function testGetProductNotExists()
    {
        $client = static::createClient();
        $token = json_decode(
            Helpers::request($client, 'POST', '/api/login', ['name' => 'Client 1', 'password' => 'client1'])
        )->token;
        $return = Helpers::request($client, 'GET', '/api/products/586', null, $token);
        $this->assertResponseStatusCodeSame(404);
    }
}
