<?php

declare(strict_types=1);


namespace App\Infrastructure\EndPointLimits\Listeners;


use App\Domain\Subscription\Subscription;
use App\Domain\User\User;
use App\Infrastructure\EndPointLimits\Annotations\MetacleanerRateLimit;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Security;

final class MetacleanerRateLimitAnnotationListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() != HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $annotationName = sprintf('_%s', MetacleanerRateLimit::ALIAS_NAME);
        $annotations = $event->getRequest()->attributes->get($annotationName , array());

        if ($annotations) {
            $firstAnnotation = reset($annotations);

            if ($firstAnnotation instanceof MetacleanerRateLimit && $firstAnnotation->getScopes()) {
                $scopes = $firstAnnotation->getScopes();

                if ($scopes) {
                    $currentScope = $this->getCurrentScope();

                    if (array_key_exists($currentScope, $scopes)) {
                        $rateLimitAnnotation = new RateLimit($scopes[$currentScope]);

                        $event->getRequest()->attributes->set(
                            sprintf('_%s', $rateLimitAnnotation->getAliasName()),
                            [$rateLimitAnnotation]
                        );
                    }
                }
            }
        }
    }

    private function getCurrentScope(): string
    {
        switch (true) {
            case $this->security->isGranted(User::ROLE_ADMIN):
                return Subscription::TYPE_ENTERPRISE;
                break;
            case $this->security->isGranted(User::ROLE_USER_BUSINESS):
                return Subscription::TYPE_ENTERPRISE;
                break;
            case $this->security->isGranted(User::ROLE_USER_PRO):
                return Subscription::TYPE_ADVANCE;
                break;
            case $this->security->isGranted(User::ROLE_USER_BASIC):
                return Subscription::TYPE_BASIC;
                break;
            case $this->security->isGranted(User::ROLE_USER):
                return Subscription::TYPE_FREE;
                break;
            default:
                return '';
                break;
        }
    }
}