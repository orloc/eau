<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertStatusCode(200, $this->client);
        $this->assertContains('Eve Alliance Utility', $crawler->filter('#intro .container h2')->text());
    }

    public function testLegalPage(){
        $crawler = $this->client->request('GET', '/legal');

        $this->assertStatusCode(200, $this->client);
        $this->assertContains('Copyright Notice', $crawler->filter('.legal h2')->text());
    }
}
