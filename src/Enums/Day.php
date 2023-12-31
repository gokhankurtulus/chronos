<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 31.12.2023 Time: 08:43
 */

namespace Chronos\Enums;

use Chronos\TimeUnitTranslator;
use MultiLanguage\LanguageException;

enum Day: string
{
    case MONDAY = "Monday";
    case TUESDAY = "Tuesday";
    case WEDNESDAY = "Wednesday";
    case THURSDAY = "Thursday";
    case FRIDAY = "Friday";
    case SATURDAY = "Saturday";
    case SUNDAY = "Sunday";

    /**
     * @throws LanguageException
     */
    public function translate(?string $lang = null): string
    {
        TimeUnitTranslator::initialize();
        return trim(TimeUnitTranslator::translate($this->value, $lang));
    }
}
