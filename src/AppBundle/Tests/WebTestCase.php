<?php

namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class WebTestCase extends BaseCase
{
    protected $client = null;

    protected function logIn($username, $string = false)
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);

        if ($user === null) {
            var_dump($username, $user);

            var_dump($em->getRepository('AppBundle:User')->findAll());
            die;
        }

        $token = new UsernamePasswordToken(
            $string === true ? $user->getUsername() : $user,
            null,
            $firewall,
            $user->getRoles()
        );

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $em->clear();
    }
}
