<?php

namespace SiteApp\Models;

class Common
{
    public static function response($input)
    {
        echo json_encode($input);
        exit;
    }
    public static function checkBook($book)
    {
        $name = true;
        if (isset($book['name'])) {
            $name = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]?)+$/", $book['name']);
        }
        $genre = true;
        if (isset($book['genre'])) {
            $genre = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]?)+$/", $book['genre']);
        }
        $pages = true;
        if (isset($book['pages'])) {
            $pages = preg_match_all("/^[0-9]*$/", $book['pages']);
        }
        return ($name && $genre && $pages) ? true : false;
    }
    public static function checkUserForm($user)
    {
        $login = !preg_match("/[^A-Za-z0-9]/", $user['login']);
        $email = true;
        if (isset($user['email'])) {
            $email = filter_var($user['email'], FILTER_VALIDATE_EMAIL);
        }
        $password = !preg_match("/[^A-Za-z0-9_-]/", $user['password']);
        return ($login && $email && $password) ? $user : false;
    }
}
