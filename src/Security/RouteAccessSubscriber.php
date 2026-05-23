<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Security;

use Override;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class RouteAccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CurrentRouteNameResolver $currentRouteNameResolver,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $controllerEvent): void
    {
        $routeName = $this->currentRouteNameResolver->tryResolve($controllerEvent);

        if (null === $routeName) {
            return;
        }

        if (!$this->authorizationChecker->isGranted(RouteAccess::ATTRIBUTE, $routeName)) {
            throw new AccessDeniedException(
                message: sprintf('Access denied to route "%s".', $routeName),
            );
        }
    }
}
