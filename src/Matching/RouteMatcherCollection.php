<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Matching;

use Override;
use Webmozart\Assert\Assert;

readonly class RouteMatcherCollection implements RouteMatcherInterface
{
    /**
     * @param RouteMatcherInterface[] $routeMatchers
     */
    public function __construct(
        private iterable $routeMatchers,
    ) {
        Assert::allIsInstanceOf($this->routeMatchers, RouteMatcherInterface::class);
    }

    #[Override]
    public function matches(string $routeName): bool
    {
        foreach ($this->routeMatchers as $routeMatcher) {
            if ($routeMatcher->matches($routeName)) {
                return true;
            }
        }

        return false;
    }
}
