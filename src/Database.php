<?php

declare(strict_types=1);

namespace EventHub;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3307';
            $db   = getenv('DB_NAME') ?: 'eventhub_db';
            $user = getenv('DB_USER') ?: 'eventhub_user';
            $pass = getenv('DB_PASS') ?: 'eventhub_pass';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException('Falha na conexão com o banco de dados MySQL.', 503, $e);
            }
        }

        return self::$instance;
    }
}
