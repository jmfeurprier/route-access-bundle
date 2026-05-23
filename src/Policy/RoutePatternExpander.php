<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Policy;

/**
 * Converts a wildcard pattern to a regex pattern:
 * - replaces "*" with ".*";
 * - a dot (".") remains a literal dot.
 *
 * Examples:
 * - "foo.bar" becomes "/^foo\.bar$/"
 * - "foo.*" becomes "/^foo\..*$/"
 */
readonly class RoutePatternExpander
{
    private const string DELIMITER = '/';

    /**
     * @return non-empty-string
     */
    public function expand(string $pattern): string
    {
        $tokens        = explode('*', $pattern);
        $escapedTokens = [];

        foreach ($tokens as $token) {
            $escapedTokens[] = preg_quote($token, self::DELIMITER);
        }

        return self::DELIMITER . '^' . implode('.*', $escapedTokens) . '$' . self::DELIMITER;
    }
}
