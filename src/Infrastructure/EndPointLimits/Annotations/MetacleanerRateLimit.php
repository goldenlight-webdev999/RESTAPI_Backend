<?php

declare(strict_types=1);


namespace App\Infrastructure\EndPointLimits\Annotations;


use Noxlogic\RateLimitBundle\Annotation\RateLimit;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
final class MetacleanerRateLimit extends RateLimit
{
    public const FIELD_SCOPES = 'scopes';
    public const ALIAS_NAME = 'metacleaner-rate-limit';

    protected $scopes;

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function setScopes(?array $scopes): void
    {
        $this->scopes = $scopes;
    }

    public function getAliasName()
    {
        return self::ALIAS_NAME;
    }
}