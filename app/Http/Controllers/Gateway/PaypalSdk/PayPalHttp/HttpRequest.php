<?php

namespace App\Http\Controllers\Gateway\PaypalSdk\PayPalHttp;

/**
 * Class HttpRequest
 *
 * @see HttpClient
 */
class HttpRequest
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var array | string
     */
    public $body;

    /**
     * @var string
     */
    public $verb;

    /**
     * @var array
     */
    public $headers;

    public function __construct($path, $verb)
    {
        $this->path = $path;
        $this->verb = $verb;
        $this->body = null;
        $this->headers = [];
    }
}
