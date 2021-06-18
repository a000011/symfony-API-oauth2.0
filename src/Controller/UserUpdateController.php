<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Group;
use App\Repository\UserRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserUpdateController extends AbstractController
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
     * @Route("/me", methods={"PUT"}, format="json")
     * @OA\RequestBody(
     *     @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="firstname",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="lastname",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="group",
     *                     type="string"
     *                 )
     *            )
     *       )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns changed user",
     * )
     *  @OA\Response(
     *     response=400,
     *     description="validation error",
     *     content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="errors",
     *                         type="array",
     *                         description="errors array",
     *                         @OA\Items(
     *                             type="string",
     *                             description="error text"
     *                         )
     *                     ),
     *                     example={
     *                         "errors": {
     *                              "Your firstname must be at least 2 characters long",
     *                              "Your lastname cannot be longer than 20 characters"
     *                          }
     *                     }
     *                 )
     *             )
     *         }
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function index(
        ServerRequestInterface $request,
        ValidatorInterface $validator
    ){
        try{
            $request = $this->server->validateAuthenticatedRequest($request);
        }
        catch(OAuthServerException $e){
            $error = ["errors"=>["bad access token"]];
            return new Response(json_encode($error), Response::HTTP_UNAUTHORIZED);
        }

        $entityManager =$this->getDoctrine()->getManager();
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $this->setUser($request, $entityManager);
        $user = $this->changeUser($user, $data, $entityManager);
        if($user === null){
            $error = json_encode(["errors"=>["bad request"]]);
            return new Response($error, Response::HTTP_BAD_REQUEST);
        }
        $errors = $validator->validate($user);
        if(count($errors)== 0){
            $entityManager->flush();
            $response = $user->serialize([
                'firstname',
                'lastname',
                'Group'=>['title']
            ]);
            return new Response($response, Response::HTTP_OK);
        }
        else{
            $errorResponse = ['errors'=>[]];
            foreach ($errors as $item){
                array_push($errorResponse['errors'], $item->getMessage());
            }
            return new Response(json_encode($errorResponse), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * This method update Users property
     * @param User $user
     * @param array $data
     * @param $entityManager
     * @return User
     */
    public function changeUser(User $user, array $data, $entityManager): ?User
    {
        $mutable = [
            'Firstname',
            'Lastname',
            'Group'
        ];
        foreach ($mutable as $item) {
            if(isset($data[mb_strtolower($item)])){
                if($item == 'Group'){
                    $title = $user->getGroup()->getTitle();
                    if($data['group'] != $title ){
                        $group = $entityManager
                            ->getRepository(Group::class)
                            ->findOneBy(['title'=>$data['group']]);
                        if($group === null){
                            return null;
                        }
                        $user->setGroup($group);
                    }
                }else{
                    $setFunc = 'set'.$item;
                    $user->$setFunc($data[mb_strtolower($item)]);
                }
            }
        }
        return $user;
    }

    /**
     * This method return authenticated User
     * @param ServerRequestInterface $request
     * @param $entityManager
     * @return User
     */
    public function setUser(ServerRequestInterface $request, $entityManager): User
    {
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['username'=>$request->getAttribute("oauth_user_id")]);
        return $user;
    }
}
