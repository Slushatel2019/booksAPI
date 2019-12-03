<?php

namespace SiteApp\Models;



class Common
{



    public static function response($input)
    {
        #error_reporting(0);
        #$resp = ['data' => $input['data'], 'message' => $input['message'], 'status' => $input['status']];
        echo json_encode($input);
        exit;
    }
    public static function checkBook($book)
    {
        $name = true;
        if (isset($book['name'])) {
            $name = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]+)+$/", $book['name']);
        }
        $genre = true;
        if (isset($book['genre'])) {
            $genre = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]+)+$/", $book['genre']);
        }
        $pages = true;
        if (isset($book['pages'])) {
            $pages = preg_match_all("/^[0-9]*$/", $book['pages']);
        }
        return ($name && $genre && $pages) ? true : false;
    }
}
