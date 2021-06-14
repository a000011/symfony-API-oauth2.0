<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenAuthenticatedController extends AbstractController
{
    /**
     * @Route("/token/auth", name="token_authenticated")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TokenAuthenticatedController.php',
        ]);
    }
}
