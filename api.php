<?php
require_once('books.php');
if (preg_match("[^/api/books(|/)$]",$_SERVER['REQUEST_URI']))
{
   books::getInstance()->allBooks();
}
if (preg_match("[^/api/books/([0-9]{1,})(|/)$]",$_SERVER['REQUEST_URI']))
{
    preg_match("/[0-9]{1,}/",$_SERVER['REQUEST_URI'],$matches);
    books::getInstance()->booksID($matches[0]);
}
if (preg_match("[^/api/books/count(|/)$]",$_SERVER['REQUEST_URI']))
{
    books::getInstance()->Count();
}
?>
