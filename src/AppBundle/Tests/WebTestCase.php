<?php

namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseCase;
use Symfony\Component\BrowserKit\Cookie;

class WebTestCase extends BaseCase
{

    protected function logIn($role){
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('orloc', null, $firewall, [
            $role
        ]);

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
