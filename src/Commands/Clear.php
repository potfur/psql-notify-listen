<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\Commands;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Clear extends NotificationCommand
{
    protected function configure()
    {
        $this
            ->setName('db:clear')
            ->setDescription('Clears database')
            ->setHelp('Removes sample table and trigger');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dropping table: ' . static::TABLE);
        $this->db->execute(
            $this->build(
                'DROP TRIGGER IF EXISTS :trigger ON :table',
                [
                    ':trigger' => static::TRIGGER,
                    ':table' => static::TABLE
                ]
            )
        );


        $output->writeln('Dropping emiting function: ' . static::TABLE);
        $this->db->execute(
            $this->build(
                'DROP FUNCTION IF EXISTS :function',
                [
                    ':function' => static::FUNCTION
                ]
            )
        );

        $output->writeln('Dropping table');
        $this->db->execute(
            $this->build(
                'DROP TABLE IF EXISTS :table',
                [
                    ':table' => static::TABLE
                ]
            )
        );
    }
}