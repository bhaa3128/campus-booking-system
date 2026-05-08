<?php

class Database
{
    public static function connect()
    {
        $host = 'db';
        $database = 'campus_booking';
        $user = 'root';
        $password = 'root';

        return new PDO(
            "mysql:host=$host;dbname=$database;charset=utf8mb4",
            $user,
            $password
        );
    }
}