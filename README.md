# MockClock
A simple implementation of psr/clock to be used in unit testing.

## Usage
This class is intended to be used when testing classes that use `Psr\Clock\ClockInterface` through dependency injection.

In your unit tests, create an instance of the clock and pass it to the constructor of the class under test. You can then alter the clock as desired,
and check if the output of your class matches expectation.

Unless the clock is actively modified, the time on the clock does not change after instantiation. This way, repeated calls to the class under test should be idempotent (as far as time is concerned).

### Example
```php
#[\PHPUnit\Framework\Attributes\Test]
public function test_the_clock(
): void {
    $clock = new \BitwiseOperators\MockClock();

    $myClass = new MyClass(clock: $clock);

    $firstValue = $myClass->myMethod();

    $clock->sleep(50);

    $secondValue = $myClass->myMethod();

    self::assertNotEquals($firstValue, $secondValue);
}
```

### Available methods
The following methods are available to on the clock:

#### `now(): DateTimeImmutable`
See https://www.php-fig.org/psr/psr-20/

#### `add(DateTimeInterval): static`
Increments the time on the clock by the specified amount.

#### `modify(string): static`
Modifies the clock according to the formats described in the [PHP manual](https://www.php.net/manual/en/datetime.formats.php).

#### `set(DateTimeInterface): static`
Sets the the clock to the specified date.

#### `setTimezone(DateTimeZone | string): static`
Set the timezone of the clock to the specified timezone.

Note that a subsequent call to either `set()` or `modify()` may override this.

#### `sleep(float | int): static`
Advances the clock the specified number of seconds

#### `startOfDay(): static`
Set the time on the clock to "00:00:00". The date is unaffected.

#### `sub(DateTimeInterval): static`
Turns back the time on the clock by the specified amount.

### Chaining
Aside from the `now()` method, all methods return the clock itself, allowing for chaining:
```php
$clock = new \BitwiseOperators\MockClock()
    ->set(new \DateTime('1995-06-08'))
    ->startOfDay()
    ->add(new \DateTimeInterval('PT1H'))
    ->setTimezone('Europe/Copenhagen')
;
```

### Extending
The MockClock is designed to be extendable. Simply create your own Clock class that extends the MockClock and add any extra methods you might need:
```php
namespace Tests\Support;

class MyMockClock extends \BitwiseOperators\MockClock {
    public function setToEpoch(): static {
        return $this->set(new \DateTime('1970-01-01T00:00:00'));
    }
}
```
Even if you don't add methods immediately, extending the class and using your own is always a good idea as a form of [dependency inversion](https://en.wikipedia.org/wiki/Dependency_inversion_principle).
