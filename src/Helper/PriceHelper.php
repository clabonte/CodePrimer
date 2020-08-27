<?php

namespace CodePrimer\Helper;

use NumberFormatter;

class PriceHelper
{
    private $numberFormatter;

    public function __construct(string $locale = 'en_US')
    {
        $this->numberFormatter = NumberFormatter::create($locale, NumberFormatter::CURRENCY);
    }

    public function isValidPrice(string $value): bool
    {
        $currency = '';

        return false !== $this->numberFormatter->parseCurrency($value, $currency);
    }

    public function asFloat(string $value): float
    {
        $currency = '';

        return $this->numberFormatter->parseCurrency($value, $currency);
    }
}
