<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\WebTestCase;

class CorporationControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testBadKeyResponse()
    {
        $this->loadFixtures(['AppBundle\DataFixtures\Test\LoadUserData']);
        $this->logIn('super_admin');

        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_corp_key'];

        $crawler = $this->client->request('POST', '/api/corporation/', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], sprintf('{ "api_key":"%sjskldfjs", "verification_code": "%skajsdfka" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(400, $this->client);
    }

    public function testGoodKeyResponse()
    {
        $this->logIn('super_admin');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_corp_key'];

        $crawler = $this->client->request('POST', '/api/corporation/', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], sprintf('{ "api_key":"%s", "verification_code": "%s" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(200, $this->client);

        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('created_at', $content);

        $corps = $em->getRepository('AppBundle:Corporation')->findAll();

        $this->assertCount(1, $corps);
    }

    public function testAdminIndex()
    {
    }

    public function testAllianceIndex()
    {
    }

    public function testCeoIndex()
    {
    }

    public function testDirectorIndex()
    {
    }
}
