<?php

namespace common\components\monochrome\i18n;

use Yii;
use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter
{
    /**
     * @var boolean whether the [PHP intl extension](http://php.net/manual/en/book.intl.php) is loaded.
     */
    private $_intlLoaded = false;

	public $currencyDivisor = 1;

	public $currencySuffix = '';

	public $currencyDecimal = 2;

    public $dateWeek = false;

    public static function weekLabels()
    {
        return [
            0 => Yii::t('common/app', 'week Sun'),
            1 => Yii::t('common/app', 'week Mon'),
            2 => Yii::t('common/app', 'week Tue'),
            3 => Yii::t('common/app', 'week Wed'),
            4 => Yii::t('common/app', 'week Thu'),
            5 => Yii::t('common/app', 'week Fri'),
            6 => Yii::t('common/app', 'week Sat'),
        ];
    }

    public function asCurrency($value, $currency = null, $options = [], $textOptions = [])
    {
        // $currencySuffix = $value > 0 ? $this->currencySuffix : '';
        $currencySuffix = $this->currencySuffix;
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if (isset($this->$key)) {
                    $this->$key = $value;
                }
            }
        }

        $value = $this->normalizeNumericValue($value);
        if ($this->_intlLoaded) {
            $formatter = $this->createNumberFormatter(NumberFormatter::CURRENCY, null, $options, $textOptions);

            if ($currency === null) {
                if ($this->currencyCode === null) {
                    $currency = $formatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
                } else {
                    $currency = $this->currencyCode;
                }
            }

            return $formatter->formatCurrency($value, $currency);
        } else {
            if ($currency === null) {
                if ($this->currencyCode === null) {
                    throw new InvalidConfigException('The default currency code for the formatter is not defined.');
                }

                $currency = $this->currencyCode;
            }

            $float = 0;

            $brokenValue = explode('.', (float)$value/(int)$this->currencyDivisor);

            if (!isset($brokenValue[1]) || $brokenValue[1] == 0) {
                $brokenValue[1] = '';
            }

            if ($brokenValue[1] > 0) {
                $brokenValue[1] = '.'.mb_substr($brokenValue[1] , $float, $this->currencyDecimal);
            }

            if ($brokenValue[0] > 9999) {
                $brokenValue[0] = $brokenValue[0]/10000;
                $currencySuffix = ' 億';
                $float = 4;
            }

            return $currency . ' ' . $this->asDecimal($brokenValue[0], $float, $options, $textOptions) . $brokenValue[1] . $currencySuffix;

            //return $currency . ' ' . $this->asDecimal((int)$value/(int)$this->currencyDivisor, (int)$this->currencyDecimal, $options, $textOptions) . $this->currencySuffix;
        }
    }

    //TODO 不要影響原生的asCurrency
    public function asTWCurrency($value, $currency = null, $options = [], $textOptions = [])
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if (isset($this->$key)) {
                    $this->$key = $value;
                }
            }
        }

        $value = $this->normalizeNumericValue($value);
        if ($this->_intlLoaded) {
            $formatter = $this->createNumberFormatter(NumberFormatter::CURRENCY, null, $options, $textOptions);
            if ($currency === null) {
                if ($this->currencyCode === null) {
                    $currency = $formatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
                } else {
                    $currency = $this->currencyCode;
                }
            }
            return $formatter->formatCurrency($value, $currency);
        } else {
            if ($currency === null) {
                if ($this->currencyCode === null) {
                    throw new InvalidConfigException('The default currency code for the formatter is not defined.');
                }
                $currency = $this->currencyCode;
            }

            $brokenValue = explode('.', (float)$value/(int)$this->currencyDivisor);
            if (!isset($brokenValue[1]) || $brokenValue[1] == 0) {
                $brokenValue[1] = '';
            }

            if ($brokenValue[1] > 0) {
                $brokenValue[1] = '.'.mb_substr($brokenValue[1] , 0, $this->currencyDecimal);
            }

            return $currency . ' ' . $this->asDecimal($brokenValue[0], 0, $options, $textOptions) . $brokenValue[1] . $this->currencySuffix;

            //return $currency . ' ' . $this->asDecimal((int)$value/(int)$this->currencyDivisor, (int)$this->currencyDecimal, $options, $textOptions) . $this->currencySuffix;
        }
    }

    public function asDate($value, $format = null)
    {
        if ($value != null) {
            return parent::asDate($value, $format);
        }

        return null;
    } 

    public function asWDate($value, $format = null)
    {
        $date = null;
        if ($value != null) {
            $date = parent::asDate($value, $format);
            $date .= self::weekLabels()[date('w', (integer)$value)];            
        }

        return $date;
    }    

    public function asEnable($value, $format = null)
    {
        if (!empty($value)) {
            return Yii::t('common/app', 'Enable');
        }

        return Yii::t('common/app', 'Disable');
    } 

    public function asWDateNoYear($value, $format = 'php:m-d')
    {
        $date = null;
        if ($value != null) {
            $date = parent::asDate($value, $format);
            $date .= self::weekLabels()[date('w', (integer)$value)];
        }

        return $date;
    } 

    public function asMongoDate($value, $format = 'php:Y-m-d')
    {
        $date = null;
        if ($value != null) {
            $date = parent::asDate($value->sec, $format);
        }

        return $date;
    }

    /**
     * @var array map of short format names to IntlDateFormatter constant values.
     */
    private $_dateFormats = [
        'short'  => 3, // IntlDateFormatter::SHORT,
        'medium' => 2, // IntlDateFormatter::MEDIUM,
        'long'   => 1, // IntlDateFormatter::LONG,
        'full'   => 0, // IntlDateFormatter::FULL,
    ];

    private function formatDateTimeValue($value, $format, $type)
    {
        $timeZone = $this->timeZone;
        // avoid time zone conversion for date-only values
        if ($type === 'date') {
            list($timestamp, $hasTimeInfo) = $this->normalizeDatetimeValue($value, true);
            if (!$hasTimeInfo) {
                $timeZone = $this->defaultTimeZone;
            }
        } else {
            $timestamp = $this->normalizeDatetimeValue($value);
        }
        if ($timestamp === null) {
            return $this->nullDisplay;
        }
        // intl does not work with dates >=2038 or <=1901 on 32bit machines, fall back to PHP
        $year = $timestamp->format('Y');
        if ($this->_intlLoaded && !(PHP_INT_SIZE == 4 && ($year <= 1901 || $year >= 2038))) {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = FormatConverter::convertDatePhpToIcu(substr($format, 4));
            }
            if (isset($this->_dateFormats[$format])) {
                if ($type === 'date') {
                    $formatter = new IntlDateFormatter($this->locale, $this->_dateFormats[$format], IntlDateFormatter::NONE, $timeZone);
                } elseif ($type === 'time') {
                    $formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, $this->_dateFormats[$format], $timeZone);
                } else {
                    $formatter = new IntlDateFormatter($this->locale, $this->_dateFormats[$format], $this->_dateFormats[$format], $timeZone);
                }
            } else {
                $formatter = new IntlDateFormatter($this->locale, IntlDateFormatter::NONE, IntlDateFormatter::NONE, $timeZone, null, $format);
            }
            if ($formatter === null) {
                throw new InvalidConfigException(intl_get_error_message());
            }
            // make IntlDateFormatter work with DateTimeImmutable
            if ($timestamp instanceof \DateTimeImmutable) {
                $timestamp = new DateTime($timestamp->format(DateTime::ISO8601), $timestamp->getTimezone());
            }
            return $formatter->format($timestamp);
        } else {
            if (strncmp($format, 'php:', 4) === 0) {
                $format = substr($format, 4);
            } else {
                $format = FormatConverter::convertDateIcuToPhp($format, $type, $this->locale);
            }
            if ($timeZone != null) {
                if ($timestamp instanceof \DateTimeImmutable) {
                    $timestamp = $timestamp->setTimezone(new DateTimeZone($timeZone));
                } else {
                    $timestamp->setTimezone(new DateTimeZone($timeZone));
                }
            }
            return $timestamp->format($format);
        }
    }

    /**
     * Given the value in bytes formats number part of the human readable form.
     *
     * @param string|integer|float $value value in bytes to be formatted.
     * @param integer $decimals the number of digits after the decimal point
     * @param array $options optional configuration for the number formatter. This parameter will be merged with [[numberFormatterOptions]].
     * @param array $textOptions optional configuration for the number formatter. This parameter will be merged with [[numberFormatterTextOptions]].
     * @return array [parameters for Yii::t containing formatted number, internal position of size unit]
     * @throws InvalidParamException if the input value is not numeric.
     */
    private function formatSizeNumber($value, $decimals, $options, $textOptions)
    {
        if (is_string($value) && is_numeric($value)) {
            $value = (int) $value;
        }
        if (!is_numeric($value)) {
            throw new InvalidParamException("'$value' is not a numeric value.");
        }
        $position = 0;
        do {
            if ($value < $this->sizeFormatBase) {
                break;
            }
            $value = $value / $this->sizeFormatBase;
            $position++;
        } while ($position < 5);
        // no decimals for bytes
        if ($position === 0) {
            $decimals = 0;
        } elseif ($decimals !== null) {
            $value = round($value, $decimals);
        }
        // disable grouping for edge cases like 1023 to get 1023 B instead of 1,023 B
        $oldThousandSeparator = $this->thousandSeparator;
        $this->thousandSeparator = '';
        if ($this->_intlLoaded) {
            $options[NumberFormatter::GROUPING_USED] = false;
        }
        // format the size value
        $params = [
            // this is the unformatted number used for the plural rule
            'n' => $value,
            // this is the formatted number used for display
            'nFormatted' => $this->asDecimal($value, $decimals, $options, $textOptions),
        ];
        $this->thousandSeparator = $oldThousandSeparator;
        return [$params, $position];
    } 

}