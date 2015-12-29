<?php

namespace AppBundle\Tests\Controller\Security;

use AppBundle\Tests\Controller\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

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
        //$this->loadFixtures(['AppBundle\DataFixtures\LoadUserData']);
        $this->logIn('NOT A ROLE');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(403, $this->client);
    }

    public function testSuperAdminLogin(){
        $this->logIn('ROLE_SUPER_ADMIN');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach($crawler->filter('ul#side-menu li a') as $item){
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(7, $menuItems);
    }

    public function testAdminLogin(){
        $this->logIn('ROLE_ADMIN');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);
    }

    public function testAllLeaderLogin(){
        $this->logIn('ROLE_ALLIANCE_LEADER');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);
    }

    public function testCEOLogin(){
        $this->logIn('ROLE_CEO');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);
    }

    public function testDirectorLogin(){
        $this->logIn('ROLE_DIRECTOR');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);
    }

    public function testMemberLogin(){
        $this->logIn('ROLE_CORP_MEMBER');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);
    }

}
