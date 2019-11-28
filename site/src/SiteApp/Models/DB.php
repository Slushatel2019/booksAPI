<?php

namespace SiteApp\Models;

class DB
{
    private static $instance = null;

    private function __construct()
    {
        $this->link = new \PDO('mysql: host=localhost;dbname=library_bd', "root", "root");
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
    public function executeUpdate($query, $book)
    {
        $stmt = $this->link->prepare($query);
        foreach ($book as $key => $value) {
            if ($key === 'pages') {
                $stmt->bindValue(":$key", $value, \PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function executeAdd($query, $book)
    {
        $stmt = $this->link->prepare($query);
        $stmt->bindParam(":name", $book['name'], \PDO::PARAM_STR);
        $stmt->bindParam(":genre", $book['genre'], \PDO::PARAM_STR);
        $stmt->bindParam(":pages", $book['pages'], \PDO::PARAM_INT);
        $stmt->execute();
        $result['successfulAdd'] = $stmt->rowCount();
        $result['id'] = $this->link->lastInsertId();
        return $result;
    }
    public function executeAuth($query, $user)
    {
        $stmt = $this->link->prepare($query);
        $stmt->bindParam(':login', $user['login'], \PDO::PARAM_STR_CHAR);
        $stmt->bindParam(':password', $user['password'],  \PDO::PARAM_STR_CHAR);
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function executeCommon($query, $param)
    {
        $stmt = $this->link->prepare($query);
        $stmt->execute();
        switch ($param) {
            case 'rowCount':
                return $stmt->rowCount();
            case 'fetchAll':
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
            case 'fetch':
                return $stmt->fetch(\PDO::FETCH_ASSOC);
            case 'fetchColumn':
                return $stmt->fetchColumn();
        }
    }
}
