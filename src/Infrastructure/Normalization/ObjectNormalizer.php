<?php

declare(strict_types=1);


namespace App\Infrastructure\Normalization;


use App\Infrastructure\Normalization\Normalizer\DateTimeNormalizer;
use App\Infrastructure\Normalization\Normalizer\EmailNormalizer;
use App\Infrastructure\Normalization\Normalizer\IpAddressNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use GBProd\UuidNormalizer\UuidNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Serializer;

final class ObjectNormalizer
{
    public const SCOPE_PUBLIC = 'public';
    public const SCOPE_PRIVATE = 'private';
    public const SCOPE_ADMIN = 'admin';

    private $serializer;

    /**
     * ObjectNormalizer constructor.
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct()
    {
        $encoders = [
            new JsonEncoder(),
        ];

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $normalizers = [
            new UuidNormalizer(),
            new EmailNormalizer(),
            new DateTimeNormalizer(),
            new IpAddressNormalizer(),
            new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer($classMetadataFactory),
        ];

        $this->serializer = new Serializer(
            $normalizers,
            $encoders
        );

    }

    public function normalize($object, string $scope): array
    {
        $result = $this->serializer->normalize(
            $object,
            null,
            [
                'groups' => [
                    $scope,
                ],
            ]
        );

        return $result;
    }


}