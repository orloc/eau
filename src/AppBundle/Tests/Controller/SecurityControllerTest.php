<?php

namespace AppBundle\Tests\Controller\Security;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testLoginPage()
    {

        $crawler = $this->client->request('GET', '/login');

        $this->assertStatusCode(200, $this->client);
        $this->assertContains('Log in', $crawler->filter('form input[type=submit]')->attr('value'));
    }

    public function testBadLogin(){
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(302, $this->client);
    }
}
