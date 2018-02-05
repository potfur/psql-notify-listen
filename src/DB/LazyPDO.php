<?php

declare (strict_types = 1);

namespace potfur\PSQLNotify\DB;


class LazyPDO implements DB
{
    private $urn;
    private $pdo;

    public function __construct(string $urn)
    {
        $this->urn = $urn;
    }

    private function connection() : \PDO
    {
        if ($this->pdo) {
            return $this->pdo;
        }

        return $this->pdo = new \PDO($this->urn);
    }

    public function execute(string $statement, array $params = []) : array
    {
        $stmt = $this->connection()->prepare($statement);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function notify(string $channel, int $timeout)
    {
        $this->execute(str_replace(':channel', $channel, 'LISTEN :channel'));
        return $this->connection()->pgsqlGetNotify(\PDO::FETCH_ASSOC, $timeout);
    }
}