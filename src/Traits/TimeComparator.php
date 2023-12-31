<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 28.12.2023 Time: 10:35
 */


namespace Chronos\Traits;

use Chronos\Chronos;
use Chronos\Exceptions\ChronosException;
use Chronos\TimeUnitTranslator;
use DateTimeImmutable;
use MultiLanguage\LanguageException;
use DateTimeInterface;
use DateInterval;

trait TimeComparator
{
    /**
     * @return bool
     */
    public function isWeekday(): bool
    {
        return $this->format('N') < 6;
    }

    /**
     * @return bool
     */
    public function isWeekend(): bool
    {
        return $this->format('N') >= 6;
    }

    /**
     * Get the difference between the current date and time and another DateTimeImmutable instance.
     * @param Chronos|DateTimeInterface $targetDateTime
     * @return DateInterval|false
     */
    public function diff(Chronos|DateTimeInterface $targetDateTime): DateInterval|false
    {
        if ($targetDateTime instanceof Chronos)
            $targetDateTime = $targetDateTime->getTime();
        return $this->getTime()->diff($targetDateTime);
    }

    /**
     * @param string $time
     * @param string|null $format
     * @param string|null $timezone
     * @return DateInterval|false
     */
    public function diffFromFormat(string $time, ?string $format = null, ?string $timezone = null): DateInterval|false
    {
        $targetDateTime = static::createFromFormat($time, $format, $timezone);
        return $this->diff($targetDateTime);
    }

    /**
     * @param Chronos|DateTimeInterface $targetDateTime
     * @return false|int
     */
    public function dayDiff(Chronos|DateTimeInterface $targetDateTime): false|int
    {
        if ($targetDateTime instanceof Chronos)
            $targetDateTime = $targetDateTime->getTime();
        return $this->diff($targetDateTime)->days;
    }

    /**
     * Check if the current date and time is in the past from instance or now.
     * @param Chronos|DateTimeImmutable|null $targetDateTime default static::now()
     * @return bool True if the current date and time is in the past, false otherwise.
     */
    public function isPast(Chronos|DateTimeImmutable|null $targetDateTime = null): bool
    {
        $targetDateTime = $targetDateTime ?: static::now();
        $diff = $this->diff($targetDateTime);
        return !$diff->invert && (
                $diff->y !== 0 ||
                $diff->m !== 0 ||
                $diff->d !== 0 ||
                $diff->h !== 0 ||
                $diff->i !== 0 ||
                $diff->s !== 0);
    }

    /**
     * Check if the current date and time is in the future from instance or now.
     * @param Chronos|DateTimeImmutable|null $targetDateTime default static::now()
     * @return bool True if the current date and time is in the future, false otherwise.
     */
    public function isFuture(Chronos|DateTimeImmutable|null $targetDateTime = null): bool
    {
        $targetDateTime = $targetDateTime ?: static::now();
        $diff = $this->diff($targetDateTime);
        return (bool)$diff->invert;
    }

    /**
     * Check if the current date and time is the same day as another DateTimeImmutable instance.
     * @param Chronos|DateTimeImmutable $targetDateTime The target object
     * @return bool True if the dates are on the same day, false otherwise.
     */
    public function isSameDay(Chronos|DateTimeImmutable $targetDateTime): bool
    {
        $diff = $this->diff($targetDateTime);
        return !$diff->invert && (
                $diff->y === 0 ||
                $diff->m === 0 ||
                $diff->d === 0);
    }

    /**
     * @return int
     */
    public function age(): int
    {
        return static::isFuture() ? 0 : $this->diff(static::now())->y;
    }

    /**
     * @param string|null $lang
     * @param int $depth
     * @param Chronos|DateTimeInterface|null $targetDateTime
     * @return false|string
     * @throws LanguageException|ChronosException
     */
    public function prettyDiff(?string $lang = "en", int $depth = 0, Chronos|DateTimeInterface|null $targetDateTime = null): false|string
    {
        if ($depth < 0)
            throw new ChronosException("depth cannot be lower than 0.");
        TimeUnitTranslator::initialize();
        $targetDateTime = $targetDateTime ?: static::now();

        $diff = $this->diff($targetDateTime);
        if (!$diff)
            return false;

        $formats = [
            "y" => "year",
            "m" => "month",
            "d" => "day",
            "h" => "hour",
            "i" => "minute",
            "s" => "second",
        ];

        $timeParts = [];
        $level = 0;
        foreach ($formats as $key => $unit) {
            if ($diff->{$key} && (int)$diff->{$key} > 0) {
                if ($level++ >= ($depth === 0 ? $depth + 1 : $depth))
                    break;
                // check if unit is plural
                $unit = (int)$diff->{$key} === 1 ? $unit : $unit . "s";
                // add translated unit into an array
                $timeParts[] = TimeUnitTranslator::translate($unit, $lang, ["#$unit#" => $diff->{$key}]);
            }
        }
        // implode array to get string like => x year y month
        $fullTimeString = implode(' ', $timeParts);

        $adverb = $diff->invert === 0 ? 'ago' : 'later';

        // if string is empty then there is no diff
        $adverb = empty($fullTimeString) ? 'same-time' : $adverb;

        if ($depth === 0) {
            $dayDiff = $diff->days;
            if ($dayDiff === 0) {
                // if there is no day diff then assume 1m59secs => now
                $adverb = $diff->h < 1 && $diff->i <= 1 && $diff->s <= 59 ? 'now' : 'today';
            } elseif ($dayDiff === 1) {
                $adverb = $diff->invert === 0 ? 'yesterday' : 'tomorrow';
            }
        }
        return TimeUnitTranslator::translate($adverb, $lang, ["#time#" => $fullTimeString]);
    }
}