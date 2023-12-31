<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 28.12.2023 Time: 08:06
 */

namespace Chronos\Traits;

use Chronos\Exceptions\ChronosException;
use DateTimeZone;

trait TimeManipulator
{
    /**
     * @param string $timezone
     * @return static
     * @throws ChronosException
     * @throws \Exception
     */
    public function toTimeZone(string $timezone): static
    {
        if (!static::isValidTimeZone($timezone))
            throw new ChronosException("$timezone is not valid timezone.");
        return new static($this->getTime()->setTimezone(new DateTimeZone($timezone)));
    }

    /**
     * Add a specified number of years to the current date and time.
     *
     * @param int $years The number of years to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addYears(int $years = 1): static
    {
        return new static($this->getTime()->modify("+$years year"));
    }

    /**
     * Subtract a specified number of years from the current date and time.
     *
     * @param int $years The number of years to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subYears(int $years = 1): static
    {
        return new static($this->getTime()->modify("-$years year"));
    }

    /**
     * Add a specified number of months to the current date and time.
     *
     * @param int $months The number of months to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addMonths(int $months = 1): static
    {
        return new static($this->getTime()->modify("+$months month"));
    }

    /**
     * Subtract a specified number of months from the current date and time.
     *
     * @param int $months The number of months to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subMonths(int $months = 1): static
    {
        return new static($this->getTime()->modify("-$months month"));
    }

    /**
     * Add a specified number of days to the current date and time.
     *
     * @param int $days The number of days to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addDays(int $days = 1): static
    {
        return new static($this->getTime()->modify("+$days day"));
    }

    /**
     * Subtract a specified number of days from the current date and time.
     *
     * @param int $days The number of days to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subDays(int $days = 1): static
    {
        return new static($this->getTime()->modify("-$days day"));
    }

    /**
     * Add a specified number of hours to the current date and time.
     *
     * @param int $hours The number of hours to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addHours(int $hours = 1): static
    {
        return new static($this->getTime()->modify("+$hours hour"));
    }

    /**
     * Subtract a specified number of hours from the current date and time.
     *
     * @param int $hours The number of hours to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subHours(int $hours = 1): static
    {
        return new static($this->getTime()->modify("-$hours hour"));
    }

    /**
     * Add a specified number of minutes to the current date and time.
     *
     * @param int $minutes The number of minutes to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addMinutes(int $minutes = 1): static
    {
        return new static($this->getTime()->modify("+$minutes minute"));
    }

    /**
     * Subtract a specified number of minutes from the current date and time.
     *
     * @param int $minutes The number of minutes to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subMinutes(int $minutes = 1): static
    {
        return new static($this->getTime()->modify("-$minutes minute"));
    }

    /**
     * Add a specified number of seconds to the current date and time.
     *
     * @param int $seconds The number of seconds to add. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function addSeconds(int $seconds = 1): static
    {
        return new static($this->getTime()->modify("+$seconds second"));
    }

    /**
     * Subtract a specified number of seconds from the current date and time.
     *
     * @param int $seconds The number of seconds to subtract. Defaults to 1.
     *
     * @return static A new instance with the modified date and time.
     */
    public function subSeconds(int $seconds = 1): static
    {
        return new static($this->getTime()->modify("-$seconds second"));
    }
}