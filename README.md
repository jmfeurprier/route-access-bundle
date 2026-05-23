# jmf/route-access-bundle

A Symfony bundle that restricts route access based on user roles, configured via YAML. Define which roles may access which routes — including wildcard patterns — without touching individual controllers.

## Requirements

- PHP 8.3+
- Symfony 7.0 or 8.0

## Installation

```bash
composer require jmf/route-access-bundle
```

Register the bundle in `config/bundles.php` if not using Symfony Flex:

```php
return [
    // ...
    Jmf\RouteAccess\JmfRouteAccessBundle::class => ['all' => true],
];
```

## Quick Start

Create `config/packages/jmf_route_access.yaml`:

```yaml
jmf_route_access:

    policy:
        public_routes:
            - 'app_login'
            - 'app_logout'

        roles:
            ROLE_USER:
                - 'app_home'
                - 'post.index'
                - 'post.read'

            ROLE_ADMIN:
                - '*'
```

That's it — the bundle automatically enforces access on every request. Unauthenticated or unauthorized users receive an `AccessDeniedException`.

## Configuration Reference

```yaml
jmf_route_access:

    policy:

        # Routes that are always accessible, regardless of authentication or roles.
        # Supports wildcard patterns: "*" matches any sequence of characters.
        # Default: ["*"] (all routes are public). Override to restrict access.
        public_routes:
            - 'app_login'
            - 'app_logout'
            - '_preview_error'  # Error preview
            - '_profiler*'      # Symfony profiler
            - '_wdt*'           # Web Debug Toolbar

        # Map each Symfony role to the list of route names (or patterns) it may access.
        # Role hierarchy is respected: a role inherits the routes of all roles below it.
        # Use "*" as the sole entry to grant access to all routes.
        roles:

            ROLE_USER:
                - 'app_home'
                - 'app_profile'
                - 'app_profile_edit'
                - 'post.index'
                - 'post.read'
                - 'comment.create'

            ROLE_EDITOR:
                - 'post.create'
                - 'post.update'
                - 'post.delete'
                - 'comment.delete'
                - 'media.*'         # Grants access to all routes matching "media.*"

            ROLE_ADMIN:
                - '*'               # Grants access to all routes

    # Optional prefix for Twig functions registered by this bundle.
    # Default: '' (no prefix). Example: 'jmf_' registers 'jmf_can_access_route'.
    twig_functions_prefix: ''
```

A sample configuration file is also available in the [`samples/`](samples/) directory.

## Wildcard Patterns

Route names and patterns support `*` as a wildcard that matches any sequence of characters:

| Pattern      | Matches                                  |
|--------------|------------------------------------------|
| `post.read`  | `post.read` only                         |
| `post.*`     | `post.index`, `post.read`, `post.create` |
| `_profiler*` | `_profiler`, `_profiler_toolbar`, …      |
| `*`          | Any route                                |

## Role Hierarchy

Symfony's role hierarchy is fully respected. If `ROLE_ADMIN` inherits `ROLE_EDITOR` in `security.yaml`, an admin user automatically has access to all routes granted to `ROLE_EDITOR` without any duplication in the bundle configuration.

## How It Works

1. **`RouteAccessSubscriber`** listens on `kernel.controller` and calls Symfony's `AuthorizationCheckerInterface` for every main request.
2. **`RouteAccessVoter`** resolves the current user's reachable roles (via role hierarchy) and checks whether any of them grants access to the requested route.
3. If access is denied, an `AccessDeniedException` is thrown, which Symfony handles according to your firewall configuration (redirect to login, return 403, etc.).

## Programmatic Access Check

Use `RouteAccessGuardInterface` to check route access from your own code:

```php
use Jmf\RouteAccess\Guard\RouteAccessGuardInterface;

class MyService
{
    public function __construct(
        private readonly RouteAccessGuardInterface $routeAccessGuard,
    ) {}

    public function canEdit(): bool
    {
        return $this->routeAccessGuard->canAccessRoute('post.update');
    }
}
```

## License

MIT
