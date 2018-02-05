<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\Commands;


use potfur\PSQLNotify\DB\DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SendToChannel extends NotificationCommand
{
    protected function configure()
    {
        $this
            ->setName('channel:send')
            ->setDescription('Send entry to channel')
            ->setHelp('Inserts single entry into database to trigger notification');

        $this->addArgument(
            'repeat',
            InputArgument::OPTIONAL,
            'Repeats execution N times',
            1
        );

        $this->addArgument(
            'delay',
            InputArgument::OPTIONAL,
            'Delay between repeats',
            0
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        for ($i = 0; $i < (int) $input->getArgument('repeat'); $i++) {
            $output->writeln('Adding entry ' . $i);
            $this->db->execute(
                $this->build(
                    'INSERT INTO :table (id, payload) VALUES (:id, :payload)',
                    [
                        ':table' => static::TABLE
                    ]
                ),
                [
                    ':id' => (string) Uuid::uuid4(),
                    ':payload' => json_encode(
                        [
                            'some' => 'value',
                            'entryNo' => $i
                        ]
                    )
                ]
            );

            sleep((int) $input->getArgument('delay'));
        }
    }
}