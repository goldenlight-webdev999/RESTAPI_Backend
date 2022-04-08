<?php

declare(strict_types=1);


namespace App\UI\Cli\Commands\Database;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitDatabaseCliCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('database:init')
            ->setDescription('This command creates databases, tables and runs all migrations. It also insert a oAuth client')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $createDatabaseCommand = $this->getApplication()->get('doctrine:database:create');
        try {
            $createDatabaseCommand->run(new ArrayInput([
                '--if-not-exists' => true,
            ]), $output);
        } catch (\Throwable $exception) {}


        $migrateDatabaseCommand = $this->getApplication()->get('doctrine:migrations:migrate');
        try {

            $migrateDatabaseInput = new ArrayInput([]);
            $migrateDatabaseInput->setInteractive(false);

            $migrateDatabaseCommand->run($migrateDatabaseInput, $output);
        } catch (\Throwable $exception) {}

        $output->writeln('[DONE]');

    }
}