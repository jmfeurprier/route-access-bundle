<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Security;

use Jmf\RouteAccess\Matching\RouteMatcher;
use Jmf\RouteAccess\Matching\RouteMatcherCollection;
use Jmf\RouteAccess\Policy\RouteAccessPolicy;
use Jmf\RouteAccess\Security\CurrentUserRolesResolver;
use Jmf\RouteAccess\Security\RouteAccess;
use Jmf\RouteAccess\Security\RouteAccessVoter;
use Override;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class RouteAccessVoterTest extends TestCase
{
    private CurrentUserRolesResolver & Stub $currentUserRolesResolver;

    private TokenInterface & Stub $token;

    private RouteAccessVoter $routeAccessVoter;

    #[Override]
    protected function setUp(): void
    {
        $routeAccessPolicy = new RouteAccessPolicy(
            new RouteMatcherCollection(
                [
                    new RouteMatcher('/^app_login$/'),
                    new RouteMatcher('/^_profiler.*$/'),
                ],
            ),
            [
                'ROLE_USER' => new RouteMatcherCollection(
                    [
                        new RouteMatcher('/^dashboard$/'),
                        new RouteMatcher('/^post\..*$/'),
                    ],
                ),
            ],
        );

        $this->currentUserRolesResolver = $this->createStub(CurrentUserRolesResolver::class);
        $this->token                    = $this->createStub(TokenInterface::class);

        $this->routeAccessVoter = new RouteAccessVoter(
            $routeAccessPolicy,
            $this->currentUserRolesResolver,
        );
    }

    public function testVoteAbstainsForWrongAttribute(): void
    {
        self::assertSame(
            Voter::ACCESS_ABSTAIN,
            $this->routeAccessVoter->vote($this->token, 'dashboard', ['WRONG_ATTRIBUTE']),
        );
    }

    public function testVoteAbstainsForNonStringSubject(): void
    {
        self::assertSame(
            Voter::ACCESS_ABSTAIN,
            $this->routeAccessVoter->vote($this->token, 123, [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteGrantsAccessForPublicRoute(): void
    {
        self::assertSame(
            Voter::ACCESS_GRANTED,
            $this->routeAccessVoter->vote($this->token, 'app_login', [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteGrantsAccessForPublicRouteMatchingWildcard(): void
    {
        self::assertSame(
            Voter::ACCESS_GRANTED,
            $this->routeAccessVoter->vote($this->token, '_profiler_toolbar', [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteGrantsAccessWhenRoleMatchesRoute(): void
    {
        $this->currentUserRolesResolver->method('resolve')->willReturn(['ROLE_USER']);

        self::assertSame(
            Voter::ACCESS_GRANTED,
            $this->routeAccessVoter->vote($this->token, 'dashboard', [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteGrantsAccessWhenRoleMatchesWildcardRoute(): void
    {
        $this->currentUserRolesResolver->method('resolve')->willReturn(['ROLE_USER']);

        self::assertSame(
            Voter::ACCESS_GRANTED,
            $this->routeAccessVoter->vote($this->token, 'post.index', [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteDeniesAccessWhenNoRoleMatchesRoute(): void
    {
        $this->currentUserRolesResolver->method('resolve')->willReturn(['ROLE_USER']);

        self::assertSame(
            Voter::ACCESS_DENIED,
            $this->routeAccessVoter->vote($this->token, 'admin.dashboard', [RouteAccess::ATTRIBUTE]),
        );
    }

    public function testVoteDeniesAccessWhenUserHasNoRoles(): void
    {
        $this->currentUserRolesResolver->method('resolve')->willReturn([]);

        self::assertSame(
            Voter::ACCESS_DENIED,
            $this->routeAccessVoter->vote($this->token, 'dashboard', [RouteAccess::ATTRIBUTE]),
        );
    }
}
