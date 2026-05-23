<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Policy;

use Jmf\RouteAccess\Matching\RouteMatcherInterface;
use Jmf\RouteAccess\Policy\RouteAccessPolicy;
use PHPUnit\Framework\TestCase;

final class RouteAccessPolicyTest extends TestCase
{
    public function testMatchesReturnsTrueForPublicRoute(): void
    {
        $publicMatcher = $this->createStub(RouteMatcherInterface::class);
        $publicMatcher->method('matches')->willReturn(true);

        $routeAccessPolicy = new RouteAccessPolicy($publicMatcher, []);

        self::assertTrue($routeAccessPolicy->matches('any.route', []));
    }

    public function testMatchesReturnsTrueForPrivateRouteWithMatchingRole(): void
    {
        $publicMatcher = $this->createStub(RouteMatcherInterface::class);
        $publicMatcher->method('matches')->willReturn(false);

        $privateMatcher = $this->createStub(RouteMatcherInterface::class);
        $privateMatcher->method('matches')->willReturn(true);

        $routeAccessPolicy = new RouteAccessPolicy($publicMatcher, ['ROLE_USER' => $privateMatcher]);

        self::assertTrue($routeAccessPolicy->matches('dashboard', ['ROLE_USER']));
    }

    public function testMatchesReturnsFalseForPrivateRouteWithUnknownRole(): void
    {
        $publicMatcher = $this->createStub(RouteMatcherInterface::class);
        $publicMatcher->method('matches')->willReturn(false);

        $routeAccessPolicy = new RouteAccessPolicy($publicMatcher, []);

        self::assertFalse($routeAccessPolicy->matches('dashboard', ['ROLE_UNKNOWN']));
    }

    public function testMatchesReturnsFalseWithNoRoles(): void
    {
        $publicMatcher = $this->createStub(RouteMatcherInterface::class);
        $publicMatcher->method('matches')->willReturn(false);

        $routeAccessPolicy = new RouteAccessPolicy($publicMatcher, []);

        self::assertFalse($routeAccessPolicy->matches('dashboard', []));
    }
}
