<?php
namespace app\includes;
use PDO;

class MySqlConnect {
    private static $instance = null;
    private $conn;

    // The db connection is established in the private constructor.
    private function __construct()
    {
        $this->conn = new PDO("mysql:host={$_ENV['DB_HOST']};
        dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'],$_ENV['DB_PASSWORD'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    }

    public static function getInstance()
    {
        if(!self::$instance)
        {
            self::$instance = new MySqlConnect();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}