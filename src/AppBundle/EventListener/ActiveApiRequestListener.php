<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActiveApiRequestListener {

    const ACTIVE_API_CHECK = 'EveAppActiveApiCheck';

    protected $storage;
    protected $router;

    protected $session;
    protected $doctrine;

    protected $log;

    public function __construct(TokenStorageInterface $storage, Router $router, Session $session, Logger $logger, Registry $registry) {
        $this->storage = $storage;
        $this->router = $router;
        $this->session = $session;
        $this->log = $logger;
        $this->doctrine = $registry;
    }

    public function onRequest(GetResponseEvent $event){

        $token = $this->storage->getToken();
        $request = $event->getRequest();

        $blackList = [
            'api.server.status',
            'api.characters',
            'api.character_create',
            'template.serverstatus',
            'api.character_create.validate',
            'api.character_create.finalize',
            'api.character.apicredentials',
            'template.slidebutton',
            'template.slidemenu'
        ];

        if (!$event->isMasterRequest()
            || $token === null
            || in_array('ROLE_SUPER_ADMIN', $this->getRolesArray($token->getRoles()))
            || in_array($request->attributes->get('_route'), $blackList)
            || !strstr(explode('::',$request->attributes->get('_controller'))[0],'AppBundle')
        ){
            $this->log->debug(sprintf("LISTENER Skipping %s", $request->attributes->get('_route')));

            return;
        }

        if ($request->attributes->get('_controller') === 'AppBundle\Controller\Admin\CharacterController::indexAction'
            && $this->session->has(self::ACTIVE_API_CHECK)
        ){
            $this->session->getFlashBag()->add('danger', 'You must add a character with a valid NO EXPIRY API KEY in order to proceed');
            $this->log->debug(sprintf("LISTENER Skipping %s", $request->attributes->get('_route')));
            $this->session->remove(self::ACTIVE_API_CHECK);
            return;
        }


        if ($token->isAuthenticated()
            && ($user = $this->storage->getToken()->getUser()) instanceof User
        ){
            $characters =  $user->getCharacters();

            $activeKeys = $this->doctrine->getRepository('AppBundle:ApiCredentials')
                ->getActiveKeyForUser($user);

            if ($characters->count() == 0 || count($activeKeys) <= 0){
                $this->log->debug(sprintf("LISTENER REDIRECT for %s", $request->attributes->get('_route')));
                $response = new RedirectResponse($this->router->generate('characters'));
                $this->session->set(self::ACTIVE_API_CHECK, time());
                $event->setResponse($response);
            }
        }

    }

    protected function getRolesArray(array $roles){
        return array_map(function($r){
            return $r->getRole();
        }, $roles);
    }

}
