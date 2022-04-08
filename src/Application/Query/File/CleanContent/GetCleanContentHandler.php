<?php

declare(strict_types=1);


namespace App\Application\Query\File\CleanContent;


use App\Application\Query\QueryHandlerInterface;
use App\Infrastructure\Cleaner\MetaCleaner;

final class GetCleanContentHandler implements QueryHandlerInterface
{
    private $metaCleaner;

    public function __construct(MetaCleaner $metaCleaner)
    {
        $this->metaCleaner = $metaCleaner;
    }

    public function handle(GetCleanContentQuery $command): string
    {
        return $this->metaCleaner->getCleanFile(
            $command->getFileName(),
            $command->getFileContent()
        );
    }
}