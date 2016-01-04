<?php

namespace AppBundle\Tests\Service;

use AppBundle\Service\DataManager\ApiKeyManager;
use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\NullLogger;

class CorporationManagerTest extends WebTestCase
{

    protected $manager;

    public function setUp(){
        $pheal = $this->getContainer()->get('tarioch.pheal.factory');
        $doctrine = $this->getContainer()->get('doctrine');
        $registry = $this->getContainer()->get('evedata.registry');

        $this->manager = new ApiKeyManager($pheal, $doctrine, $registry, new NullLogger());
    }

    public function testCreateNewCorporation(){

    }

    public function testCorporationDetails(){

    }

    public function testGetMembers(){

    }

    public function testGetCorporationSheet(){

    }

    public function testInitializeAccounts(){

    }

}
