<?php

declare(strict_types=1);


namespace App\Application\Query\File\UpdateContent;


final class GetUpdatedContentQuery
{
    private $fileName;
    private $fileContent;
    private $metadata;

    public function __construct(
        string $fileName,
        string $fileContent,
        array $metadata)
    {
        $this->fileName = $fileName;
        $this->fileContent = $fileContent;
        $this->metadata = $metadata;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFileContent(): string
    {
        return $this->fileContent;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }
}