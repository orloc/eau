<?php

namespace AppBundle\Tests\Security;

use AppBundle\Tests\WebTestCase;

class CorpMemberTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
        $this->logIn('member', true);
    }

    public function testCorpMemberLogin()
    {
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $this->assertStatusCode(200, $this->client);

        $menuItems = [];
        foreach ($crawler->filter('ul#side-menu li a') as $item) {
            $menuItems[] = $item->textContent;
        }

        $this->assertCount(5, $menuItems);
    }

    public function testCorpPage()
    {
        $crawler = $this->client->request('GET', '/admin/corporation');
        $this->assertStatusCode(403, $this->client);
    }

    public function testUserPage()
    {
        $crawler = $this->client->request('GET', '/admin/user');
        $this->assertStatusCode(403, $this->client);
    }

    public function testIndustryPages()
    {
        $crawler = $this->client->request('GET', '/admin/industry');
        $this->assertStatusCode(200, $this->client);

        $this->assertCount(0, $crawler->filter('slide-button'));

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
            '/admin/corporation' => 0,
            '/admin/dashboard' => 1,
            '/admin/industry' => 1,
            '/admin/user' => 0,
            '/admin/industry/price-helper' => 1,
            '/admin/template/corpoverview' => 0,
            '/admin/template/corpinventory' => 0,
            '/admin/template/corp_market_orders' => 0,
            '/admin/template/api_keys' => 0,
            '/admin/template/corp_members' => 0,
            '/admin/template/corp_towers' => 0,
        ];

        foreach ($routes as $r => $expected) {
            $this->client->request('GET', $r);
            $this->assertStatusCode($expected === 1 ? 200 : 403, $this->client,
                "On $r"
            );
        }
    }
}
