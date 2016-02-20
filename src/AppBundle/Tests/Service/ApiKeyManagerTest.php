<?php

namespace AppBundle\Tests\Service;

use AppBundle\Entity\ApiCredentials;
use AppBundle\Service\DataManager\ApiKeyManager;
use AppBundle\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\NullLogger;

class ApiKeyManagerTest extends WebTestCase
{
    protected $manager;

    public function setUp()
    {
        $pheal = $this->getContainer()->get('tarioch.pheal.factory');
        $doctrine = $this->getContainer()->get('doctrine');
        $registry = $this->getContainer()->get('evedata.registry');

        $this->manager = new ApiKeyManager($pheal, $doctrine, $registry, new NullLogger());
    }

    public function testBuildInstanceFromRequest()
    {
        $request = new Request();

        $request->request->replace(['api_key' => 'apiKey', 'verification_code' => 'code']);

        $apiKey = $this->manager->buildInstanceFromRequest($request->request);

        $this->assertTrue($apiKey instanceof ApiCredentials);
        $this->assertSame('apiKey', $apiKey->getApiKey());
        $this->assertSame('code', $apiKey->getVerificationCode());
    }

    public function testUpdateKey()
    {
        //@TODO rethink the use of this and test me when ready
    }

    /**
     * @expectedException Pheal\Exceptions\ConnectionException
     */
    public function testBadPhealRequest()
    {
        $noExpire = new ApiCredentials();
        $this->manager->validateKey($noExpire, 'Account', '1073741823');
    }

    /**
     * @expectedException AppBundle\Exception\InvalidExpirationException
     */
    public function testBadExpireException()
    {
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['no_expire']['key'])
            ->setVerificationCode($config['api_keys']['no_expire']['code']);

        $this->manager->validateKey($key, 'Account', '1073741823');
    }

    /**
     * @expectedException AppBundle\Exception\InvalidApiKeyTypeException
     */
    public function testBadTypeException()
    {
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['bad_type']['key'])
            ->setVerificationCode($config['api_keys']['bad_type']['code']);

        $this->manager->validateKey($key, 'Account', '1073741823');
    }

    /**
     * @expectedException \Exception
     */
    public function testDuplicateApiException()
    {
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $api_key = $config['api_keys']['good_corp_key']['key'];
        $verification_code = $config['api_keys']['good_corp_key']['code'];

        $key->setApiKey($api_key)
            ->setVerificationCode($verification_code);

        $this->manager->validateKey($key, 'Corporation', '134217727');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $existing = $em->getRepository('AppBundle:ApiCredentials')->findAll();
        foreach ($existing as $e) {
            $em->remove($e);
        }

        $em->flush();

        $afterPurge = $em->getRepository('AppBundle:ApiCredentials')->findAll();

        $this->assertCount(0, $afterPurge);
    }

    /**
     * @expectedException AppBundle\Exception\InvalidAccessMaskException
     */
    public function testBadAccessMaskException()
    {
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['bad_mask']['key'])
            ->setVerificationCode($config['api_keys']['bad_mask']['code']);

        $this->manager->validateKey($key, 'Account', '1073741823');
    }

    public function testGoodCharKey()
    {
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $key->setApiKey($config['api_keys']['good_user_key']['key'])
            ->setVerificationCode($config['api_keys']['good_user_key']['code']);

        $res = $this->manager->validateKey($key, 'Account', '1073741823')->toArray();
        // fix when update works again
        $this->assertSame('Account', $res['type']);
        $this->assertSame('1073741823', $res['accessMask']);
    }

    /*
     * @depends testDuplicateApiException
     */
    public function testGoodCorpKey()
    {
        $this->loadFixtures(['AppBundle\DataFixtures\Test\LoadUserData']);
        $config = $this->getContainer()->getParameter('test_config');

        $key = new ApiCredentials();

        $api_key = $config['api_keys']['good_corp_key']['key'];
        $verification_code = $config['api_keys']['good_corp_key']['code'];

        $key->setApiKey($api_key)
            ->setVerificationCode($verification_code);

        $res = $this->manager->validateKey($key, 'Corporation', '134217727');
        $this->manager->updateCorporationKey($key, $res);

        $this->assertSame('Corporation', $key->getType());
        $this->assertSame('134217727', $key->getAccessMask());
    }
}
