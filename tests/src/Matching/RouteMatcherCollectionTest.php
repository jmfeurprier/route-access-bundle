<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Matching;

use Jmf\RouteAccess\Matching\RouteMatcher;
use Jmf\RouteAccess\Matching\RouteMatcherCollection;
use PHPUnit\Framework\TestCase;

final class RouteMatcherCollectionTest extends TestCase
{
    public function testMatchesReturnsFalseForEmptyCollection(): void
    {
        $routeMatcherCollection = new RouteMatcherCollection([]);

        self::assertFalse($routeMatcherCollection->matches('foo.bar'));
    }

    public function testMatchesReturnsTrueWhenOneMatcherMatches(): void
    {
        $routeMatcherCollection = new RouteMatcherCollection(
            [
                new RouteMatcher('/^foo\.bar$/'),
                new RouteMatcher('/^baz\.qux$/'),
            ],
        );

        self::assertTrue($routeMatcherCollection->matches('foo.bar'));
    }

    public function testMatchesReturnsFalseWhenNoMatcherMatches(): void
    {
        $routeMatcherCollection = new RouteMatcherCollection(
            [
                new RouteMatcher('/^foo\.bar$/'),
                new RouteMatcher('/^baz\.qux$/'),
            ],
        );

        self::assertFalse($routeMatcherCollection->matches('not.matching'));
    }

    public function testMatchesStopsAtFirstMatch(): void
    {
        $routeMatcherCollection = new RouteMatcherCollection(
            [
                new RouteMatcher('/^foo\.bar$/'),
                new RouteMatcher('/^.*$/'),
            ],
        );

        self::assertTrue($routeMatcherCollection->matches('anything'));
    }
}
