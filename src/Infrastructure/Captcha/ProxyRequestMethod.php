<?php

declare(strict_types=1);


namespace App\Infrastructure\Captcha;


use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

final class ProxyRequestMethod implements RequestMethod
{
    /**
     * URL to which requests are POSTed.
     * @const string
     */
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    private $proxy;

    /**
     * ProxyRequestMethod constructor.
     * @param string $proxy
     */
    public function __construct(string $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Submit the POST request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the reCAPTCHA response
     */
    public function submit(RequestParameters $params)
    {
        /**
         * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
         * Using "CN_name" will still work, but it will raise deprecated errors.
         */
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $params->toQueryString(),
                // Force the peer to validate (not needed in 5.6.0+, but still works)
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peer_key => 'www.google.com',
                // Uncomment if proxy is active
                // 'proxy' => $this->proxy,
                'request_fulluri' => true,
            ],
        ];

        $context = stream_context_create($options);
        return file_get_contents(self::SITE_VERIFY_URL, false, $context);
    }
}