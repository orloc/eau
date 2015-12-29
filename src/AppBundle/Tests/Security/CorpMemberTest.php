<?php

namespace AppBundle\Tests\Security;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class CorpMemberTest extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testCorpMemberLogin(){
        $this->logIn('ROLE_CORP_MEMBER');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach($crawler->filter('ul#side-menu li a') as $item){
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(5, $menuItems);
    }

    public function testCorpPage(){
        $this->logIn('ROLE_CORP_MEMBER');
        $crawler = $this->client->request('GET', '/admin/corporation');
        $this->assertStatusCode(403, $this->client);
    }

    public function testUserPage(){
        $this->logIn('ROLE_CORP_MEMBER');
        $crawler = $this->client->request('GET', '/admin/user');
        $this->assertStatusCode(403, $this->client);
    }

    public function testIndustryPages(){
        $this->logIn('ROLE_CORP_MEMBER');

        $crawler = $this->client->request('GET', '/admin/industry');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(0, $crawler->filter('slide-button'));

        $this->client->request('GET', '/admin/industry/price-helper');
        $this->assertStatusCode(200, $this->client);
    }

    public function setCharacters(){
        $this->logIn('ROLE_CORP_MEMBER');
        $crawler = $this->client->request('GET', '/admin/character');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(1, $crawler->filter('slide-button'));
    }
}
