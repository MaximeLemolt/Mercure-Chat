<?php

namespace App\Controller;

use App\Mercure\CookieGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/chat", name="chat")
     */
    public function chat(CookieGenerator $cookieGenerator): Response
    {
        $response = $this->render('index/chat.html.twig', []);
        $response->headers->setCookie($cookieGenerator->generate());

        return $response;
    }
}
