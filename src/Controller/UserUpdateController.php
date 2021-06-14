<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/me", methods={"PUT"})
     */
    //TODO поиск юзеров по айди а не юзернэйму
    public function index(
        ServerRequestInterface $request
    ){
        $request = $this->server->validateAuthenticatedRequest($request);// !TODO вынести

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username'=>$request->getAttribute("oauth_user_id")]);

        $data = json_decode(file_get_contents('php://input'), true);//$request почемуто не хочет давать тело запроса

        //удобно, если надо менять много полей
        $mutable = [
            'Firstname',
            'Lastname',
        ];
        //смотрим изменения и применяем
        foreach ($mutable as $item) {
            if(isset($data[mb_strtolower($item)])){
                $setFunc = 'set'.$item;
                $user->$setFunc($data[mb_strtolower($item)]);
            }
        }
        if(isset($data['group'])){
            $user->getGroup()->setTitle($data['group']);
        }
        $entityManager->flush();
        return $this->json([
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'group' => $user->getGroup()->getTitle(),
        ]);
    }
}