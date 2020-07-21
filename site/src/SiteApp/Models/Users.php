<?php

namespace SiteApp\Models;

use SiteApp\Models\DB;

class Users
{
    private static $instance = null;
    private function __construct()
    {
        $this->db = DB::getInstance();
    }
    private function __clone()
    {
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Users;
        }
        return self::$instance;
    }
    public function firstCheckInputData()
    {
        $postdata = file_get_contents("php://input");
        return ($postdata != null) ? json_decode($postdata, true) : false;
    }
    public function addNewUser($user)
    {
        $query = 'INSERT INTO `users` (`login`, `email`, `password`, `userType`) 
        VALUES (:login, :email, :password, :userType)';
        $params = [
            ['key' => ':login', 'value' => $user['login'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':email', 'value' => $user['email'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':password', 'value' => $user['password'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':userType', 'value' => $user['userType'], 'type' => \PDO::PARAM_STR_CHAR]
        ];
        $result = $this->db->executeInsert($query, $params);
        if (isset($result['rowCount']) and $result['rowCount'] == 1) {
            Common::response(['data' => '', 'message' => 'ok', 'status' => 200]);
        } else {
            if (isset($result['dbError']) and $result['dbError'][1] == 1062) {
                Common::response(['data' => '', 'message' => 'login is already existed', 'status' => 200]);
            }
        }
        return false;
    }
    public function getAllUsers()
    {
        $query = "SELECT * FROM users";
        $data = $this->db->executeSelect($query, 'fetchAll');
        return ['data' => $data, 'message' => 'all users', 'status' => 200];
    }
    public function deleteUserById($id)
    {
        $query = "DELETE FROM `users` WHERE id=:id";
        $params = [['key' => 'id', 'value' => $id, 'type' => \PDO::PARAM_INT]];
        $result = $this->db->executeDelete($query, $params);
        return ['data' => ['id' => $id], 'message' => $result['rowCount'] . ' user is deleted', 'status' => 200];
    }
    public function getUserTypeByToken($token)
    {
        $query = "SELECT userType FROM users WHERE 
           BINARY token = :token";
        $params = [['key' => ':token', 'value' => $token, 'type' => \PDO::PARAM_STR_CHAR]];
        return $this->db->executeSelect($query, 'fetchColumn', $params);
    }
}
