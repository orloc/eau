<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class CharacterControllerTest extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
    }

    public function testBadCharKeyResponse(){
        $this->logIn('member');

        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_user_key'];

        $crawler = $this->client->request('POST', '/api/characters', [],[],[
            'CONTENT_TYPE' => 'application/json'
        ],sprintf('{ "api_key":"%sjskldfjs", "verification_code": "%skajsdfka" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(400, $this->client);
    }

    public function testGoodCharKeyResponse(){
        $this->loadFixtures([
            'AppBundle\DataFixtures\Test\LoadUserData',
            'AppBundle\DataFixtures\Test\LoadCorporateData'
        ]);

        $this->logIn('member');

        $goodKey = $this->getContainer()->getParameter('test_config')['api_keys']['good_user_key'];

        $crawler = $this->client->request('POST', '/api/characters', [],[],[
            'CONTENT_TYPE' => 'application/json'
        ],sprintf('{ "api_key":"%s", "verification_code": "%s" }', $goodKey['key'], $goodKey['code']));

        $this->assertStatusCode(200, $this->client);

        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );

        $content = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $content);
        $this->assertArrayHasKey('accessMask', $content);
        $this->assertArrayHasKey('characters', $content);
        $this->assertEquals('Account', $content['type']);

        $main = null;
        foreach ($content['characters'] as $c){
            $this->assertArrayHasKey('best_guess', $c);
            if ($c['best_guess'] === true){
                $main = $c;
            }
        }

        $crawler = $this->client->request('POST', '/api/characters/final', [],[],[
            'CONTENT_TYPE' => 'application/json'
        ],json_encode([
            'char' => $main === null ? $content['characters'][0] : $main,
            'full_key' => $content
        ], true));

        $this->assertStatusCode(200, $this->client);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $this->assertTrue(
            $this->client->getResponse()->headers->contains('Content-Type', 'application/json')
        );

        $chars = $em->getRepository('AppBundle:Character')->findAll();

        $this->assertCount(3, $chars);

    }

    public function testAdminIndex(){

    }

    public function testAllianceIndex(){

    }

    public function testCeoIndex(){

    }

    public function testDirectorIndex(){

    }
}
