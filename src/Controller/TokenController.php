<?php

declare(strict_types=1);

namespace App\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TokenController extends AbstractController
{
    private $server;

    public function __construct(AuthorizationServer $server)
    {
        $this->server = $server;
    }
    /**
     * @Route("/token", methods={"POST"})
     * @OA\RequestBody(
     *     @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="grant_type",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="client_id",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="client_secret",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="scope",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 )
     *            )
     *       )
     * )
     * @OA\Tag(name="Token")
     */
    public function indexAction(
        ServerRequestInterface $serverRequest,
        ResponseFactoryInterface $responseFactory
    ): ResponseInterface {
        $serverResponse = $responseFactory->createResponse();
        try {
            return $this->server->respondToAccessTokenRequest($serverRequest, $serverResponse);
        } catch (OAuthServerException $e) {
            return $e->generateHttpResponse($serverResponse);
        }
    }
}
