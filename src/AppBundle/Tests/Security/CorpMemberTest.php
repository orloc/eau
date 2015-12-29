<?php

namespace AppBundle\Tests\Security;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class CorpMemberTest extends WebTestCase
{
    private $client = null;

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
}
