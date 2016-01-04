<?php

namespace AppBundle\Tests\Process;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Entity\Corporation;
use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class CorporationRegistrationTest extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();

        $emMock = $this->getServiceMockBuilder('doctrine.orm.default_entity_manager');

        $emMock->expects($this->once())
            ->method('flush');

        $creds = new ApiCredentials();

        $apiMock = $this->getServiceMockBuilder('app.apikey.manager');
        $apiMock->expects($this->any())
            ->method('buildInstanceFromRequest')
            ->will($this->returnValue($creds))
            ->method('validateAndUpdateApiKey')
            ->will($this->returnValue($creds))
            ->method('updateCorporationKey')
            ->will($this->returnValue($creds));


        $corp = new Corporation();
        $corp->addApiCredential($creds);
        $corpMock = $this->getServiceMockBuilder('app.corporation.manager');
        $corpMock->expects($this->any())
            ->method('createNewCorporation')
            ->will($this->returnValue($corp));

        $this->client->getContainer()->set('doctrine.orm.default_entity_manager', $emMock);
        $this->client->getContainer()->set('app.apikey.manager', $apiMock);
        $this->client->getContainer()->set('app.corporation.manager', $corpMock);
    }

    public function testBadKeyResponse(){
        $this->logIn('ROLE_SUPER_ADMIN');

        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_corp_key'];

        $crawler = $this->client->request('POST', '/api/corporation/', [],[],[
            'CONTENT_TYPE' => 'application/json'
        ],sprintf('{ "api_key":"%sjskldfjs", "verification_code": "%skajsdfka" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(400, $this->client);
    }

    public function testGoodKeyResponse(){
        $this->loadFixtures(['AppBundle\DataFixtures\LoadUserData']);
        $this->logIn('ROLE_SUPER_ADMIN');

        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_corp_key'];

        $crawler = $this->client->request('POST', '/api/corporation/', [],[],[
            'CONTENT_TYPE' => 'application/json'
        ],sprintf('{ "api_key":"%s", "verification_code": "%s" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(200, $this->client);

        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );
    }
}
