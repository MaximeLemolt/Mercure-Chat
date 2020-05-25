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
    public function __invoke(MessageBusInterface $bus, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): RedirectResponse
    {
        $message = $serializer->deserialize($request->getContent(), Message::class, 'json');
        $message->setAuthor($this->getUser());

        $em->persist($message);
        $em->flush();

        $update = new Update('http://localhost:8000/message/' . $message->getAuthor()->getId() . '/' . $message->getRecipient()->getId(),
            json_encode([
                'message' => [
                    'content' => $message->getContent(),
                    'author' => $message->getAuthor()->getId(),
                    'date' => $message->getCreatedAt()->format('d-m-y | H:i')
                ],
            ]),
            // Targets (l'auteur ou le destinataire du message)
            ["http://localhost:8000/users/{$message->getAuthor()->getId()}", "http://localhost:8000/users/{$message->getRecipient()->getId()}"]
        );
        $bus->dispatch($update);

        return $this->redirectToRoute('chat');
    }
}
