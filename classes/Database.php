<?php

namespace Rayac;


class Database
{
    private static $pdo;
    private function __construct()
    {
    }
    public static function getPDO()
    {
        if (self::$pdo === null) {
            self::$pdo = new \PDO('mysql:host=localhost;dbname=homestead;charset=utf8', 'homestead', 'secret');
        }
        return self::$pdo;
    }
}