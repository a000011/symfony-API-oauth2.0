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
use App\Repository\UserRepository;

class MainController extends AbstractController
{
    private $server;
    private $userRepository;

    public function __construct(
        ScopeManager $scopeManager,
        ResourceServer $server,
        UserRepository $userRepository
    ){
        $this->userRepository = $userRepository;
        $this->server = $server;
    }
    /**
     * @Route("/me", methods={"GET"})
     */
    public function me_get(
        ServerRequestInterface $request,
        ResponseFactoryInterface $responseFactory
    ){
        $request = $this->server->validateAuthenticatedRequest($request);
        $user = $this->userRepository->loadUserByUsername($request->getAttribute("oauth_user_id"));
        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'group' => $user->getGroup()->getTitle(),
        ]);
    }
    /**
     * @Route("/me", methods={"PUT"})
     */
    public function me_put(
        ServerRequestInterface $request,
        ResponseFactoryInterface $responseFactory
    ){
        $request = $this->server->validateAuthenticatedRequest($request);
        $user = $this->userRepository->loadUserByUsername($request->getAttribute("oauth_user_id"));
        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'group' => $user->getGroup()->getTitle(),
        ]);
    }
}
