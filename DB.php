<?php
class DB
{
    private static $instance = null;

    private function __construct()
    {
        $this->link = new PDO('mysql: host=localhost;dbname=library_bd', "root", "root");
    }

    private function __clone()
    { }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DB;
        }
        return self::$instance;
    }
    public function link()
    {
        return $this->link;
    }
}
