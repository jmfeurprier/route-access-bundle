<?php

declare(strict_types=1);

namespace Jmf\RouteAccess\Tests\Policy;

use Jmf\RouteAccess\Policy\RoutePatternExpander;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RoutePatternExpanderTest extends TestCase
{
    private RoutePatternExpander $routePatternExpander;

    #[Override]
    protected function setUp(): void
    {
        $this->routePatternExpander = new RoutePatternExpander();
    }

    /**
     * @return array{0: string, 1: non-empty-string}[]
     */
    public static function dataProvider(): iterable
    {
        return [
            // Simple patterns without wildcards
            [
                'foo.bar',
                '/^foo\.bar$/',
            ],
            [
                'app.user.list',
                '/^app\.user\.list$/',
            ],
            // Patterns with wildcards at the end
            [
                'foo.*',
                '/^foo\..*$/',
            ],
            [
                'app.user.*',
                '/^app\.user\..*$/',
            ],
            // Patterns with wildcards at the beginning
            [
                '*.bar',
                '/^.*\.bar$/',
            ],
            // Patterns with wildcards in the middle
            [
                'foo.*.baz',
                '/^foo\..*\.baz$/',
            ],
            // Patterns with multiple wildcards
            [
                'foo.*.*.baz',
                '/^foo\..*\..*\.baz$/',
            ],
            [
                '*.*',
                '/^.*\..*$/',
            ],
            // Only wildcard
            [
                '*',
                '/^.*$/',
            ],
            // Special regex characters that need escaping
            [
                'foo.bar+baz',
                '/^foo\.bar\+baz$/',
            ],
            [
                'foo.bar(test)',
                '/^foo\.bar\(test\)$/',
            ],
            [
                'foo.bar[test]',
                '/^foo\.bar\[test\]$/',
            ],
            [
                '',
                '/^$/',
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testExpand(string $pattern, string $expected): void
    {
        $result = $this->routePatternExpander->expand($pattern);

        self::assertSame($expected, $result);
    }
}
