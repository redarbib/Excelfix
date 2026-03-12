<?php

class Database
{
    private PDO $pdo;

    public function __construct(string $host, string $name, string $user, string $pass)
    {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4';
        $this->pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
