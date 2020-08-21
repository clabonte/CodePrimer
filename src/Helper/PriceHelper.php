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

        return $this->numberFormatter->parseCurrency($value, $currency);
    }
}
