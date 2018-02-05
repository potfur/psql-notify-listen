<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\Commands;


use potfur\PSQLNotify\DB\DB;
use Symfony\Component\Console\Command\Command;

abstract class NotificationCommand extends Command
{
    protected const TABLE = 'events';
    protected const FUNCTION = 'emit_event';
    protected const TRIGGER = 'emitter';
    protected const CHANNEL = 'channel';

    protected $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function build(string $statement, array $params) : string
    {
        return str_replace(
            array_keys($params),
            array_values($params),
            $statement
        );
    }
}