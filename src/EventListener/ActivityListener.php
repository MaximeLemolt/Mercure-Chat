<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Listener that updates the last activity of the authenticated user
 */
class ActivityListener
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function onKernelRequest (RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        // Check token authentication availability
        if ($this->security->getToken()) {
            // Get the user
            $user = $this->security->getToken()->getUser();

            if ( ($user instanceof UserInterface) ) {
                $user->setLastActivityAt(new \DateTime());
                $this->em->flush();
            }
        }
    }
}