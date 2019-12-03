<?php

namespace SiteApp\Controllers;

use SiteApp\Models\Books;
use SiteApp\Models\Common;


class Route
{
    //public $log;

    public function __construct($loger, $user)
    {
        $this->log = $loger;
        $this->user = $user;
        $this->modal = new Books($this->log);
    }

    public function run()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case "GET":
                $this->log->info('username= ' . $this->user, array('Method' => 'GET', 'inputUri' => $_SERVER['REQUEST_URI']));
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $this->modal->getAllBooks();
                    Common::response($result);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
                    $result = $this->modal->getBooksById($matches[0]);
                    Common::response($result);
                }
                if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $this->modal->getCountBooks();
                    Common::response($result);
                }
                break;

            case "POST":
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->modal->checkInputData($this->user, 'POST');
                    if (!isset($array[0]) or !is_array($array[0])) {
                        $array = [$array];
                    }
                    $this->modal->addBooks($array);
                }
                break;

            case "PUT":
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->modal->checkInputData($this->user, 'PUT');
                    if (!isset($array[0]) or !is_array($array[0])) {
                        $array = [$array];
                    }
                    $this->modal->updateBooksWithId($array);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->modal->checkInputData($this->user, 'PUT');
                    if (@is_array($array[0])) {
                        Common::response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
                    }
                    $this->modal->updateBookByUriId($array);
                }
                break;

            case "DELETE":
                $this->log->info('username= ' . $this->user, array('Method' => 'DELETE', 'inputUri' => $_SERVER['REQUEST_URI']));
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
                    $id = implode($matches[0]);
                    $result = $this->modal->deleteById($id);
                    Common::response($result);
                }
                break;
        }
        Common::response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    }
}
