<?php

namespace AppBundle\Tests\Controller\Security;

use Symfony\Component\BrowserKit\Cookie;

class DirectorTest extends WebTestCase
{
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testDirectorLogin(){
        $this->logIn('ROLE_DIRECTOR');
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach($crawler->filter('ul#side-menu li a') as $item){
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(6, $menuItems);
    }

}
