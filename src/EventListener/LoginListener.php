<?php

namespace App\EventListener;


use App\Entity\Peacher\Peacher;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * LoginListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param InteractiveLoginEvent $event
     * @throws \Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var Peacher $peacher */
        $peacher = $event->getAuthenticationToken()->getUser();

        $peacher->setLastLogin(new \DateTime());
        // Persist the data to database.
        $this->em->persist($peacher);
        $this->em->flush();
    }
}
