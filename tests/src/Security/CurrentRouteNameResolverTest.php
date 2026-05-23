<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Security;

use Jmf\RouteAccess\Security\CurrentRouteNameResolver;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CurrentRouteNameResolverTest extends TestCase
{
    private CurrentRouteNameResolver $currentRouteNameResolver;

    #[Override]
    protected function setUp(): void
    {
        $this->currentRouteNameResolver = new CurrentRouteNameResolver();
    }

    public function testTryResolveReturnsNullForSubRequest(): void
    {
        $controllerEvent = $this->buildEvent(new Request(), HttpKernelInterface::SUB_REQUEST);

        self::assertNull($this->currentRouteNameResolver->tryResolve($controllerEvent));
    }

    public function testTryResolveReturnsNullWhenRouteNotSet(): void
    {
        $controllerEvent = $this->buildEvent(new Request(), HttpKernelInterface::MAIN_REQUEST);

        self::assertNull($this->currentRouteNameResolver->tryResolve($controllerEvent));
    }

    public function testTryResolveReturnsRouteName(): void
    {
        $request = new Request();
        $request->attributes->set('_route', 'my_route');

        $controllerEvent = $this->buildEvent($request, HttpKernelInterface::MAIN_REQUEST);

        self::assertSame('my_route', $this->currentRouteNameResolver->tryResolve($controllerEvent));
    }

    private function buildEvent(
        Request $request,
        int $requestType,
    ): ControllerEvent {
        return new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            static fn(): string => '',
            $request,
            $requestType,
        );
    }
}
