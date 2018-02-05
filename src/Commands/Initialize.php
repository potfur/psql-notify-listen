<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\Commands;


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class Initialize extends NotificationCommand
{
    protected function configure()
    {
        $this
            ->setName('db:init')
            ->setDescription('Initialize database')
            ->setHelp('Creates sample database and notification trigger');

        $this->addOption(
            'clear',
            null,
            InputOption::VALUE_NONE,
            'Clears existing table and triggers before inserting them'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('clear')) {
            $this->getApplication()->get('db:clear')->run(new ArrayInput([]), $output);
        }

        $output->writeln('Created table: ' . static::TABLE);
        $this->db->execute(
            $this->build(
                'CREATE TABLE :table (id UUID PRIMARY KEY, payload JSONB)',
                [
                    ':table' => static::TABLE
                ]
            )
        );

        $output->writeln('Creating notification emitter to channel: ' . static::CHANNEL);
        $this->db->execute(
            $this->build(
                'CREATE FUNCTION :function() RETURNS trigger AS $$ DECLARE BEGIN PERFORM pg_notify(\':channel\', row_to_json(NEW)::text); RETURN NEW; END; $$ LANGUAGE plpgsql;',
                [
                    ':function' => static::FUNCTION,
                    ':channel' => static::CHANNEL,
                ]
            )
        );

        $output->writeln('Creating trigger: ' . static::TRIGGER);
        $this->db->execute(
            $this->build(
                'CREATE TRIGGER :trigger AFTER INSERT ON :table FOR EACH ROW EXECUTE PROCEDURE :function();',
                [
                    ':trigger' => static::TRIGGER,
                    ':table' => static::TABLE,
                    ':function' => static::FUNCTION
                ]
            )
        );
    }
}