<?php

declare(strict_types=1);


namespace App\Application\Query\File\UpdateContent;


use App\Application\Query\QueryHandlerInterface;
use App\Infrastructure\Cleaner\MetaCleaner;

final class GetUpdatedContentHandler implements QueryHandlerInterface
{
    private $metaCleaner;

    public function __construct(MetaCleaner $metaCleaner)
    {
        $this->metaCleaner = $metaCleaner;
    }

    public function handle(GetUpdatedContentQuery $command): string
    {
        return $this->metaCleaner->getUpdatedFile(
            $command->getFileName(),
            $command->getFileContent(),
            $command->getMetadata()
        );
    }
}