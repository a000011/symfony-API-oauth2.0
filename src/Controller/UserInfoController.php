<?php

namespace App\Controller;

use App\Repository\UserRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     *  @Route("/me", methods={"GET"}, format="json")
     *  @OA\Response(
     *     response=200,
     *     description="Return authenticated user",
     * )
     *  @OA\Response(
     *     response=401,
     *     description="unauthorized"
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function index(ServerRequestInterface $request): Response
    {
        try{
            $request = $this->server->validateAuthenticatedRequest($request);//
        }
        catch(OAuthServerException $e){
            $error = [
                "errors"=>[
                    "bad access token"
                ]
            ];
            return new Response(json_encode($error), Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userRepository
            ->loadUserByUsername($request->getAttribute("oauth_user_id"));

        $response = $user->serialize([
            'firstname',
            'lastname',
            'Group'=>[
                'title'
            ]
        ]);
        return new Response($response, Response::HTTP_OK);
    }
}
