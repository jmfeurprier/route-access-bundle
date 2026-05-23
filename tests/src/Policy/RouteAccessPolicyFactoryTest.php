<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Policy;

use Jmf\RouteAccess\Policy\RouteAccessPolicy;
use Jmf\RouteAccess\Policy\RouteAccessPolicyFactory;
use Jmf\RouteAccess\Policy\RoutePatternExpander;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class RouteAccessPolicyFactoryTest extends TestCase
{
    private RouteAccessPolicyFactory $routeAccessPolicyFactory;

    #[Override]
    protected function setUp(): void
    {
        $cacheItem = $this->createStub(ItemInterface::class);

        $cache = $this->createStub(CacheInterface::class);
        $cache
            ->method('get')
            ->willReturnCallback(
                fn(
                    string $key,
                    callable $callback,
                ): mixed => $callback($cacheItem),
            )
        ;

        $this->routeAccessPolicyFactory = new RouteAccessPolicyFactory(
            new RoutePatternExpander(),
            $cache,
        );
    }

    public function testCreateBuildsPublicRouteMatchers(): void
    {
        $routeAccessPolicy = $this->routeAccessPolicyFactory->create(
            [
                'public_routes' => [
                    'app_login',
                    'app_logout',
                    '_profiler*',
                ],
                'roles'         => [],
            ],
        );

        self::assertTrue($routeAccessPolicy->matches('app_login', []));
        self::assertTrue($routeAccessPolicy->matches('app_logout', []));
        self::assertTrue($routeAccessPolicy->matches('_profiler_toolbar', []));
        self::assertFalse($routeAccessPolicy->matches('dashboard', []));
    }

    public function testCreateBuildsRoleRouteMatchers(): void
    {
        $routeAccessPolicy = $this->routeAccessPolicyFactory->create(
            [
                'public_routes' => [],
                'roles'         => [
                    'ROLE_USER'  => [
                        'dashboard',
                        'post.*',
                    ],
                    'ROLE_ADMIN' => ['*'],
                ],
            ],
        );

        self::assertTrue($routeAccessPolicy->matches('dashboard', ['ROLE_USER']));
        self::assertTrue($routeAccessPolicy->matches('post.index', ['ROLE_USER']));
        self::assertFalse($routeAccessPolicy->matches('admin.dashboard', ['ROLE_USER']));
        self::assertTrue($routeAccessPolicy->matches('anything', ['ROLE_ADMIN']));
    }

    public function testCreateHandlesEmptyConfig(): void
    {
        $routeAccessPolicy = $this->routeAccessPolicyFactory->create([]);

        self::assertFalse($routeAccessPolicy->matches('any.route', []));
        self::assertFalse($routeAccessPolicy->matches('any.route', ['ROLE_USER']));
    }
}
