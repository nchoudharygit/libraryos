<?php

class Database
{

    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            self::$instance = new PDO($dsn, $config['username'], $config['password']);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
