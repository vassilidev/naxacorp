<?php

namespace App\Http\Controllers\Gateway\PaypalSdk\PayPalHttp;

/**
 * Interface Environment
 *
 * @see HttpClient
 */
interface Environment
{
    /**
     * @return string
     */
    public function baseUrl();
}
