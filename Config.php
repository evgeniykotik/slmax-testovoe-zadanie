<?php

class Config
{
    private static array $config =
        [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'test',
            'length' => 40,
            'lengthCity' => 30
        ];

    public static function getConfig()
    {
        return Config::$config;
    }
}