<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\Assert\Assert;

readonly class CurrentUserRolesResolver
{
    public function __construct(
        private RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    /**
     * @return non-empty-string[]
     */
    public function resolve(TokenInterface $token): iterable
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return [];
        }

        $roles = $this->roleHierarchy->getReachableRoleNames($user->getRoles());

        Assert::allStringNotEmpty($roles);

        return $roles;
    }
}
