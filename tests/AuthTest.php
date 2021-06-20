<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    private $client;
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testAuth(): String
    {
        $requestUri = '/token';
        $request = [
            'grant_type'=>'password',
            'client_id'=>'TestClient',
            'client_secret'=>'ClientSecret',
            'scope'=>'read',
            'username'=>'User2002',
            'password'=>'password'
        ];
        $this->client->request(
            'POST',
            $requestUri,
            $request,
            [],
            []
        );
        $this->assertResponseIsSuccessful('');
        return json_decode($this->client->getResponse()->getContent(), true)['access_token'];
    }
    
    /**
     * @depends testAuth
     */
    public function testUserInfo(String $access_token): void
    {
        $requestUri = '/me';
        $this->client->request(
            'GET',
            $requestUri,
            [],
            [],
            ['HTTP_Authorization'=>"Bearer {$access_token}"]
        );
        $this->assertResponseIsSuccessful('');
    }

}
