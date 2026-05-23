<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Security;

use Jmf\RouteAccess\Policy\RouteAccessPolicy;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Webmozart\Assert\Assert;

/**
 * Supports wildcard patterns (e.g., "user.*", "*.delete") where "*" matches any characters.
 * Respects Symfony's role hierarchy (e.g., ROLE_ADMIN inherits ROLE_EDITOR and ROLE_USER permissions).
 *
 * @extends Voter<string, string>
 */
class RouteAccessVoter extends Voter
{
    public function __construct(
        private readonly RouteAccessPolicy $routeAccessPolicy,
        private readonly CurrentUserRolesResolver $currentUserRolesResolver,
    ) {
    }

    #[Override]
    protected function supports(
        string $attribute,
        mixed $subject,
    ): bool {
        return (RouteAccess::ATTRIBUTE === $attribute) && is_string($subject);
    }

    #[Override]
    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        Assert::stringNotEmpty($subject);

        $routeName = $subject;

        return $this->routeAccessPolicy->matches(
            $routeName,
            $this->currentUserRolesResolver->resolve($token),
        );
    }
}
