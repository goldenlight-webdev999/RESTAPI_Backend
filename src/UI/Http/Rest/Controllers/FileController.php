<?php

declare(strict_types=1);


namespace App\UI\Http\Rest\Controllers;


use App\Application\Command\Log\SaveCleanTaskLog\SaveCleanTaskLogCommand;
use App\Application\Query\File\CleanContent\GetCleanContentQuery;
use App\Application\Query\File\Metadata\GetMetadataQuery;
use App\Application\Query\File\UpdateContent\GetUpdatedContentQuery;
use App\Domain\User\User;
use App\Infrastructure\EndPointLimits\Annotations\MetacleanerSizeLimit;
use App\Infrastructure\OAuth2\Entity\OAuth2Client;
use App\Infrastructure\EndPointLimits\Annotations\MetacleanerRateLimit;
use App\Infrastructure\Cleaner\Exiftool;
use DDD\Embeddable\IpAddress;
use FOS\OAuthServerBundle\Model\AccessTokenManagerInterface;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class FileController
{
    private $commandBus;
    private $logger;
    private $exiftool;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger, Exiftool $exiftool)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->exiftool = $exiftool;
    }

    /**
     * @Route ("/files")
     *
     * You must define the user limits here!
     *
     * @MetacleanerRateLimit(
     *     scopes = {
     *          "free": {
     *              "limit": 5,
     *              "period": 86400
     *          }
     *     }
     * )
     * @MetacleanerSizeLimit(
     *     scopes = {
     *          "basic" : {
     *              "size": 5000000,
     *              "bandwidth": 1000000000
     *          },
     *          "advance" : {
     *              "size": 30000000,
     *              "bandwidth": 10000000000
     *          },
     *          "enterprise" : {
     *              "size": 30000000,
     *              "bandwidth": 100000000000
     *          }
     * })
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request, Security $security, AccessTokenManagerInterface $accessTokenManager, ClientManagerInterface $clientManager): JsonResponse
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

        /**
         * @var User $currentUser
         */
        $currentUser = $security->getUser();
        /**
         * @var OAuthToken $token
         */
        $token = $security->getToken();
        $accessToken = $accessTokenManager->findTokenByToken($token->getToken());
        $clientPublicId = $accessToken->getClientId();
        /**
         * @var OAuth2Client $client
         */
        $client =  $clientManager->findClientByPublicId($clientPublicId);

        $this->commandBus->handle(
            new SaveCleanTaskLogCommand(
                $currentUser->getId(),
                $client->getId(),
                $fileName,
                mb_strlen($finalContent, '8bit'),
                mb_strlen($originalContent, '8bit'),
                new IpAddress($request->getClientIp()),
                $request->headers->get('User-Agent', ''),
                (int)(($end - $start) * 1000)
            )
        );

        // FIXME review memory limit setted
        ini_set("memory_limit", "-1");

        return new JsonResponse([
            'filename' => $fileName,
            'content' => base64_encode($finalContent),
            'metadata' => $metadata,
        ]);
    }

    /**
     * @Route ("/getFileMetadata")
     *
     * You must define the user limits here!
     *
     * @MetacleanerSizeLimit(
     *     scopes = {
     *          "basic" : {
     *              "size": 5000000,
     *              "bandwidth": 1000000000
     *          },
     *          "advance" : {
     *              "size": 30000000,
     *              "bandwidth": 10000000000
     *          },
     *          "enterprise" : {
     *              "size": 30000000,
     *              "bandwidth": 100000000000
     *          }
     * })
     * @param Request $request
     * @return JsonResponse
     */
    public function getMetaData(Request $request): JsonResponse{
        $rawContent = $request->getContent(false);

        $content = json_decode($rawContent, true);

        $fileName = $content['filename'];
        $fileContent = $content['content'];
        $originalContent = base64_decode($fileContent);

        try {
            $metadata = $this->commandBus->handle(new GetMetadataQuery($fileName, $originalContent));
        } catch (\Throwable $exception) {
            $metadata = [];
        }
        $fileNameParts = explode('.', $fileName);
        $extension = end($fileNameParts);
        $writable = in_array($extension, $this->exiftool->EXIFTOOL_SUPPORT_TAGS);
        return new JsonResponse([
            'filename' => $fileName,
            'metadata' => $metadata,
            'writable' => $writable
        ]);
    }

    /**
     * @Route ("/updateFileMetaData")
     *
     * You must define the user limits here!
     *
     * @MetacleanerRateLimit(
     *     scopes = {
     *          "free": {
     *              "limit": 0,
     *              "period": 86400
     *          }
     *     }
     * )
     * @MetacleanerSizeLimit(
     *     scopes = {
     *          "basic" : {
     *              "size": 5000000,
     *              "bandwidth": 1000000000
     *          },
     *          "advance" : {
     *              "size": 30000000,
     *              "bandwidth": 10000000000
     *          },
     *          "enterprise" : {
     *              "size": 30000000,
     *              "bandwidth": 100000000000
     *          }
     * })
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMetaData(Request $request): JsonResponse{
        $rawContent = $request->getContent(false);

        $content = json_decode($rawContent, true);
        $file = $content['file'];
        $metadata = $content['metadata'];
        $fileName = $file['filename'];
        $fileContent = $file['content'];
        $originalContent = base64_decode($fileContent);

        $start = microtime(true);
        $finalContent = $this->commandBus->handle(new GetUpdatedContentQuery($fileName, $originalContent, $metadata));
        $end = microtime(true);

        try {
            $newMetadata = $this->commandBus->handle(new GetMetadataQuery($fileName, $originalContent));
        } catch (\Throwable $exception) {
            $newMetadata = [];
        }
        return new JsonResponse([
            'filename' => $fileName,
            'content' => base64_encode($finalContent),
            'metadata' => $newMetadata,
        ]);
    }

}