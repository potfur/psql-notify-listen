<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\Commands;


use potfur\PSQLNotify\DB\DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

final class ListenToChannel extends NotificationCommand
{
    protected const TIMEOUT = 30000;

    protected function configure()
    {
        $this
            ->setName('channel:listen')
            ->setDescription('Listen to channel')
            ->setHelp('Listens to channel to retrieve notifications');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        while (true) {
            $output->writeln('Listening to channel: ' . static::CHANNEL);

            while ($result = $this->db->notify(static::CHANNEL, static::TIMEOUT)) {
                $result['payload'] = json_decode($result['payload']);
                $output->writeln(print_r($result));
            }

            $output->writeln('Timed out...');
        }
    }
}