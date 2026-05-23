<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Matching;

use Override;
use Webmozart\Assert\Assert;

readonly class RouteMatcher implements RouteMatcherInterface
{
    /**
     * @param non-empty-string $pattern
     */
    public function __construct(
        private string $pattern,
    ) {
        Assert::stringNotEmpty($pattern);
    }

    #[Override]
    public function matches(string $routeName): bool
    {
        return (1 === preg_match($this->pattern, $routeName));
    }
}
