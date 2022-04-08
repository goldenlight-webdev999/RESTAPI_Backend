<?php

declare(strict_types=1);


namespace App\UI\Http\PublicRest\Controllers;

use App\Application\Command\Log\SaveCleanTaskLog\SaveCleanTaskLogCommand;
use App\Application\Query\File\CleanContent\GetCleanContentQuery;
use App\Application\Query\File\Metadata\GetMetadataQuery;
use DDD\Embeddable\IpAddress;
use League\Tactician\CommandBus;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class FileController
{
    private $commandBus;
    private $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    /**
     * This endpoint will be used only for anonymous users
     *
     * @Route ("/files", methods={"POST"})
     * @RateLimit(limit=1, period=86400)
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $rawContent = $request->getContent(false);

        $content = json_decode($rawContent, true);

        $fileName = $content['filename'];
        $fileContent = $content['content'];
        $originalContent = base64_decode($fileContent);

        $start = microtime(true);
        $finalContent = $this->commandBus->handle(new GetCleanContentQuery($fileName, $originalContent));
        $end = microtime(true);

        try {
            $metadata = $this->commandBus->handle(new GetMetadataQuery($fileName, $originalContent));
        } catch (\Throwable $exception) {
            $metadata = [];
        }

        $this->commandBus->handle(
            new SaveCleanTaskLogCommand(
                null,
                null,
                $fileName,
                mb_strlen($finalContent, '8bit'),
                mb_strlen($originalContent, '8bit'),
                new IpAddress($request->getClientIp()),
                $request->headers->get('User-Agent', ''),
                (int)(($end - $start) * 1000)
            )
        );

        return new JsonResponse([
            'filename' => $fileName,
            'content' => base64_encode($finalContent),
            'metadata' => $metadata,
        ]);
    }
}