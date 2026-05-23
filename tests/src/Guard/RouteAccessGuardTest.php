<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Guard;

use Jmf\RouteAccess\Guard\RouteAccessGuard;
use Jmf\RouteAccess\Security\RouteAccess;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class RouteAccessGuardTest extends TestCase
{
    private AuthorizationCheckerInterface & MockObject $authorizationChecker;

    private RouteAccessGuard $routeAccessGuard;

    #[Override]
    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);

        $this->routeAccessGuard = new RouteAccessGuard($this->authorizationChecker);
    }

    public function testCanAccessRouteReturnsTrueWhenGranted(): void
    {
        $this->authorizationChecker
            ->expects(self::once())
            ->method('isGranted')
            ->with(RouteAccess::ATTRIBUTE, 'my_route')
            ->willReturn(true)
        ;

        self::assertTrue($this->routeAccessGuard->canAccessRoute('my_route'));
    }

    public function testCanAccessRouteReturnsFalseWhenDenied(): void
    {
        $this->authorizationChecker
            ->expects(self::once())
            ->method('isGranted')
            ->with(RouteAccess::ATTRIBUTE, 'my_route')
            ->willReturn(false)
        ;

        self::assertFalse($this->routeAccessGuard->canAccessRoute('my_route'));
    }
}
