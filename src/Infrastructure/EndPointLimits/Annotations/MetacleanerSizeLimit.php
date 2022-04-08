<?php

declare(strict_types=1);


namespace App\Infrastructure\EndPointLimits\Annotations;


use Doctrine\Common\Annotations\Annotation\Required;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
final class MetacleanerSizeLimit implements ConfigurationInterface
{
    public const FIELD_SCOPES = 'scopes';
    public const FIELD_SIZE = 'size';
    public const FIELD_BANDWIDTH = 'bandwidth';

    public const ALIAS_NAME = 'metacleaner-size-limit';

    /**
     * @Required()
     * @var array
     */
    public $scopes = [];

    public function getAliasName()
    {
        return self::ALIAS_NAME;
    }

    public function allowArray()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes(?array $scopes)
    {
        $this->scopes = $scopes;
    }
}