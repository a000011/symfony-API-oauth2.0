<?php


namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Repository\UserRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Http\Message\ServerRequestInterface;

class TokenValidator implements EventSubscriberInterface
{

    private $server;
    private $userRepository;

    public function __construct(
        ResourceServer $server,
        UserRepository $userRepository
    ){
        $this->userRepository = $userRepository;
        $this->server = $server;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        try{
            $requsest = $this->server->validateAuthenticatedRequest($event->getRequest());// !TODO вынести, добавить try catch

        }
        catch(OAuthServerException $e){
            $error = [
                "errors"=>[
                    "bad access token"
                ]
            ];
            return new Response(json_encode($error), Response::HTTP_UNAUTHORIZED);
        }
        $request = (object)$event->getRequest();
        $request = $this->server->validateAuthenticatedRequest($event->getRequest());// !TODO вынести
    }
}