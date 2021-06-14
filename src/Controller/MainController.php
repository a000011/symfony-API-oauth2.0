<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\AuthorizationServer;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\OAuth2\Server\Exception\OAuthServerException;
use Trikoder\Bundle\OAuth2Bundle\Manager\InMemory\ScopeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @var AuthorizationServer
     */
    private $server;
    private $resServer;

    public function __construct(
        AuthorizationServer $server,
        ScopeManager $scopeManager,
        ResourceServer $resServer
    ){
        $this->server = $server;
        $this->resServer = $resServer;
    }
    /**
     * @Route("/me", methods={"GET"})
     */
    public function me_get(
        ServerRequestInterface $request,
        ResponseFactoryInterface $responseFactory
    ){
        $request = $this->resServer->validateAuthenticatedRequest($request);
        $user = $request->getAttribute("oauth_user_id");
        return $this->json([
            'u r' => $user
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
}
