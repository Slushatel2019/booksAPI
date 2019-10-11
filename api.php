<?php
require_once('DB.php');
require_once('Common.php');
$user = get($_SERVER['PHP_AUTH_USER'], false);
$pass = get($_SERVER['PHP_AUTH_PW'], false);
if (!$user or !$pass) {
    header('WWW-Authenticate: Basic realm');
    response(['data' => 'error', 'message' =>
    'incorrect login or password', 'status' => 403]);
}
$login = DB::getInstance()->auth($user, $pass);
if (!$login) {
    header('WWW-Authenticate: Basic realm');
    response(['data' => 'error', 'message' => 'incorrect login or password', 'status' => 403]);
}
switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = DB::getInstance()->allBooks();
            response($result);
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
            $result = DB::getInstance()->booksId($matches[0]);
            response($result);
        }
        if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = DB::getInstance()->count();
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
                $result = DB::getInstance()->add($book);
                $res = array_merge_recursive($res, $result);
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
            $responseFalseId = [];
            $res = [];
            foreach ($array as $book) {
                if (!is_integer($book['id']) or is_null($book['id'])) {
                    $response = ['message' => 'no id, no changes', 'data' => $book, 'status' => 200];
                    $responseFalseId = array_merge_recursive($responseFalseId, $response);
                    continue;
                }
                $id = $book['id'];
                $result = DB::getInstance()->update($book, $id);
                $res = array_merge_recursive($res, $result);
            }
            $finishResponseArray = array_merge_recursive($res, $responseFalseId);
            response($finishResponseArray);
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            if (is_array($array[0])) {
                response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
            }
            preg_match('/[0-9]+/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches);
            $result = DB::getInstance()->update($array, $id);
            response($result);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    case "DELETE":
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches[0]);
            $result = DB::getInstance()->del($id);
            response($result);
        }
        response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
}
