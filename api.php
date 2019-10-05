<?php
require_once('Books.php');
function get($value, $default = null)
{
    return isset($value) ? $value : $default;
}
$user = get($_SERVER['PHP_AUTH_USER'], false);
$pass = get($_SERVER['PHP_AUTH_PW'], false);
if (!$user or !$pass) {
    header('WWW-Authenticate: Basic realm');
}
$login = Books::getInstance()->auth($user, $pass);
if (!$login) {
    header('WWW-Authenticate: Basic realm');
    exit('incorrect login or password');
}
switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            Books::getInstance()->allBooks();
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
            Books::getInstance()->booksId($matches[0]);
        }
        if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
            Books::getInstance()->count();
        }
        break;
    case "POST":
        if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
            $postdata = file_get_contents("php://input");
            $array = json_decode($postdata, true);
            if (is_array($array)) {
                echo (Books::getInstance()->add($array));
            }
            echo 'data format is not an array';
        }
        echo 'incorrect URL';
        break;
    case "PUT":
        $postdata = file_get_contents("php://input");
        $array = json_decode($postdata, true);
        if (is_array($array)) {
            echo (Books::getInstance()->update($array, $_SERVER['REQUEST_URI']));
            exit;
        }
        echo 'data format is not an array';
        break;
    case "DELETE":
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
            echo (Books::getInstance()->del($_SERVER['REQUEST_URI']));
            exit;
        }
        echo "incorrect url for delete";
        break;
}
