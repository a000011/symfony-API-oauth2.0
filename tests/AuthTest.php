<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    public function testAuth(): String
    {
        $client = static::createClient();
        $requestUri = '/token';
        $request = [
            'grant_type'=>'password',
            'client_id'=>'8241f3308a649c873c7a53e82400fef3',
            'client_secret'=>'f440ef578beaf5a3f4cabf0f7e3184f43e02493d53b4d17d0f5437e0f6240febb8f8daf433cac2fe1eb67c42800a4c3d9753cab22f70ae919afcf0a3680ee084',
            'scope'=>'read',
            'username'=>'anton228',
            'password'=>'Ivan'
        ];
        $auth = $client->request(
            'POST',
            $requestUri,
            $request,
            [],
            []
        );
        $this->assertResponseIsSuccessful('');
        return json_decode($client->getResponse()->getContent(), true)['access_token'];
    }
    
    /**
     * @depends testAuth
     */
    public function testUserInfo(String $access_token): void
    {
        $client = static::createClient();
        $requestUri = '/me';
        $auth = $client->request(
            'GET',
            $requestUri,
            [],
            [],
            ['HTTP_Authorization'=>"Bearer {$access_token}"]
        );
        $this->assertResponseIsSuccessful('');
    }

}
