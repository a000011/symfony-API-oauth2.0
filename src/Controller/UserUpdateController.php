<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
     *                             description="error test"
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
    //TODO поиск юзеров по айди а не юзернэйму
    public function index(
        ServerRequestInterface $request,
        ValidatorInterface $validator
    ){
        $request = $this->server->validateAuthenticatedRequest($request);// !TODO вынести, добавить try catch

        $entityManager =
            $this->getDoctrine()->getManager();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['username'=>$request->getAttribute("oauth_user_id")]);

        $data = json_decode(file_get_contents('php://input'), true);//$request почемуто не хочет давать тело запроса

        //удобно, если надо менять много полей
        $mutable = [
            'Firstname',
            'Lastname',
        ];
        //смотрим изменения и применяем
        foreach ($mutable as $item) {//TODO вынести
            if(isset($data[mb_strtolower($item)])){
                $setFunc = 'set'.$item;
                $user->$setFunc($data[mb_strtolower($item)]);
            }
        }
        if(isset($data['group'])){//TODO переделать
            $user->getGroup()->setTitle($data['group']);
        }


        $errors = $validator->validate($user);//TODO вынести
        if(count($errors)== 0){
            $entityManager->flush();
            $response = $user->serialize([
                'firstname',
                'lastname',
                'Group'=>['title']
            ]);
            return new Response($response, Response::HTTP_OK);
        }else{
            $errorResponse = ['errors'=>[]];//TODO изменить код ответа
            foreach ($errors as $item){
                array_push($errorResponse['errors'], $item->getMessage());
            }
            return new Response(json_encode($errorResponse), Response::HTTP_BAD_REQUEST);
        }

    }
}
