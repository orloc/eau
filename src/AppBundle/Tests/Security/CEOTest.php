<?php

namespace AppBundle\Tests\Controller\Security;

use AppBundle\Tests\WebTestCase;

class CEOTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
        $this->logIn('ceo', true);
    }

    public function testDirectorLogin()
    {
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach ($crawler->filter('ul#side-menu li a') as $item) {
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(8, $menuItems);
    }

    public function testCorpPage()
    {
        $crawler = $this->client->request('GET', '/admin/corporation');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(0, $crawler->filter('slide-button'));
    }

    public function testUserPage()
    {
        $crawler = $this->client->request('GET', '/admin/user');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(2, $crawler->filter('slide-button'));
    }

    public function testIndustryPages()
    {
        $crawler = $this->client->request('GET', '/admin/industry');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(1, $crawler->filter('slide-button'));

        $this->client->request('GET', '/admin/industry/price-helper');
        $this->assertStatusCode(200, $this->client);
    }

    public function setCharacters()
    {
        $crawler = $this->client->request('GET', '/admin/character');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(1, $crawler->filter('slide-button'));
    }

    public function testGeneralRoutes()
    {
        $routes = [
            '/admin/character' => 1,
            '/admin/user' => 1,
            '/admin/corporation' => 1,
            '/admin/dashboard' => 1,
            '/admin/industry' => 1,
            '/admin/industry/price-helper' => 1,
            '/admin/template/corpoverview' => 1,
            '/admin/template/corpinventory' => 1,
            '/admin/template/corp_market_orders' => 1,
            '/admin/template/api_keys' => 1,
            '/admin/template/corp_members' => 1,
            '/admin/template/corp_towers' => 1,
        ];

        foreach ($routes as $r => $expected) {
            $this->client->request('GET', $r);
            $this->assertStatusCode($expected === 1 ? 200 : 403, $this->client,
                "On $r"
            );
        }
    }
}
