<?php

declare(strict_types=1);

namespace Tests\Unit;

use BitwiseOperators\MockClock;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Psr\Clock\ClockInterface;

use function sprintf;

#[CoversClass(MockClock::class)]
final class MockClockTest extends TestCase
{
    #[Test]
    #[TestDox('The clock implements the psr/clock interface')]
    public function the_clock_implements_the_psr_clock_interface(
    ): void {
        $clock = new MockClock();

        self::assertTrue($clock instanceof ClockInterface, sprintf('Failed asserting that %s is an instanceof %s', $clock::class, ClockInterface::class));
    }

    #[Test]
    public function now_returns_a_datetime_immutable_object(
    ): void {
        $clock = new MockClock();

        $now = $clock->now();

        self::assertIsObject($now);
        self::assertTrue($now instanceof DateTimeImmutable, sprintf('Failed asserting that %s is an instanceof %s', $now::class, DateTimeImmutable::class));
    }

    #[Test]
    public function the_clock_does_not_change_between_calls(
    ): void {
        $clock = new MockClock();

        $start = $clock->now();

        sleep(1);

        $end = $clock->now();

        self::assertEquals($start, $end);
    }

    #[Test]
    public function adding_time_increases_the_time_on_the_clock(
    ): void {
        $clock = new MockClock();

        $start = $clock->now();

        $interval = new DateInterval("PT10M");

        $clock->add(new DateInterval("PT10M"));

        $end = $clock->now();

        self::assertEquals($start->add($interval), $end);
    }

    #[Test]
    public function setting_time_time_changes_the_time_on_the_clock(
    ): void {
        $clock = new MockClock();

        $start = $clock->now();

        $interval = new DateInterval("PT10M");

        $clock->set($start->add($interval));

        $end = $clock->now();

        self::assertEquals($start->add($interval), $end);
        self::assertNotEquals($start, $end);
    }

    #[Test]
    public function modifying_the_time_changes_the_time_by_the_specified_amount(
    ): void {
        $clock = new MockClock();

        $start = $clock->now();

        $clock->modify('+10 minutes');

        $interval = new DateInterval("PT10M");

        $end = $clock->now();

        self::assertEquals($start->add($interval), $end);
    }

    #[DataProvider('dateTimeZoneProvider')]
    #[Test]
    public function setting_the_timezone_forces_the_clock_to_return_that_timezone(
        DateTimeZone $tz,
    ): void {
        $clock = new MockClock();

        $clock->setTimezone($tz);

        $now = $clock->now();

        self::assertEquals($tz, $now->getTimezone());
    }

    #[DataProvider('dateTimeZoneIdentifierProvider')]
    #[Test]
    public function setting_the_timezone_by_string_forces_the_clock_to_return_that_timezone(
        string $tz,
    ): void {
        $clock = new MockClock();

        $clock->setTimezone($tz);

        $now = $clock->now();

        self::assertEquals(new DateTimeZone($tz), $now->getTimezone());
    }

    #[Test]
    public function sleeping_advances_the_clock_by_the_specified_number_of_seconds(
    ): void {
        $sleepTime = 10;

        $clock = new MockClock();

        $start = $clock->now();

        $clock->sleep($sleepTime);

        $end = $clock->now();

        self::assertEquals($start->add(new DateInterval("PT{$sleepTime}S")), $end);
    }

    #[Test]
    public function setting_the_clock_to_start_of_day_sets_the_time_to_00_00_00(
    ): void {
        $clock = new MockClock();

        $clock->startOfDay();

        $now = $clock->now();

        self::assertEquals('00:00:00', $now->format('H:i:s'));
    }

    #[Test]
    public function substracting_time_turns_back_the_clock(
    ): void {
        $clock = new MockClock();

        $start = $clock->now();

        $interval = new DateInterval("PT10M");

        $clock->sub(new DateInterval("PT10M"));

        $end = $clock->now();

        self::assertEquals($start->sub($interval), $end);
    }

    /**
     * @return Generator<non-empty-string, list<DateTimeZone>>
     */
    public static function dateTimeZoneProvider(
    ): Generator {
        foreach (self::dateTimeZoneIdentifierProvider() as [$tzIdentifier]) {
            yield "Timezone {$tzIdentifier}" => [new DateTimeZone($tzIdentifier)];
        }
    }

    /**
     * @return Generator<non-empty-string, list<non-empty-string>>
     */
    public static function dateTimeZoneIdentifierProvider(
    ): Generator {
        foreach (DateTimeZone::listIdentifiers() as $tzIdentifier) {
            yield "Timezone identifier {$tzIdentifier}" => [$tzIdentifier];
        }
    }
}
