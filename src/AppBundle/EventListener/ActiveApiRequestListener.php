<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
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

    public function __construct(TokenStorageInterface $storage, Router $router, Session $session) {
        $this->storage = $storage;
        $this->router = $router;
        $this->session = $session;
    }

    public function onRequest(GetResponseEvent $event){

        $token = $this->storage->getToken();
        $request = $event->getRequest();

        $blackList = [
            'api.server.status'
        ];

        if ($request->attributes->get('_controller') === 'AppBundle\Controller\Admin\CharacterController::indexAction' || $this->session->has(self::ACTIVE_API_CHECK)){
            $this->session->remove(self::ACTIVE_API_CHECK);
            return;
        }

        if (!$event->isMasterRequest()
            || $token === null
            || in_array($request->attributes->get('_route'), $blackList)
            || !strstr(explode('::',$request->attributes->get('_controller'))[0],'AppBundle')
        ){
            return;
        }

        if ($token->isAuthenticated()
            && ($user = $this->storage->getToken()->getUser()) instanceof User
        ){
            $characters =  $user->getCharacters();

            if ($characters->count() == 0){
                $response = new RedirectResponse($this->router->generate('characters'));
                $this->session->set(self::ACTIVE_API_CHECK, time());
                $event->setResponse($response);
            }
        }

    }

}
