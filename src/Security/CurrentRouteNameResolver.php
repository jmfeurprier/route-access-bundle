<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Security;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Webmozart\Assert\Assert;

readonly class CurrentRouteNameResolver
{
    /**
     * @return null|non-empty-string
     */
    public function tryResolve(ControllerEvent $controllerEvent): ?string
    {
        if (!$controllerEvent->isMainRequest()) {
            return null;
        }

        $routeName = $controllerEvent->getRequest()->attributes->get('_route');

        Assert::nullOrStringNotEmpty($routeName);

        return $routeName;
    }
}
