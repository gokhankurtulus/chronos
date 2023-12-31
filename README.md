# Chronos

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)
![Release](https://img.shields.io/github/v/release/gokhankurtulus/chronos.svg)

A simple PHP library for working with date and time.

## Installation

You can install the library using Composer. Run the following command:

```bash
composer require gokhankurtulus/chronos
```

## Usage

Chronos uses DateTimeImmutable when working with instances.

* [Initialize](#initialize)
* [Date Creation](#date-creation)
* [Manipulation](#manipulation)
* [Comparison](#comparison)
* [Multi-Language Printing](#multi-language-printing)
* [More](#more)

### Initialize

You can set default timezone, format and current language for multi-language printing.

```php
use Chronos\Chronos;
use Chronos\TimeUnitTranslator;

$format = "Y-m-d H:i:s";
$timezone = "Europe/Istanbul";
$allowedLanguages = ["en", "tr"];
$currentLanguage = "tr";
$defaultLanguage = "en";

Chronos::setDefaultFormat($format);

//If you want to trigger 'date_default_timezone_set' function, set second parameter as true on the 'setDefaultTimeZone' method.
Chronos::setDefaultTimeZone($timezone, true);

// Set current language by default to pretty print.
TimeUnitTranslator::initialize($currentLanguage, $defaultLanguage, $allowedLanguages);
```

### Date Creation

```php
use Chronos\Chronos;

$timestamp = 946677600;
$time = "2000-01-01 00:00:00";
$format = "Y-m-d H:i:s";
$timezone = "Europe/Istanbul";

Chronos::setDefaultFormat($format);
Chronos::setDefaultTimeZone($timezone, true);

/** 
 * Format parameter is optional. If you don't give, tries to get from Chronos::getDefaultFormat()
 * Timezone parameter is optional.
 * Indicates that date was created from this timezone.
 * If you don't give, tries to get from Chronos::getDefaultTimeZone()
 * Then tries to get from 'date_default_timezone_get()'
 */
$createFromFormat = Chronos::createFromFormat($time, $format, $timezone);

/** 
 * Timezone parameter is optional.
 * Indicates that created date from timestamp will be converted to this timezone.
 * If you don't give, tries to get from Chronos::getDefaultTimeZone()
 * Then tries to get from 'date_default_timezone_get()'
 */
$createFromTimestamp = Chronos::createFromTimestamp($timestamp, $timezone);

/** Usage Examples */

$yesterday  = Chronos::yesterday();
$now        = Chronos::now();
$tomorrow   = Chronos::tomorrow();

if (Chronos::isFormattable($time, $format) && Chronos::isValidTimeZone($timezone)) {
    $createFromFormat = Chronos::createFromFormat($time, $format, $timezone);
}

if (Chronos::isTimestamp($timestamp) && Chronos::isValidTimeZone($timezone)) {
    $createFromTimestamp = Chronos::createFromTimestamp($timestamp, $timezone);
}

var_dump($createFromTimestamp);
// output:
//object(Chronos\Chronos)#4 (1) {
//  ["dateTimeImmutable":protected]=>
//  object(DateTimeImmutable)#2 (3) {
//    ["date"]=>
//    string(26) "2000-01-01 00:00:00.000000"
//    ["timezone_type"]=>
//    int(3)
//    ["timezone"]=>
//    string(15) "Europe/Istanbul"
//  }
//}
```

### Manipulation

```php
$time = "2023-12-12 00:00:00";
$format = "Y-m-d H:i:s";
$timezone = "Europe/Istanbul";

$createFromFormat = Chronos::createFromFormat($time, $format, $timezone);

$manipulatedDate = $createFromFormat
    ->addSeconds(1)
    ->addMinutes(1)
    ->addHours(1)
    ->addDays(1)
    ->addMonths(1)
    ->addYears(1);

// change timezone to actual date by timezone
$timezoneChanged = $manipulatedDate->toTimeZone('Europe/Berlin');

echo '<pre>';
var_dump($timezoneChanged->date($format));
var_dump($manipulatedDate->date($format));
var_dump($createFromFormat->date($format));
echo '</pre>';

// Output:
// string(19) "2025-01-12 23:01:01"
// string(19) "2025-01-13 01:01:01"
// string(19) "2023-12-12 00:00:00"
```

### Comparison

```php
$time = "2000-01-01 00:00:00";
$format = "Y-m-d H:i:s";
$timezone1 = "Europe/Istanbul";
$timezone2 = "Europe/Berlin";

$first = Chronos::createFromFormat($time, $format, $timezone1);
$second = Chronos::createFromFormat($time, $format, $timezone2);

var_dump($first->age()); // executed in: 2023, output: 23.

/** $targetDateTime can be Chronos|DateTimeInterface */
var_dump($first->isPast($second)); // output: true

/** $targetDateTime can be Chronos|DateTimeInterface */
var_dump($first->isFuture($second)); // output: false

/** $targetDateTime can be Chronos|DateTimeInterface */
var_dump($first->isSameDay($second)); // output: true

var_dump($first->isWeekday()); // output: false
var_dump($first->isWeekend()); // output: true

/** $targetDateTime can be Chronos|DateTimeInterface */
var_dump($first->dayDiff($second)); // output: int(0)

/** $targetDateTime can be Chronos|DateTimeInterface */
var_dump($first->diff($second)); // output is similar to diffFromFormat method

var_dump($first->diffFromFormat($time, $format, $timezone2));
// same output for diff and diffFromFormat methods:
//object(DateInterval)#7 (10) {
//  ["y"]=>
//  int(0)
//  ["m"]=>
//  int(0)
//  ["d"]=>
//  int(0)
//  ["h"]=>
//  int(1)
//  ["i"]=>
//  int(0)
//  ["s"]=>
//  int(0)
//  ["f"]=>
//  float(0)
//  ["invert"]=>
//  int(0)
//  ["days"]=>
//  int(0)
//  ["from_string"]=>
//  bool(false)
//}
}
```

### Multi-Language Printing

Visit [gokhankurtulus/multilanguage](https://github.com/gokhankurtulus/multilanguage) repository for more about the
library.

```php
// If you want to initialize TimeUnitTranslator,
// then you can give null to $lang parameter when you want to use translations
// default language is 'en'.
// If you don't set current language and give null it will try to get default language
/** @see https://github.com/gokhankurtulus/multilanguage for more usage examples */

use Chronos\TimeUnitTranslator;

// check src/Lang for supported languages
$allowedLanguages = ["en"]; // then you can set allowed languages whatever you want.
$currentLanguage = "en";
$defaultLanguage = "en";

if (!TimeUnitTranslator::isAllowedLanguage($currentLanguage)) {
    die("Language: '$currentLanguage' is not allowed.");
}
if (!TimeUnitTranslator::isAllowedLanguage($defaultLanguage)) {
    die("Language: '$defaultLanguage' is not allowed.");
}
TimeUnitTranslator::initialize($currentLanguage, $defaultLanguage, $allowedLanguages);

$time1 = "2000-01-01 00:00:00";
$time2 = "2006-06-05 03:02:01";
$format = "Y-m-d H:i:s";
$timezone = "Europe/Istanbul";

$first = Chronos::createFromFormat($time1, $format, $timezone);
$second = Chronos::createFromFormat($time2, $format, $timezone);

/** 
 * you can give a $language if its supported,
 * if you want to use current language set null
 * 
 * $depth can be 0-6 represents how many units do you want
 * priority order: year, month, day, hour, minute, second
 * $depth = 0 means first unit but also includes (now, today, yesterday, tomorrow)
 * 
 * $targetDateTime can be Chronos|DateTimeInterface
 * if you don't give it will be Chronos::now()
* */

var_dump($first->prettyDiff(null, 6, $second));
// output: string(54) "6 years 5 months 4 days 3 hours 2 minutes 1 second ago"

var_dump($first->prettyDiff(null, 2, $second));
// output: string(20) "6 years 5 months ago"

$time1 = "2000-01-01 00:00:00";
$time2 = "2000-01-01 00:01:59";
// If times are given like this then,
$first = Chronos::createFromFormat($time1, $format, $timezone);
$second = Chronos::createFromFormat($time2, $format, $timezone);

var_dump($first->prettyDiff("en", 0, $second));
// output: string(3) "now"

var_dump($first->prettyDiff("en"));
// executed in: 2023, output: string(12) "23 years ago"

var_dump($first->prettyDiff("tr", 0, $second));
// will throw LanguageException because given language is not set as allowed language

var_dump($first->date($format)); // if you don't pass $format default format is 'Y-m-d'
// output: string(19) "2000-01-01 00:00:00"

var_dump($first->time(false)); // $includeSeconds to false if you don't want seconds
// output: string(5) "00:00"

var_dump($first->prettyDatePrint(null, false)); // $includeYears to false if you don't want years
// output: string(10) "01 January"

// parameters are $lang, $includeYears, $includeSeconds
var_dump($first->prettyPrint());
// output: string(24) "01 January 2000 00:00:00"

var_dump($first->dayName($currentLanguage));
// output: string(8) "Saturday"

var_dump($first->monthName($currentLanguage));
// output: string(7) "January"

var_dump(\Chronos\Enums\Day::MONDAY->translate("en"));
// output: string(6) "Monday"

var_dump(\Chronos\Enums\Month::JANUARY->translate("en"));
// output: string(7) "January"

var_dump($first->second()); // output: string(2) "00"
var_dump($first->minute()); // output: string(2) "00"
var_dump($first->hour());   // output: string(4) "00"
var_dump($first->day());    // output: string(2) "01"
var_dump($first->month());  // output: string(2) "01"
var_dump($first->year());   // output: string(4) "2000"
```

### More

```php
use Chronos\Chronos;

$time = "2000-01-01 00:00:00";
$format = "Y-m-d H:i:s";
$timezone = "Europe/Istanbul";

Chronos::getDefaultFormat();
Chronos::setDefaultFormat($format);

Chronos::getDefaultTimeZone();
Chronos::setDefaultTimeZone($timezone);

Chronos::isTimestamp($timestamp);
Chronos::isFormattable($time, $format);
Chronos::isValidFormat($format);
Chronos::isValidTimeZone($timezone);

$now = Chronos::now($timezone);
$now->format($format);
$now->timestamp();

$now->getTime(); // returns DateTimeImmutable object
$now->setTime($dateTimeImmutableObject);
```

## License

Chronos is open-source software released under the [MIT License](LICENSE). Feel free to modify and use it in your
projects.

## Contributions

Contributions to Chronos are welcome! If you find any issues or have suggestions for improvements, please create
an issue or submit a pull request on the [GitHub repository](https://github.com/gokhankurtulus/chronos).