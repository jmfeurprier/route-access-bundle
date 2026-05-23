<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Guard;

interface RouteAccessGuardInterface
{
    public function canAccessRoute(string $routeName): bool;
}
