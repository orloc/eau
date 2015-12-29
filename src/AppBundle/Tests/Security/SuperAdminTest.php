<?php

namespace AppBundle\Tests\Security;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class SuperAdminTest extends WebTestCase
{
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
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
}
