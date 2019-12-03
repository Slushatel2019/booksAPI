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

    private function execute($query, $params)
    {
        $stmt = $this->link->prepare($query);
        if ($params) {
            foreach ($params as $param) {
                $stmt->bindValue($param['key'], $param['value'], $param['type']);
            }
        }
        $stmt->execute();
        return $stmt;
    }
    private function executeUpsert($query, $params)
    {
        $stmt = $this->execute($query, $params);
        $result['rowCount'] = $stmt->rowCount();
        $result['lastInsertId'] = $this->link->lastInsertId();
        return $result;
    }
    public function executeUpdate($query, $params)
    {
        return $this->executeUpsert($query, $params);
    }
    public function executeInsert($query, $params)
    {
        return $this->executeUpsert($query, $params);
    }
    public function executeDelete($query, $params)
    {
        return $this->executeUpsert($query, $params);
    }
    public function executeAuth($query, $params)
    {
        return $this->executeUpsert($query, $params);
    }
    public function executeSelect($query, $param)
    {
        $stmt = $this->execute($query, false);
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
