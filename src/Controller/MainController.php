<?php

namespace App\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\Bundle\OAuth2Bundle\Manager\InMemory\ScopeManager;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;

class MainController extends AbstractController
{
    /**
     * @var AuthorizationServer
     */
    private $server;
    public function __construct(AuthorizationServer $server, ScopeManager $scopeManager)
    {
        $this->server = $server;
    }
    /**
     * @Route("/me", methods={"GET"})
     */
    public function me_get(): Response
    {
        return $this->json([
            'me' => 'GET'
        ]);
    }
    /**
     * @Route("/me", methods={"PUT"})
     */
    public function me_put():Response
    {
        return $this->json([
            'me'=>'PUT'
        ]);
    }
    /**
     * @Route("/token", methods={"POST"})
     */
    public function token(
        ServerRequestInterface $request,
        ResponseFactoryInterface $responseFactory)
    {
        $response = $responseFactory->createResponse();
        try {
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($response);
        }
    }
}
