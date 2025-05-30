<?php

namespace App\Service;

use PDO;
use PDOException;

class DatabaseService
{
    private PDO $connection;

    public function __construct(string $dbUrl)
    {
        $parsed = parse_url($dbUrl);

        $host = $parsed['host'];
        $user = $parsed['user'];
        $pass = $parsed['pass'] ?? '';
        $dbname = ltrim($parsed['path'], '/');

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8",
                $user,
                $pass
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception('Erreur connexion BDD : ' . $e->getMessage());
        }
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }
}