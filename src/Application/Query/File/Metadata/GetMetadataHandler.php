<?php

declare(strict_types=1);


namespace App\Application\Query\File\Metadata;


use App\Application\Query\QueryHandlerInterface;
use App\Infrastructure\Cleaner\MetaCleaner;

final class GetMetadataHandler implements QueryHandlerInterface
{
    private $metaCleaner;

    public function __construct(MetaCleaner $metaCleaner)
    {
        $this->metaCleaner = $metaCleaner;
    }

    public function handle(GetMetadataQuery $command): array
    {
        return $this->metaCleaner->getAnalytics(
            $command->getFileName(),
            $command->getFileContent()
        );
    }
}