<?php
/**
 * @author Gökhan Kurtuluş @gokhankurtulus
 * Date: 28.12.2023 Time: 07:40
 */

namespace Chronos;

use MultiLanguage\LanguageException;

class TimeUnitTranslator extends \MultiLanguage\MultiLanguage
{
    protected static string $directoryPath = "";
    protected static array $allowedLanguages = [
        "az",
        "en",
        "tr"
    ];
    protected static string $defaultLanguage = "en";
    protected static string $currentLanguage = "";

    /**
     * @throws LanguageException
     */
    public static function initialize(?string $lang = null, ?string $defaultLang = null, array $allowedLanguages = []): void
    {
        static::setDirectoryPath(__DIR__ . DIRECTORY_SEPARATOR . 'Lang');
        if (!empty($allowedLanguages))
            static::setAllowedLanguages($allowedLanguages);
        if (!empty($defaultLang))
            static::setDefaultLanguage($defaultLang);
        if (!empty($lang))
            static::setCurrentLanguage($lang);
    }
}