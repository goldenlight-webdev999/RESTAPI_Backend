<?php

declare(strict_types=1);


namespace App\Application\Query\File\Metadata;


final class GetMetadataQuery
{
    private $fileName;
    private $fileContent;

    public function __construct(
        string $fileName,
        string $fileContent
    )
    {
        $this->fileName = $fileName;
        $this->fileContent = $fileContent;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFileContent(): string
    {
        return $this->fileContent;
    }
}