<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Service\DataManager\ApiKeyManager;
use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\NullLogger;

class ApiKeyManagerTest extends WebTestCase
{

    protected $manager;

    public function setUp(){
        $pheal = $this->getContainer()->get('tarioch.pheal.factory');
        $doctrine = $this->getContainer()->get('doctrine');
        $registry = $this->getContainer()->get('evedata.registry');

        $this->manager = new ApiKeyManager($pheal, $doctrine, $registry, new NullLogger());
    }

    public function testBuildInstanceFromRequest(){
        $request = new Request();

        $request->request->replace(['api_key' => 'apiKey', 'verification_code' => 'code'] );

        $apiKey = $this->manager->buildInstanceFromRequest($request->request);

        $this->assertTrue($apiKey instanceof ApiCredentials);
        $this->assertSame('apiKey', $apiKey->getApiKey());
        $this->assertSame('code', $apiKey->getVerificationCode());


    }

    public function testUpdateKey(){
        //@TODO rethink the use of this and test me when ready
    }

    /**
     * @expectedException Pheal\Exceptions\ConnectionException
     */
    public function testBadPhealRequest(){
        $noExpire = new ApiCredentials();
        $this->manager->validateKey($noExpire);
    }

    /**
     * @expectedException AppBundle\Exception\InvalidExpirationException
     */

    public function testBadExpireException(){
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['no_expire']['key'])
            ->setVerificationCode($config['api_keys']['no_expire']['code']);

        $this->manager->validateKey($key);
    }

    /**
     * @expectedException AppBundle\Exception\InvalidApiKeyTypeException
     */

    public function testBadTypeException(){
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['bad_type']['key'])
            ->setVerificationCode($config['api_keys']['bad_type']['code']);

        $this->manager->validateKey($key, 'Account');
    }

    /**
     * @expectedException AppBundle\Exception\InvalidAccessMaskException
     */

    public function testBadAccessMaskException(){
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['bad_mask']['key'])
            ->setVerificationCode($config['api_keys']['bad_mask']['code']);

        $this->manager->validateKey($key);
    }

    public function testGoodCharKey(){
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['good_user_key']['key'])
            ->setVerificationCode($config['api_keys']['good_user_key']['code']);

        $this->manager->validateAndUpdateApiKey($key, 'Account');

        $this->assertSame('Account', $key->getType());
        $this->assertSame('1073741823', $key->getAccessMask());
    }

    public function testGoodCorpKey(){
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['good_corp_key']['key'])
            ->setVerificationCode($config['api_keys']['good_corp_key']['code']);

        $this->manager->validateAndUpdateApiKey($key, 'Corporation');

        $this->assertSame('Corporation', $key->getType());
        $this->assertSame('134217727', $key->getAccessMask());
    }

}
