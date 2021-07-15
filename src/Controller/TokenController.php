<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
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
     *   @OA\Response(
     *     response=400,
     *     description="bad request",
     *     content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="errors",
     *                         type="string",
     *                         description="error"
     *                     ),
     *                     @OA\Property(
     *                         property="error_description",
     *                         type="string",
     *                         description="error"
     *                     ),
     *                     @OA\Property(
     *                         property="hint",
     *                         type="string",
     *                         description="error"
     *                     ),
     *                     @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="error"
     *                     ),
     *                     example={
     *                         "errors": {
     *                              "error": "unsupported_grant_type",
     *                              "error_description": "The authorization grant type is not supported by the authorization server.",
     *                              "hint": "Check that all required parameters have been provided",
     *                              "message": "The authorization grant type is not supported by the authorization server."
     *                          }
     *                     }
     *                 )
     *             )
     *         }
     * )
     *   @OA\Response(
     *     response=200,
     *     description="unauthorized"
     * )
     * @OA\Tag(name="Token")
     */
    public function indexAction(
        ServerRequest $request,
        ResponseFactoryInterface $responseFactory
    ): ResponseInterface {
        dd($request);
    }
}
