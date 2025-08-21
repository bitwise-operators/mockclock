<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static function assertEqualsStrict(
        mixed $expected,
        mixed $actual,
        string $message = '',
    ): void {
        $constraint = new IsIdentical($expected);

        self::assertThat($actual, $constraint, $message);
    }
}
