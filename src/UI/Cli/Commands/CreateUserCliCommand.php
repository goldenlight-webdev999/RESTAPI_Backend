<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands;


use App\Application\Command\User\SignUp\SignUpCommand;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateUserCliCommand extends Command
{
    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
    }

    /**
     * @var CommandBus
     */
    private $commandBus;

    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Given a uuid and email, generates a new user.')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uuid = Uuid::uuid4();
        $email = $input->getArgument('email');
        $name = $input->getArgument('name');
        $password = $input->getArgument('password');

        $command = new SignUpCommand(
            new EmailAddress($email),
            $name,
            $password,
            $uuid,
            false
        );

        $this->commandBus->handle($command);
        $output->writeln('<info>User Created: </info>');
        $output->writeln('');
        $output->writeln(sprintf("Uuid: %s", $uuid->toString()));
        $output->writeln("Email: " . $email);
    }

}