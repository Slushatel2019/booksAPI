<?php

namespace SiteApp\Models;

use SiteApp\Models\DB;

class Auth
{

    public function __construct($loger)
    {
        $this->log = $loger;
        $this->db = DB::getInstance();
    }
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
    public function checkUserLoginPassword()
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
    }
}
