<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;


final class TokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var ResourceServer
     */
    private $resourceServer;

    /**
     * TokenSubscriber constructor.
     * @param ResourceServer $resourceServer
     */
    public function __construct(ResourceServer $resourceServer)
    {
        $this->resourceServer = $resourceServer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
       
       return [
           KernelEvents::CONTROLLER => 'onKernelController'
       ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!\is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            $request = $event->getRequest();
            $psrRequest = (new DiactorosFactory)->createRequest($request);
            try {
                $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
            } catch (OAuthServerException $exception) {
                throw $exception;
            } catch (\Exception $exception) {
                throw new OAuthServerException($exception->getMessage(), 0, 'unknown_error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $this->enrichSymfonyRequestWithAuthData($request, $psrRequest);
        }
    }

    /**
     * @param Request $request
     * @param ServerRequestInterface $psrRequest
     */
    private function enrichSymfonyRequestWithAuthData(Request $request, ServerRequestInterface $psrRequest): void
    {
        $request = $request->request;
        $requestArray = $request->all();
        $requestArray['oauth_user_id'] = $psrRequest->getAttribute('oauth_user_id');
        $requestArray['oauth_access_token_id'] =  $psrRequest->getAttribute('oauth_access_token_id');
        $requestArray['oauth_client_id'] =  $psrRequest->getAttribute('oauth_client_id');
        $request->replace($requestArray);
    }

    
    public function onKernelException(KernelEvents $event): void
    {
        $response = new JsonResponse(['error' => 'error'], '401');
        $event->setResponse($response);
    }
}