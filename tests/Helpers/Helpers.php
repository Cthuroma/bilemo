<?php

namespace App\Tests\Helpers;


class Helpers
{
    static function request($client, string $method, string $url, ?array $data = null, ?string $token = null): string
    {
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_Accept' => 'application/json'];

        if($token){
            $headers['HTTP_AUTHORIZATION'] = 'Bearer '.$token;
        }
        $client->request(
            $method,
            $url,
            [],
            [],
            $headers,
            $data ? json_encode($data) : null
        );

        return $client->getResponse()->getContent();
    }
}

