<?php

declare(strict_types=1);


namespace App\Infrastructure\Normalization\Normalizer;


use DDD\Embeddable\EmailAddress;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EmailNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /**
         * @var $object EmailAddress
         */
        return (string)$object;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof EmailAddress;
    }
}