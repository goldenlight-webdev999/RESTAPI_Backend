<?php

declare(strict_types=1);


namespace App\Infrastructure\Captcha;


use ReCaptcha\ReCaptcha;

final class CaptchaProvider
{
    private $reCaptcha;

    public function __construct(string $captchaKey, string $proxyConnectionUrl)
    {
        $proxyRequestMethod = new ProxyRequestMethod($proxyConnectionUrl);
        $this->reCaptcha = new ReCaptcha($captchaKey, $proxyRequestMethod);
    }

    public function isCaptchaSolutionValid(string $captchaSolution): bool
    {
        return $this->reCaptcha->verify($captchaSolution)->isSuccess();
    }
}