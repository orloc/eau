<?php

namespace AppBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use \Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public function setContainer(ContainerInterface $container = null){
        $this->container = $container;
    }

    public function load(ObjectManager $manager){

        $userManager = $this->container->get('fos_user.user_manager');

        $users = [
            [
                'username' => 'orloc',
                'email' => 'teppga52@gmail.com',
                'enabled' => true,
                'password' => 'password',
                'locked' => false,
                'roles' => ['ROLE_SUPER_ADMIN']
            ],
            [
                'username' => 'alliance_leader',
                'email' => 'test4@gmail.com',
                'enabled' => true,
                'password' => 'password',
                'locked' => false,
                'roles' => ['ROLE_ALLIANCE_LEADER']
            ],
            [
                'username' => 'ceo',
                'email' => 'test1@gmail.com',
                'enabled' => true,
                'password' => 'password',
                'locked' => false,
                'roles' => ['ROLE_CEO']
            ],
            [
                'username' => 'director',
                'email' => 'test2@gmail.com',
                'enabled' => true,
                'password' => 'password',
                'locked' => false,
                'roles' => ['ROLE_DIRECTOR']
            ],
            [
                'username' => 'member',
                'email' => 'test3@gmail.com',
                'enabled' => true,
                'password' => 'password',
                'locked' => false,
                'roles' => ['ROLE_CORP_MEMBER']
            ],
        ];

        foreach ($users as $u){
            $user = $this->createUser($u, $userManager);
            $userManager->updateUser($user, false);
        }

        $manager->flush();
    }

    public function createUser(array $data, UserManager $userManager){

        $user = $userManager->createUser();

        $user->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setEnabled($data['enabled'])
            ->setLocked($data['locked'])
            ->setRoles($data['roles'])
            ->setPlainPassword($data['password']);

        return $user;

    }

    public function getOrder(){
        return 1;
    }
}