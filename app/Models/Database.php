<?php

class Database
{
    public static function connect()
    {
$host = '127.0.0.1';
        $database = 'campus_booking';
        $user = 'root';
        $password = '';

        return new PDO(
            "mysql:host=$host;dbname=$database;charset=utf8mb4",
            $user,
            $password
        );
    }
}