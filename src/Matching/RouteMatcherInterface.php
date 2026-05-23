<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Matching;

interface RouteMatcherInterface
{
    public function matches(string $routeName): bool;
}
