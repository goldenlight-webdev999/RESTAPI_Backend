<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands\Database;


use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserRole;
use App\Infrastructure\OAuth2\Entity\OAuth2Client;
use DDD\Embeddable\EmailAddress;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LoadInitialDataCliCommand extends Command
{
    private const DEV_SECRET = '4s702vda5o6c8c8kg08kckcko4g8k0kwswcs8k80gocs8ggw0w';
    private const DEV_CLIENT_ID = '36r5u9uleb8kc8kcwoksow4o4wswwcw8wsw4so0ockosowkowc';

    private const PROD_SECRET = '1m6fgtynmoxwow4osso44ck0kosg44c0ocog4wgsw8k88808os';
    private const PROD_CLIENT_ID = '5rh7prsyrdc8wkgsgw0og0gg44kkcocs0g0wkows8sksc4gcgk';

    private $clientManager;
    private $userRepository;
    private $userFactory;

    /**
     * LoadInitialDataCliCommand constructor.
     * @param ClientManagerInterface $clientManager
     * @param UserRepositoryInterface $userRepository
     * @param UserFactory $userFactory
     */
    public function __construct(ClientManagerInterface $clientManager, UserRepositoryInterface $userRepository, UserFactory $userFactory)
    {
        parent::__construct();
        $this->clientManager = $clientManager;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    protected function configure()
    {
        $this
            ->setName('database:load-initial-data')
            ->setDescription('This command load the initial data')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oauthClientExists = $this->clientManager->findClientBy([
            'id' => 1,
        ]);

        if (!$oauthClientExists) {
            $output->writeln('Client not found!');
            /**
             * @var OAuth2Client $devClient
             */
            $devClient = $this->clientManager->createClient();

            $devClient->setSecret(self::DEV_SECRET);
            $devClient->setRandomId(self::DEV_CLIENT_ID);
            $devClient->setName('Metacleaner DEV');
            $devClient->setRedirectUris(['http://localhost']);
            $devClient->setAllowedGrantTypes(['password']);

            $this->clientManager->updateClient($devClient);

            /**
             * @var OAuth2Client $prodClient
             */
            $prodClient = $this->clientManager->createClient();

            $prodClient->setSecret(self::PROD_SECRET);
            $prodClient->setRandomId(self::PROD_CLIENT_ID);
            $prodClient->setName('Metacleaner');
            $prodClient->setRedirectUris(['https://metacleaner.com', 'http://metacleaner.com']);
            $prodClient->setAllowedGrantTypes(['password']);

            $this->clientManager->updateClient($prodClient);
        }

        $userExists = $this->userRepository->getByEmail(new EmailAddress('metacleaner@opendatasecurity.io'));

        if (!$userExists) {
            $output->writeln('User not found!');

            $user = $this->userFactory->createUser(
                new EmailAddress('metacleaner@opendatasecurity.io'),
                'Admin',
                Uuid::fromString('f7a708b3-c427-4696-b9a4-4ce825f41163'),
                'opendatasecurity1!Ods',
                true
            );
            $user->addRole(User::ROLE_ADMIN);
            $this->userRepository->save($user);
        }

        $output->writeln('[DONE]');
    }
}