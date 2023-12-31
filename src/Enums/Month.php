<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 31.12.2023 Time: 08:23
 */


namespace Chronos\Enums;

use Chronos\TimeUnitTranslator;
use MultiLanguage\LanguageException;

enum Month: string
{
    case JANUARY = "January";
    case FEBRUARY = "February";
    case MARCH = "March";
    case APRIL = "April";
    case MAY = "May";
    case JUNE = "June";
    case JULY = "July";
    case AUGUST = "August";
    case SEPTEMBER = "September";
    case OCTOBER = "October";
    case NOVEMBER = "November";
    case DECEMBER = "December";

    /**
     * @throws LanguageException
     */
    public function translate(?string $lang = null): string
    {
        TimeUnitTranslator::initialize();
        return trim(TimeUnitTranslator::translate($this->value, $lang, ['#days#' => "", '#years#' => ""]));
    }
}