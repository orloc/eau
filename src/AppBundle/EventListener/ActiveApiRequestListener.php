<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActiveApiRequestListener {

    const ACTIVE_API_CHECK = 'EveAppActiveApiCheck';

    protected $storage;
    protected $router;

    protected $session;

    protected $log;

    public function __construct(TokenStorageInterface $storage, Router $router, Session $session, Logger $logger) {
        $this->storage = $storage;
        $this->router = $router;
        $this->session = $session;
        $this->log = $logger;
    }

    public function onRequest(GetResponseEvent $event){

        $token = $this->storage->getToken();
        $request = $event->getRequest();

        $blackList = [
            'api.server.status',
            'template.serverstatus',
            'template.slidebutton'
        ];

        if (!$event->isMasterRequest()
            || $token === null
            || in_array($request->attributes->get('_route'), $blackList)
            || !strstr(explode('::',$request->attributes->get('_controller'))[0],'AppBundle')
        ){
            $this->log->debug(sprintf("LISTENER Skipping %s", $request->attributes->get('_route')));

            return;
        }

        if ($request->attributes->get('_controller') === 'AppBundle\Controller\Admin\CharacterController::indexAction'
            || $this->session->has(self::ACTIVE_API_CHECK)
        ){
            $this->log->debug(sprintf("LISTENER Skipping %s", $request->attributes->get('_route')));
            $this->session->remove(self::ACTIVE_API_CHECK);
            return;
        }


        if ($token->isAuthenticated()
            && ($user = $this->storage->getToken()->getUser()) instanceof User
        ){
            $characters =  $user->getCharacters();

            if ($characters->count() == 0){
                $this->log->debug(sprintf("LISTENER REDIRECT for %s", $request->attributes->get('_route')));
                $response = new RedirectResponse($this->router->generate('characters'));
                $this->session->set(self::ACTIVE_API_CHECK, time());
                $event->setResponse($response);
            }
        }

    }

}
