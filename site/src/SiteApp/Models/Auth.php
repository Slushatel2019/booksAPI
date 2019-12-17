<?php

namespace SiteApp\Models;

use SiteApp\Models\DB;
use SiteApp\Models\Common;

class Auth
{

    public function __construct($loger)
    {
        $this->log = $loger;
        $this->db = DB::getInstance();
    }

    public function signIn()
    {
        return $this->checkCookieAndToken() ? true : $this->checkInputDataAndSetCookie();
    }
    private function checkInputDataAndSetCookie()
    {
        $postdata = file_get_contents("php://input");
        if ($postdata != null) {
            $user = json_decode($postdata, true);
            if (isset($user['login']) and isset($user['password'])) {
                $id = $this->checkUserLoginPasswordAndGetId($user);
                if ($id) {
                    $token = $this->createSaveAndGetToken($id);
                    if ($token) {
                        setcookie('token', $token, time() + 10);
                        Common::response(['data' => '', 'message' => 'ok', 'status' => 200]);
                    }
                }
            }
        }
        return false;
    }
    private function checkCookieAndToken()
    {
        return isset($_COOKIE['token']) ? $this->checkToken($_COOKIE['token']) : false;
    }
    private function checkToken($token)
    {
        $query = "SELECT id FROM users WHERE 
           BINARY token = :token";
        $params = [['key' => ':token', 'value' => $token, 'type' => \PDO::PARAM_STR_CHAR]];
        return ($this->db->executeSelect($query, 'rowCount', $params) === 1) ? true : false;
    }

    private function checkUserLoginPasswordAndGetId($user)
    {
        $query = "SELECT id FROM users WHERE 
        BINARY login = :login AND BINARY password = :password";
        $params = [
            ['key' => ':login', 'value' => $user['login'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':password', 'value' => $user['password'], 'type' => \PDO::PARAM_STR_CHAR]
        ];
        $id = $this->db->executeSelect($query, 'fetchColumn', $params);
        return ($id != null) ? $id : false;
    }
    private function createSaveAndGetToken($id)
    {
        $token = bin2hex(random_bytes(10));
        $query = "UPDATE `users` SET `token` = :token WHERE `id` = :id";
        $params = [
            ['key' => ':token', 'value' => $token, 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':id', 'value' => $id, 'type' => \PDO::PARAM_INT]
        ];
        return ($this->db->executeUpdate($query, $params)['rowCount'] === 1) ? $token : false;
    }

    /*
    public function getUserLoginPassword($force = false)
    {
        if (
            !isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])
            or $_SERVER['PHP_AUTH_USER'] == '' or $_SERVER['PHP_AUTH_PW'] == '' or $force
        ) {
            header('WWW-Authenticate: Basic realm');
            return false;
        }
        return ['login' => $_SERVER['PHP_AUTH_USER'], 'password' => $_SERVER['PHP_AUTH_PW']];
    }
    public function checkUserLoginPassword1()
    {
        $user = $this->getUserLoginPassword();
        $query = "SELECT id FROM users WHERE 
           BINARY login = :login AND BINARY password = :password";
        $params = [
            ['key' => ':login', 'value' => $user['login'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':password', 'value' => $user['password'], 'type' => \PDO::PARAM_STR_CHAR]
        ];
        if ($this->db->executeSelect($query, 'rowCount', $params) === 0) {
            $force = true;
            $this->getUserLoginPassword($force);
        }
        $result = $this->db->executeSelect($query, 'rowCount', $params);
        return ($result === 1) ? true : false;
    }*/
}
