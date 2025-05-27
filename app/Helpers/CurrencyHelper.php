<?php

// app/Helpers/CurrencyHelper.php

if (!function_exists('format_currency_to_cents')) {
    function format_currency_to_cents(string|int|float $amount): int
    {
        $amount = (string) $amount;

        $amount = trim($amount);


        if (str_contains($amount, ',') && (!str_contains($amount, '.') || strpos($amount, ',') > strpos($amount, '.'))) {
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        } else
            $amount = str_replace(',', '', $amount);

        if (!is_numeric($amount))
            throw new \InvalidArgumentException("Valor monetário inválido: '{$amount}'");

        bcscale(2);
        $floatAmount = bcmul((string) $amount, '100');

        return (int) round(floatval($floatAmount));
    }

}
