<?php

require_once 'Config.php';

class ConnectDB
{
    private $connect;
    private static $instance;

    private function __construct()
    {
        $config = Config::getConfig();
        $this->connect = mysqli_connect($config['hostname'], $config['username'], $config['password'], $config['database']);
        if (is_null(mysqli_query($this->connect, "SHOW TABLES FROM `test` LIKE 'people'")->fetch_assoc())) {
            mysqli_query($this->connect, "CREATE TABLE `person` (`id` SMALLINT UNSIGNED AUTO_INCREMENT, `name` VARCHAR(".$config['length']."),
                `surname` VARCHAR(".$config['length']."), `date_of_birth` DATE, `gender` ENUM('0','1'), `place_of_birth` VARCHAR(".$config["lengthCity"].")");
        }
    }

    public function getConnect()
    {
        return $this->connect;
    }


    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
