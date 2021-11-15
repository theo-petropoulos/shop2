<?php

abstract class Database{
    protected static $db;

    public function __construct(){
        self::$db = new DB\SQL(
            'mysql:host=localhost;port=3306;dbname=shop;charset=UTF8mb4',
            'root',
            ''
        );
    }
}