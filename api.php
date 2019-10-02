<?php
require_once('Books.php');
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm');
}
if (Books::getInstance()->auth($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {

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
}
else {
    echo 'incorrect login or password';
    header('WWW-Authenticate: Basic realm'); 
}
