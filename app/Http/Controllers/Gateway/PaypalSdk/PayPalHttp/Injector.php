<?php

namespace App\Http\Controllers\Gateway\PaypalSdk\PayPalHttp;

/**
 * Interface Injector
 *
 * @see HttpClient
 */
interface Injector
{
    /**
     * @param $httpRequest HttpRequest
     */
    public function inject($httpRequest);
}
