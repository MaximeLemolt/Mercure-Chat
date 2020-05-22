<?php

namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class PublishController extends AbstractController
{
    /**
     * @Route("/message", name="sendMessage", methods={"POST"})
     */
    public function chat(MessageBusInterface $bus, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): RedirectResponse
    {
        $message = $serializer->deserialize($request->getContent(), Message::class, 'json');
        $message->setAuthor($this->getUser());

        $em->persist($message);
        $em->flush();

        $update = new Update('http://localhost:8000/message', json_encode([
            'message' => $message->getContent(),
            'author' => $message->getAuthor()->getId()
        ]));
        $bus->dispatch($update);

        return $this->redirectToRoute('chat');
    }
}
