<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

            self::$instance = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$instance;
    }
}
