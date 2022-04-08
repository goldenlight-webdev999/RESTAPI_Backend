<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands\Sandbox;


use App\Application\Command\ContactRequest\CreateContactRequest\CreateContactRequestCommand;
use DDD\Embeddable\EmailAddress;
use League\Tactician\CommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SendContactRequest extends Command
{
    private $commandBus;

    /**
     * SendContactRequest constructor.
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('sandbox:send_contact_request')
            ->setDescription('It sends a contact request email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(
            new CreateContactRequestCommand(
                'test',
                new EmailAddress('test@metacleaner.com'),
                '66666666',
                "Testing\n...\n\n...",
                true
            )
        );

    $output->writeln('[DONE]');
    }
}