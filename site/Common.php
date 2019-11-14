<?php
function response($input)
{
    #error_reporting(0);
    #$resp = ['data' => $input['data'], 'message' => $input['message'], 'status' => $input['status']];
    echo json_encode($input);
    exit;
}
function check($book) {
    $name = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]+)+$/", $book['name']);
    $genre = preg_match("/^([A-Za-z]+[\s]?[A-Za-z]+)+$/", $book['genre']);
    $pages = preg_match("/\D/", $book['pages']);
    return (($name==true)&&($genre==true)&&($pages==false)) ? true : false;
     
}
function get($value, $default = null)
{
    return isset($value) ? $value : $default;
}

