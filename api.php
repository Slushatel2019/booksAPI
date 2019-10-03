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
}
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
