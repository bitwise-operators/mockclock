<?php

declare(strict_types=1);

namespace Bitwise;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;
use Override;
use Psr\Clock\ClockInterface;
use RuntimeException;

use function round;
use function sprintf;

/**
 * @api
 */
class MockClock implements ClockInterface
{
    private DateTimeImmutable $now;

    public function __construct(
    ) {
        $this->now = new DateTimeImmutable();
    }

    /**
     * Advances the clock by the supplied DateInterval value
     *
     * @return $this
     */
    public function add(
        DateInterval $interval,
    ): static {
        $this->now = $this->now->add($interval);

        return $this;
    }

    /**
     * Modifies the clock according to the supplied string modifier (e.g. '+42 seconds')
     *
     * @return $this
     */
    public function modify(
        string $modifier,
    ): static {
        $this->now = $this->now->modify($modifier);

        return $this;
    }

    #[Override]
    public function now(
    ): DateTimeImmutable {
        return clone $this->now;
    }

    /**
     * Sets the clock to the supplied value
     *
     * @return $this
     */
    public function set(
        DateTimeInterface $date,
    ): static {
        $this->now = DateTimeImmutable::createFromInterface($date);

        return $this;
    }

    /**
     * Changes the timezone of the clock
     *
     * @param DateTimeZone | non-empty-string $timezone
     * @return $this
     */
    public function setTimezone(
        DateTimeZone | string $timezone,
    ): static {
        if (!$timezone instanceof DateTimeZone) {
            $timezone = new DateTimeZone($timezone);
        }

        $this->now = $this->now->setTimezone($timezone);

        return $this;
    }

    /**
     * Increases the clock by the applied amount of seconds
     *
     * @phpstan-assert float | positive-int $seconds
     * @psalm-assert float | positive-int $seconds
     * @return $this
     */
    public function sleep(
        float | int $seconds,
    ): static {
        if ($seconds <= 0) {
            throw new LogicException("Unable to sleep for a negative period");
        }

        $sleepInterval = DateInterval::createFromDateString(sprintf('%.0f microseconds', round((float) $seconds * 1000000.0)))
            ?: throw new RuntimeException();

        return $this->add($sleepInterval);
    }

    /**
     * Sets the clock to midnight of the same day
     *
     * @return $this
     */
    public function startOfDay(
    ): static {
        $this->now = $this->now->setTime(hour: 0, minute: 0, second: 0, microsecond: 0);

        return $this;
    }

    /**
     * Set the clock back by the supplied DateInterval value
     *
     * @return $this
     */
    public function sub(
        DateInterval $interval,
    ): static {
        $this->now = $this->now->sub($interval);

        return $this;
    }
}
