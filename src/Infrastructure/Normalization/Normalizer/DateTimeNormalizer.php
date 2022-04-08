<?php

declare(strict_types=1);


namespace App\Infrastructure\Normalization\Normalizer;


use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DateTimeNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /**
         * @var $object \DateTimeInterface
         */
        return $object->format(DATE_RFC3339);
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && in_array(\DateTimeInterface::class, class_implements(get_class($data)));
    }
}