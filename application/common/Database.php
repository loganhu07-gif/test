<?php
namespace app\common;

use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection) {
            return self::$connection;
        }
        $config = require __DIR__ . '/../config/config.php';
        $db = $config['db'];
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['database'],
            $db['charset']
        );
        $pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        self::$connection = $pdo;
        self::migrate();
        return self::$connection;
    }

    private static function migrate(): void
    {
        $pdo = self::$connection;
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(64) NOT NULL UNIQUE,
                password VARCHAR(128) NOT NULL,
                salt VARCHAR(64) NOT NULL,
                role VARCHAR(32) NOT NULL,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "CREATE TABLE IF NOT EXISTS test_results (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                answers JSON,
                score INT NOT NULL DEFAULT 0,
                submitted_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "CREATE TABLE IF NOT EXISTS treehole_posts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                content TEXT NOT NULL,
                tags JSON,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            "CREATE TABLE IF NOT EXISTS captcha_tokens (
                token VARCHAR(64) PRIMARY KEY,
                answer INT NOT NULL,
                created_at INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];
        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }
    }
}
