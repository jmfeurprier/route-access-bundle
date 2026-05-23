<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Twig;

use Jmf\RouteAccess\Guard\RouteAccessGuardInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouteAccessExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    public function __construct(
        private readonly RouteAccessGuardInterface $routeAccessGuard,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    #[Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                $this->prefix . 'can_access_route',
                $this->canAccessRoute(...),
            ),
        ];
    }

    public function canAccessRoute(string $routeName): bool
    {
        return $this->routeAccessGuard->canAccessRoute($routeName);
    }
}
