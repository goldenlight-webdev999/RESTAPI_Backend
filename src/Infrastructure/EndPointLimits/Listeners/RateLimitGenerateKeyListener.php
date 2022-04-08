<?php

declare(strict_types=1);


namespace App\Infrastructure\EndPointLimits\Listeners;


use App\Domain\User\User;
use Noxlogic\RateLimitBundle\Events\GenerateKeyEvent;
use Symfony\Component\Security\Core\Security;

final class RateLimitGenerateKeyListener
{
    private $security;

    /**
     * RateLimitGenerateKeyListener constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function onGenerateKey(GenerateKeyEvent $event)
    {
        $ip = $event->getRequest()->getClientIp();
        $key = sprintf('ip_%s', $ip);

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $key = sprintf('user_%s', $user->getId()->toString());
        }

        $event->setKey($key);
        $event->stopPropagation();
    }
}