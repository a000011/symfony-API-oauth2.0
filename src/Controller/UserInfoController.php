<?php

namespace App\Controller;

use App\Repository\UserRepository;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserInfoController extends AbstractController
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
    /**
     * @Route("/me", methods={"GET"})
     */
    public function index(
        ServerRequestInterface $request
    ){
        $request = $this->server->validateAuthenticatedRequest($request);// !TODO вынести

        $user = $this->userRepository->loadUserByUsername($request->getAttribute("oauth_user_id"));
        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'group' => $user->getGroup()->getTitle(),
        ]);
    }
}
