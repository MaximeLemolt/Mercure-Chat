<?php

namespace App\Controller;

use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublishController extends AbstractController
{
    /**
     * @Route("/hub/message", name="sendMessage", methods={"POST"})
     */
    public function __invoke(MessageBusInterface $bus, Request $request): RedirectResponse
    {
        $update = new Update('http://localhost:8000/message', json_encode([
            'message' => $request->request->get('message'),
        ]));
        $bus->dispatch($update);

        return $this->redirectToRoute('home');
    }
}