<?php

namespace App\Http\Controllers\Gateway\PaypalSdk\PayPalHttp;

/**
 * Interface Serializer
 */
interface Serializer
{
    /**
     * @return string Regex that matches the content type it supports.
     */
    public function contentType();

    /**
     * @return string representation of your data after being serialized.
     */
    public function encode(HttpRequest $request);

    /**
     * @return mixed object/string representing the de-serialized response body.
     */
    public function decode($body);
}
