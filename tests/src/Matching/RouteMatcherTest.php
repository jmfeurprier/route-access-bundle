<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Matching;

use Jmf\RouteAccess\Matching\RouteMatcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RouteMatcherTest extends TestCase
{
    /**
     * @return iterable<array{0: non-empty-string, 1: string, 2: bool}>
     */
    public static function dataProviderMatches(): iterable
    {
        return [
            [
                '/^foo\.bar$/',
                'foo.bar',
                true,
            ],
            [
                '/^foo\.bar$/',
                'foo.baz',
                false,
            ],
            [
                '/^foo\..*$/',
                'foo.bar',
                true,
            ],
            [
                '/^foo\..*$/',
                'foo.bar.baz',
                true,
            ],
            [
                '/^foo\..*$/',
                'bar.foo',
                false,
            ],
            [
                '/^.*$/',
                'anything',
                true,
            ],
            [
                '/^.*$/',
                '',
                true,
            ],
        ];
    }

    /**
     * @param non-empty-string $pattern
     */
    #[DataProvider('dataProviderMatches')]
    public function testMatches(
        string $pattern,
        string $routeName,
        bool $expected,
    ): void {
        $routeMatcher = new RouteMatcher($pattern);

        self::assertSame($expected, $routeMatcher->matches($routeName));
    }
}
