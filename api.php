<?php
require_once('Books.php');
function get($value, $default = null) {
    return isset($value) ? $value : $default;
}

$user = get($_SERVER['PHP_AUTH_USER'], false);
$pass = get($_SERVER['PHP_AUTH_PW'], false);
if (!$user or !$pass) {
    header('WWW-Authenticate: Basic realm');
    exit('incorrect login or password');
}

$login = Books::getInstance()->auth($user, $pass);
if (!$login) {
    header('WWW-Authenticate: Basic realm');
    exit('incorrect login or password');
}

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = Books::getInstance()->allBooks();
            Books::getInstance()->response($result);
            exit;
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
            $result = Books::getInstance()->booksId($matches[0]);
            Books::getInstance()->response($result);
            exit;
        }
        if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = Books::getInstance()->count();
            Books::getInstance()->response($result);
            exit;
        }
        break;
    case "POST":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $postdata = file_get_contents("php://input");
            $array = json_decode($postdata, true);
            if (is_null($array) or !is_array($array)) {
                Books::getInstance()->response(['message' => 'Incorrect input data', 'data' => $postdata]);
            }
            var_dump($array);

            if (!is_array($array[0])) {
                $array = [$array];
            }
            $results = [];
            foreach ($array as $book) {
                $result = Books::getInstance()->add($book);
                $results += $result;
            }
            Books::getInstance()->response($results);
        }
        Books::getInstance()->response(['message' => 'incorrect URL']);
    case "PUT":
        $postdata = file_get_contents("php://input");
        $array = json_decode($postdata, true);
        $array = is_array($array) ? $array : exit('data format is not an array');
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $result = Books::getInstance()->update($array);
            Books::getInstance()->response($result);
            exit;
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches[0]);
            $result = Books::getInstance()->update($array, $id);
            Books::getInstance()->response($result);
            exit;
        }
        exit('incorrect URL');
        break;
    case "DELETE":
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches[0]);
            $result = Books::getInstance()->del($id);
            Books::getInstance()->response($result);
            exit;
        }
        echo "incorrect url for delete";
        break;
}
