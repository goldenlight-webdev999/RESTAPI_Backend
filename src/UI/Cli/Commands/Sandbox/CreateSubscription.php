<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands\Sandbox;


use App\Application\Command\Subscription\CreateSubscription\CreateSubscriptionCommand;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\Repository\UserRepositoryInterface;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateSubscription extends Command
{
    private $commandBus;
    private $userRepository;

    public function __construct(CommandBus $commandBus, UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
        $this->userRepository = $userRepository;
    }


    protected function configure()
    {
        $this
            ->setName('sandbox:create_subscription')
            ->setDescription('Setup subscription')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $payload = '{
  "id": "tok_br",
  "object": "token",
  "card": {
    "id": "card_1CnLuDDkjO2tcojreXn2sfgQ",
    "object": "card",
    "address_city": null,
    "address_country": null,
    "address_line1": null,
    "address_line1_check": null,
    "address_line2": null,
    "address_state": null,
    "address_zip": null,
    "address_zip_check": null,
    "brand": "Visa",
    "country": "US",
    "cvc_check": "pass",
    "dynamic_last4": null,
    "exp_month": 8,
    "exp_year": 2019,
    "fingerprint": "dYYZXHi55rAuzXiN",
    "funding": "credit",
    "last4": "4242",
    "metadata": {
    },
    "name": null,
    "tokenization_method": null
  },
  "client_ip": null,
  "created": 1531467693,
  "livemode": false,
  "type": "card",
  "used": false
  }';
        $user = $this->userRepository->get(Uuid::fromString('f7a708b3-c427-4696-b9a4-4ce825f41163'));
        $this->commandBus->handle(new CreateSubscriptionCommand(
            $user,
            SubscriptionTypeEnum::build('basic'),
            json_decode($payload, true)
        ));
    }
}