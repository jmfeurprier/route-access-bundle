<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Policy;

use Jmf\RouteAccess\Matching\NullRouteMatcher;
use Jmf\RouteAccess\Matching\RouteMatcherInterface;
use Webmozart\Assert\Assert;

readonly class RouteAccessPolicy
{
    /**
     * @param array<non-empty-string, RouteMatcherInterface> $privateRouteMatchers
     */
    public function __construct(
        private RouteMatcherInterface $publicRouteMatcher,
        private array $privateRouteMatchers,
    ) {
    }

    /**
     * @param non-empty-string   $routeName
     * @param non-empty-string[] $roles
     */
    public function matches(
        string $routeName,
        iterable $roles,
    ): bool {
        Assert::stringNotEmpty($routeName);

        if ($this->publicRouteMatcher->matches($routeName)) {
            return true;
        }

        foreach ($roles as $role) {
            if ($this->getPrivateRouteMatcher($role)->matches($routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param non-empty-string $role
     */
    private function getPrivateRouteMatcher(string $role): RouteMatcherInterface
    {
        return $this->privateRouteMatchers[$role] ?? new NullRouteMatcher();
    }
}
