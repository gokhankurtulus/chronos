<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 28.12.2023 Time: 08:12
 */


namespace Chronos\Traits;

use Chronos\Exceptions\ChronosException;
use DateTimeImmutable;
use DateTimeZone;

trait TimeGenerator
{
    /**
     * Get the current Chronos instance.
     * @param string|null $timezone
     * @return static The current Chronos instance.
     * @throws \Exception
     */
    public static function now(?string $timezone = null): static
    {
        $timezone = $timezone ?: static::getDefaultTimeZone() ?: date_default_timezone_get();
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("$timezone is not valid timezone.");
        return new static(static::getInstance()->getTime()->setTimezone(new DateTimeZone($timezone)));
    }

    /**
     * Get a Chronos instance representing yesterday.
     * @param string|null $timezone
     * @return static A Chronos instance representing yesterday.
     * @throws ChronosException
     * @throws \Exception
     */
    public static function yesterday(?string $timezone = null): static
    {
        $timezone = $timezone ?: static::getDefaultTimeZone() ?: date_default_timezone_get();
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("$timezone is not valid timezone.");
        return new static(static::getInstance()->subDays(1)->getTime()->setTimezone(new DateTimeZone($timezone)));
    }

    /**
     * Get a Chronos instance representing tomorrow.
     * @param string|null $timezone
     * @return static A Chronos instance representing tomorrow.
     * @throws ChronosException
     * @throws \Exception
     */
    public static function tomorrow(?string $timezone = null): static
    {
        $timezone = $timezone ?: static::getDefaultTimeZone() ?: date_default_timezone_get();
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("$timezone is not valid timezone.");
        return new static(static::getInstance()->addDays(1)->getTime()->setTimezone(new DateTimeZone($timezone)));
    }

    /**
     * @param string $time
     * @param string|null $format
     * @param string|null $timezone
     * @return static
     * @throws ChronosException
     * @throws \Exception
     */
    public static function createFromFormat(string $time, ?string $format = null, ?string $timezone = null): static
    {
        $format = $format ?: static::getDefaultFormat();
        $timezone = $timezone ?: static::getDefaultTimeZone() ?: date_default_timezone_get();

        if (!static::isValidFormat($format))
            throw new ChronosException("'$format' is not valid format.");
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("'$timezone' is not valid timezone.");

        $dateTime = @DateTimeImmutable::createFromFormat($format, $time, new DateTimeZone($timezone));
        if (!$dateTime instanceof DateTimeImmutable)
            throw new ChronosException("Failed to create DateTimeImmutable from format.");

        return new static($dateTime);
    }


    /**
     * @param int $timestamp
     * @param string|null $timezone
     * @return static
     * @throws ChronosException
     * @throws \Exception
     */
    public static function createFromTimestamp(int $timestamp, ?string $timezone = null): static
    {
        $timezone = $timezone ?: static::getDefaultTimeZone() ?: date_default_timezone_get();

        if (!static::isTimestamp($timestamp))
            throw new ChronosException("$timezone is not valid timestamp.");
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("$timezone is not valid timezone.");

        $dateTime = @DateTimeImmutable::createFromFormat('U', $timestamp)->setTimezone(new DateTimeZone($timezone));
        if (!$dateTime instanceof DateTimeImmutable)
            throw new ChronosException("Failed to create DateTimeImmutable from timestamp.");

        return new static($dateTime);
    }
}