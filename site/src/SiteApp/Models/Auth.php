<?php

namespace SiteApp\Models;

use SiteApp\Models\DB;
use SiteApp\Models\Common;
use SiteApp\Models\Users;

class Auth
{
    public function __construct($loger)
    {
        $this->log = $loger;
        $this->db = DB::getInstance();
        $this->user = Users::getInstance();
    }
    public function signIn()
    {
        return $this->checkCookieAndToken() ? true : $this->secondCheckInputData();
    }
    public function secondCheckInputData()
    {
        $inputData = Users::getInstance()->firstCheckInputData();
        if ($inputData) {
            $patternSignIn = ['login' => '', 'password' => ''];
            if (count(array_diff_key($inputData, $patternSignIn)) == 0) {
                return $this->userAuth($inputData);
            }
            $patternSignUp = ['login' => '', 'password' => '', 'email' => '', 'userType' => ''];
            if (
                count(array_diff_key($inputData, $patternSignUp)) == 0
                and $inputData['userType'] === 'user'
            ) {
                return (Common::checkUserForm($inputData)) ? $this->user->addNewUser($inputData) : false;
            }
        } else {
            return false;
        }
    }
    private function userAuth($user)
    {
        if (Common::checkUserForm($user)) {
            $checked = $this->checkUserLoginPasswordGetIdUserType($user);
            if ($checked) {
                $token = $this->createSaveAndGetToken($checked['id']);
                if ($token) {
                    setcookie('token', $token, time() + 5);
                    Common::response(['data' => $checked['userType'], 'message' => 'ok', 'status' => 200]);
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
    private function checkUserLoginPasswordGetIdUserType($user)
    {
        $query = "SELECT id, userType FROM users WHERE 
        BINARY login = :login AND BINARY password = :password";
        $params = [
            ['key' => ':login', 'value' => $user['login'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':password', 'value' => $user['password'], 'type' => \PDO::PARAM_STR_CHAR]
        ];
        $result = $this->db->executeSelect($query, 'fetch', $params);
        return ($result != null) ? $result : false;
    }
    private function createSaveAndGetToken($id)
    {
        $token = bin2hex(random_bytes(10));
        $query = "UPDATE `users` SET `token` = :token WHERE `id` = :id";
        $params = [
            ['key' => ':token', 'value' => $token, 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':id', 'value' => $id, 'type' => \PDO::PARAM_INT]
        ];
        $result = $this->db->executeUpdate($query, $params);
        if (isset($result['rowCount']) and $result['rowCount'] == 1) {
            return $token;
        } else {
            if (isset($result['dbError'])) {
                Common::response(['data' => '', 'message' => $result['dbError'], 'status' => 200]);
            }
        }
        return false;
    }
}
