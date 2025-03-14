<?php

namespace App\Event\EventListener;


use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AuthenticationSuccessListener{

    public  function __construct(private readonly EntityManagerInterface $em) {
      
    }

    public function __invoke(LoginSuccessEvent $event)
    {
        /** @var Users $user */
        $user = $event->getUser();

        $user->setLastLogin(new DateTime());

        $this->em->flush();

        
        
    }

}