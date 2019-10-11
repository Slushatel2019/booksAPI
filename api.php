<?php
require_once('Books.php');
require_once('Common.php');

$user = get($_SERVER['PHP_AUTH_USER'], false);
$pass = get($_SERVER['PHP_AUTH_PW'], false);
if (!$user or !$pass) {
    header('WWW-Authenticate: Basic realm');
    response(['data' => 'error', 'message' =>
    'incorrect login or password', 'status' => 403]);
}
$login = auth($user, $pass);
if (!$login) {
    header('WWW-Authenticate: Basic realm');
    response(['data' => 'error', 'message' => 'incorrect login or password', 'status' => 403]);
}
switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = allBooks();
            response($result);
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
            $result = booksId($matches[0]);
            response($result);
        }
        if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = counts();
            response($result);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    case "POST":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $postdata = file_get_contents("php://input");
            $array = json_decode($postdata, true);
            if (is_null($array) or !is_array($array)) {
                response(['message' => 'Incorrect input data', 'data' => $postdata, 'status' => 200]);
            }
            #error_reporting(0);
            if (!is_array($array[0])) {
                $array = [$array];
            }
            $res = [];
            foreach ($array as $book) {
                $result = add($book);
                array_push($res, $result);
            }
            response($res);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    case "PUT":
        $postdata = file_get_contents("php://input");
        $array = json_decode($postdata, true);
        if (is_null($array) or !is_array($array)) {
            response(['message' => 'Incorrect input data', 'data' => $postdata, 'status' => 200]);
        }
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            if (!is_array($array[0])) {
                $array = [$array];
            }
            $res = [];
            foreach ($array as $book) {
                $id = $book['id'];
                unset($book['id']);
                $result = update($book, $id);
                array_push($res, $result);
            }
            response($res);
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches[0]);
            if (!is_array($array[0])) {
                $array = [$array];
            }
            $res = [];
            foreach ($array as $book) {
                $result = update($book, $id);
                array_push($res, $result);
            }
            response($res);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    case "DELETE":
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches[0]);
            $result = del($id);
            response($result);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
}
