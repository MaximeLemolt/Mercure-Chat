<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * Get conversation between two users
     * 
     * @Route("/messages/{author<\d+>}/{recipient<\d+>}", name="conversation")
     */
    public function getConversation(User $author = null, User $recipient = null, MessageRepository $messageRepository, UserRepository $userRepository)
    {
        if($author === null || $recipient === null) {
            throw $this->createNotFoundException('Utilisateur inexistant.');
        }

        $users = $userRepository->findAll();
        $messages = $messageRepository->getMessagesBetweenTwoUsers($author, $recipient);

        return $this->render('index/chat.html.twig', [
            'messages' => $messages,
            'users' => $users,
            'recipient' => $recipient
        ]);
    }
}
