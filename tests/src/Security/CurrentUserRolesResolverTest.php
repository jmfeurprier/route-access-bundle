<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Security;

use Jmf\RouteAccess\Security\CurrentUserRolesResolver;
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CurrentUserRolesResolverTest extends TestCase
{
    private RoleHierarchyInterface & MockObject $roleHierarchy;

    private CurrentUserRolesResolver $currentUserRolesResolver;

    #[Override]
    protected function setUp(): void
    {
        $this->roleHierarchy = $this->createMock(RoleHierarchyInterface::class);

        $this->currentUserRolesResolver = new CurrentUserRolesResolver(
            $this->roleHierarchy,
        );
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testResolveReturnsEmptyArrayWhenNoUser(): void
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        self::assertSame([], $this->currentUserRolesResolver->resolve($token));
    }

    public function testResolveReturnsReachableRoles(): void
    {
        $user = $this->createStub(UserInterface::class);
        $user->method('getRoles')->willReturn(['ROLE_EDITOR']);

        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $this->roleHierarchy
            ->expects(self::once())
            ->method('getReachableRoleNames')
            ->with(['ROLE_EDITOR'])
            ->willReturn(
                [
                    'ROLE_EDITOR',
                    'ROLE_USER',
                ],
            )
        ;

        self::assertSame(
            [
                'ROLE_EDITOR',
                'ROLE_USER',
            ],
            $this->currentUserRolesResolver->resolve($token),
        );
    }
}
