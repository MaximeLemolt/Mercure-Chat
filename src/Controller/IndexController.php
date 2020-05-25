<?php

namespace App\Controller;

use App\Mercure\CookieGenerator;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/chat", name="chat")
     */
    public function chat(CookieGenerator $cookieGenerator, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $response = $this->render('index/chat.html.twig', [
            'users' => $users
        ]);
        $response->headers->setCookie($cookieGenerator->generate(
            [
                "http://localhost:8000/users/{$this->getUser()->getId()}"
            ]
        ));

        return $response;
    }
}
