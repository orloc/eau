<?php

namespace AppBundle\DataFixtures\Test;

use AppBundle\Entity\ApiCredentials;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCorporateData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $goodKey = $this->container->getParameter('test_config')['api_keys']['good_corp_key'];
        $corpManager = $this->container->get('app.corporation.manager');

        $apiManager = $this->container->get('app.apikey.manager');

        $key = new ApiCredentials();
        $key->setApiKey($goodKey['key'])
            ->setVerificationCode($goodKey['code']);

        $res = $apiManager->validateAndUpdateApiKey($key, 'Corporation', '134217727');
        $apiManager->updateCorporationKey($key, $res);

        $corp = $corpManager->createNewCorporation($key);

        $manager->persist($corp);
        $manager->flush();

        $corpManager->checkCorporationDetails($corp);

        $manager->persist($corp);
        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}
