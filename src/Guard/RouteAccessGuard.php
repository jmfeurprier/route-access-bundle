<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Guard;

use Jmf\RouteAccess\Security\RouteAccess;
use Override;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class RouteAccessGuard implements RouteAccessGuardInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    #[Override]
    public function canAccessRoute(string $routeName): bool
    {
        return $this->authorizationChecker->isGranted(
            RouteAccess::ATTRIBUTE,
            $routeName,
        );
    }
}
