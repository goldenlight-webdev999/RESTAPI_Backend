<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands\Sandbox;


use App\Application\Command\Subscription\ChangeSubscriptionPlan\ChangeSubscriptionTypeCommand;
use App\Domain\Subscription\Subscription;
use App\Domain\Subscription\ValueObject\SubscriptionTypeEnum;
use App\Domain\User\Repository\UserRepositoryInterface;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpgradeSubscription extends Command
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
            ->setName('sandbox:upgrade_subscription')
            ->setDescription('Setup subscription')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->userRepository->get(Uuid::fromString('f7a708b3-c427-4696-b9a4-4ce825f41163'));
        $this->commandBus->handle(
            new ChangeSubscriptionTypeCommand(
                $user,
                SubscriptionTypeEnum::build(Subscription::TYPE_ADVANCE)
            )
        );
    }
}