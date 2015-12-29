<?php

namespace AppBundle\Tests\Security;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class AllianceLeaderTest extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testDirectorLogin(){
        $this->logIn('ROLE_ALLIANCE_LEADER');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach($crawler->filter('ul#side-menu li a') as $item){
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(7, $menuItems);
    }

}
