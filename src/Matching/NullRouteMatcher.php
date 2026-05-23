<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Matching;

use Override;

readonly class NullRouteMatcher implements RouteMatcherInterface
{
    #[Override]
    public function matches(string $routeName): bool
    {
        return false;
    }
}
