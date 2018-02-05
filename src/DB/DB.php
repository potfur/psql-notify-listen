<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\DB;


interface DB
{
    public function execute(string $statement, array $params = []) : array;

    public function notify(string $channel, int $timeout);
}