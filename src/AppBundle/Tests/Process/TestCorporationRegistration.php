<?php

namespace AppBundle\Tests\Process;

use AppBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class TestCorporationRegistration extends WebTestCase
{

    public function setUp(){
        $this->client = static::createClient();
    }

}
