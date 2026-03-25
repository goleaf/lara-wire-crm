<?php

if (! function_exists('crm_currency')) {
    function crm_currency(): string
    {
        return (string) config('crm.currency.symbol', config('crm.default_currency.symbol', '$'));
    }
}
