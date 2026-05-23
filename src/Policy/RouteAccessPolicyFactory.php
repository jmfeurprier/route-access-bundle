<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Policy;

use Jmf\RouteAccess\Matching\RouteMatcher;
use Jmf\RouteAccess\Matching\RouteMatcherCollection;
use Jmf\RouteAccess\Matching\RouteMatcherInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class RouteAccessPolicyFactory
{
    public function __construct(
        private RoutePatternExpander $routePatternExpander,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @param array<string, mixed> $routePermissionsConfig
     *
     * @throws InvalidArgumentException
     */
    public function create(array $routePermissionsConfig): RouteAccessPolicy
    {
        return $this->cache->get(
            'jmf_route_access_' . $this->configHash($routePermissionsConfig),
            fn(
                ItemInterface $item,
            ): RouteAccessPolicy => $this->build($routePermissionsConfig),
        );
    }

    /**
     * @param array<string, mixed> $routePermissionsConfig
     */
    private function configHash(array $routePermissionsConfig): string
    {
        return hash('xxh128', serialize($routePermissionsConfig));
    }

    /**
     * @param array<string, mixed> $routePermissionsConfig
     */
    private function build(array $routePermissionsConfig): RouteAccessPolicy
    {
        return new RouteAccessPolicy(
            $this->buildPublicRouteMatchers($routePermissionsConfig),
            $this->buildPrivateRouteMatchers($routePermissionsConfig),
        );
    }

    /**
     * @param array<string, mixed> $routePermissionsConfig
     */
    private function buildPublicRouteMatchers(array $routePermissionsConfig): RouteMatcherInterface
    {
        $routePatterns = $routePermissionsConfig['public_routes'] ?? [];

        Assert::allStringNotEmpty($routePatterns);

        return $this->buildRouteMatcher($routePatterns);
    }

    /**
     * @param array<string, mixed> $routePermissionsConfig
     *
     * @return array<non-empty-string, RouteMatcherInterface>
     */
    private function buildPrivateRouteMatchers(array $routePermissionsConfig): array
    {
        $routeMatchers     = [];
        $roleRoutePatterns = $routePermissionsConfig['roles'] ?? [];

        Assert::isMap($roleRoutePatterns);

        foreach ($roleRoutePatterns as $role => $routePatterns) {
            Assert::stringNotEmpty($role);
            Assert::allStringNotEmpty($routePatterns);

            $routeMatchers[$role] = $this->buildRouteMatcher($routePatterns);
        }

        return $routeMatchers;
    }

    /**
     * @param non-empty-string[] $routePatterns
     */
    private function buildRouteMatcher(iterable $routePatterns): RouteMatcherInterface
    {
        $routeMatchers = [];

        foreach ($routePatterns as $routePattern) {
            $routeMatchers[] = new RouteMatcher(
                $this->routePatternExpander->expand($routePattern),
            );
        }

        return new RouteMatcherCollection($routeMatchers);
    }
}
