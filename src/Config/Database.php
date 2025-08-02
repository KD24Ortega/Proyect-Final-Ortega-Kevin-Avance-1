<?php

declare(strict_types=1);

namespace App\Config;

use PDO; #Establecer la conexión a la base de datos

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if(self::$instance === null) {
            $host       = 'localhost';
            $dbName     = 'project_db';
            $username   = 'root';
            $password   = '1234';
            $charset    = 'utf8mb4';
            $dsn        = "mysql:host={$host};dbname={$dbName};charset={$charset}";
            $options    = [
                PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES      => false
            ];

            self::$instance =new PDO($dsn, $username, $password, $options);
        }

        return self::$instance;
    }
}