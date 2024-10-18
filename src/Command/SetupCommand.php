<?php

namespace HowMAS\CoreMSBundle\Command;

use Pimcore\Cache;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use HowMAS\CoreMSBundle\Service\DatabaseService;

class SetupCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('hcore:setup')
            ->setDescription('HowMAS CoreMS setup database')
            ->addOption(
                // this is the name that users must type to pass this option (e.g. --iterations=5)
                'update',
                // this is the optional shortcut of the option name, which usually is just a letter
                // (e.g. `i`, so users pass it as `-i`); use it for commonly used options
                // or options with long names
                'u',
                // this is the type of option (e.g. requires a value, can be passed more than once, etc.)
                InputOption::VALUE_NONE,
                // the option description displayed when showing the command help
                'Update custom tables in database'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');

        if ((bool) $update) {
            $this->writeInfo("RUN: update tables");
            DatabaseService::updateTables();
        } else {
            $this->writeInfo("RUN: create tables");
            DatabaseService::createTables();
        }

        $this->writeInfo("RUN: Clear all cache");
        Cache::clearAll();

        return AbstractCommand::SUCCESS;
    }
}
