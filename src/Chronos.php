<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 28.12.2023 Time: 07:41
 */

namespace Chronos;

use Chronos\Exceptions\ChronosException;
use Chronos\Traits\TimeManipulator;
use Chronos\Traits\TimeGenerator;
use Chronos\Traits\TimeComparator;
use DateTimeImmutable;
use DateTimeZone;
use MultiLanguage\LanguageException;

class Chronos
{
    use TimeManipulator;
    use TimeGenerator;
    use TimeComparator;

    protected static array $instances = [];
    protected DateTimeImmutable $dateTimeImmutable;
    protected static ?string $defaultFormat = null;
    protected static ?string $defaultTimeZone = null;


    public function __construct(?DateTimeImmutable $dateTimeImmutable = null)
    {
        $this->setTime($dateTimeImmutable ?? new DateTimeImmutable());
    }

    /**
     * @return static
     * @throws \Exception
     */
    protected static function getInstance(): static
    {
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            static::$instances[$cls] = new static();
        }
        return static::$instances[$cls];
    }


    /**
     * @return DateTimeImmutable
     */
    public function getTime(): DateTimeImmutable
    {
        return $this->dateTimeImmutable;
    }


    /**
     * @param DateTimeImmutable $dateTimeImmutable
     * @return void
     */
    public function setTime(DateTimeImmutable $dateTimeImmutable): void
    {
        $this->dateTimeImmutable = $dateTimeImmutable;
    }


    /**
     * @return string|null
     */
    public static function getDefaultFormat(): ?string
    {
        return static::$defaultFormat;
    }


    /**
     * @param string|null $defaultFormat
     * @return void
     * @throws ChronosException
     */
    public static function setDefaultFormat(?string $defaultFormat): void
    {
        if (!static::isValidFormat($defaultFormat))
            throw new ChronosException("$defaultFormat is not valid format.");
        static::$defaultFormat = $defaultFormat;
    }

    /**
     * @return string|null
     */
    public static function getDefaultTimeZone(): ?string
    {
        return static::$defaultTimeZone;
    }

    /**
     * @param string|null $defaultTimeZone
     * @param bool $triggerDateDefaultTimeZoneSet
     * @return void
     * @throws ChronosException
     */
    public static function setDefaultTimeZone(?string $defaultTimeZone, bool $triggerDateDefaultTimeZoneSet = false): void
    {
        if (is_null($defaultTimeZone))
            throw new ChronosException("Default timezone cannot be null.");
        if (!static::isValidTimeZone($defaultTimeZone))
            throw new ChronosException("$defaultTimeZone is not valid timezone.");
        if ($triggerDateDefaultTimeZoneSet && !date_default_timezone_set($defaultTimeZone))
            throw new ChronosException("Failed to set the default timezone to $defaultTimeZone.");
        static::$defaultTimeZone = $defaultTimeZone;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isTimestamp(mixed $value): bool
    {
        if (!empty($value) || is_int($value) || (is_string($value) && ctype_digit($value))) {
            $value = (int)$value;
            return ($value >= 0) && ($value <= PHP_INT_MAX) && (date('Y', $value) > 1970);
        }
        return false;
    }

    /**
     * @param string $value
     * @param string|null $format
     * @return bool
     */
    public static function isFormattable(string $value, ?string $format = null): bool
    {
        $format ?: static::getDefaultFormat();
        if (empty($format)) {
            return false;
        }

        // Attempt to create a DateTimeImmutable object from the string using the provided format
        $dateTime = @DateTimeImmutable::createFromFormat($format, $value);

        // Check if the DateTimeImmutable object was created successfully and the formatted result matches the input string
        return $dateTime instanceof DateTimeImmutable && $dateTime->format($format);
    }

    /**
     * @param string|null $format
     * @return bool
     */
    public static function isValidFormat(?string $format): bool
    {
        if (empty($format)) {
            return false;
        }
        // Create a date object and attempt to format it
        $date = new DateTimeImmutable();
        return $date instanceof DateTimeImmutable && $date->format($format);
    }

    /**
     * @param string|null $timezone
     * @return bool
     */
    public static function isValidTimeZone(?string $timezone = ""): bool
    {
        if (empty($timezone))
            return false;
        return in_array($timezone, DateTimeZone::listIdentifiers());
    }

    /**
     * @param string|null $format
     * @return string
     */
    public function format(?string $format = null): string
    {
        $format = $format ?: static::getDefaultFormat();
        return $this->getTime()->format($format);
    }

    /**
     * @return int
     */
    public function timestamp(): int
    {
        return $this->getTime()->getTimestamp();
    }

    public function second(): string
    {
        return $this->format('s');
    }

    public function minute(): string
    {
        return $this->format('i');
    }

    public function hour(): string
    {
        return $this->format('H');
    }

    /**
     * @return string
     */
    public function day(): string
    {
        return $this->format('d');
    }

    /**
     * @return string
     */
    public function month(): string
    {
        return $this->format('m');
    }

    /**
     * @return string
     */
    public function year(): string
    {
        return $this->format('Y');
    }

    /**
     * @param string|null $lang
     * @return string
     * @throws LanguageException
     */
    public function dayName(?string $lang = null): string
    {
        TimeUnitTranslator::initialize();
        $day = $this->format('l');
        return trim(TimeUnitTranslator::translate($day, $lang));
    }

    /**
     * @param string|null $lang
     * @return string
     * @throws LanguageException
     */
    public function monthName(?string $lang = null): string
    {
        TimeUnitTranslator::initialize();
        $month = $this->format('F');
        return trim(TimeUnitTranslator::translate($month, $lang, ['#days#' => '', '#years#' => '']));
    }

    /**
     * @param string $format
     * @return string
     */
    public function date(string $format = 'Y-m-d'): string
    {
        return $this->format($format);
    }

    /**
     * @param bool $includeSeconds
     * @return string
     */
    public function time(bool $includeSeconds = true): string
    {
        $format = 'H:i';
        $format .= $includeSeconds ? ':s' : false;
        return $this->format($format);
    }

    /**
     * @param string|null $lang
     * @param bool $includeYears
     * @return string
     * @throws LanguageException
     */
    public function prettyDatePrint(?string $lang = null, bool $includeYears = true): string
    {
        TimeUnitTranslator::initialize();
        $day = $this->day();
        $month = $this->format('F');
        $year = $includeYears ? $this->year() : '';
        return trim(TimeUnitTranslator::translate($month, $lang, ['#days#' => $day, '#years#' => $year]));
    }

    /**
     * @param string|null $lang
     * @param bool $includeYears
     * @param bool $includeSeconds
     * @return string
     * @throws LanguageException
     */
    public function prettyPrint(?string $lang = null, bool $includeYears = true, bool $includeSeconds = true): string
    {
        $prettyDate = $this->prettyDatePrint($lang, $includeYears);
        $time = $this->time($includeSeconds);
        return "{$prettyDate} {$time}";
    }
}