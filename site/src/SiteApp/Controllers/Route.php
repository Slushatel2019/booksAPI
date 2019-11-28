<?php

namespace SiteApp\Controllers;

use SiteApp\Models\Books;
use SiteApp\Models\Common;


class Route
{
    //public $log;

    public function __construct($loger,$user)
    {
        $this->log = $loger;
        $this->user= $user;
    }

    public function run()
    {
        $books = new Books($this->log);
        switch ($_SERVER['REQUEST_METHOD']) {
            case "GET":
                $this->log->info('username= ' . $this->user, array('Method' => 'GET', 'inputUri' => $_SERVER['REQUEST_URI']));
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $books->allBooks();
                    Common::response($result);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
                    $result = $books->booksId($matches[0]);
                    Common::response($result);
                }
                if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $books->count();
                    Common::response($result);
                }
                break;

            case "POST":
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $postdata = file_get_contents("php://input");
                    $this->log->info('username= ' . $this->user, array('Method' => 'POST', 'inputData' => $postdata));
                    $array = json_decode($postdata, true);
                    if (is_null($array) or !is_array($array)) {
                        Common::response(['message' => 'Incorrect input data', 'data' => $postdata, 'status' => 200]);
                    }
                    //error_reporting(0);
                    if (@!is_array($array[0])) {
                        $array = [$array];
                    }
                    $res = [];
                    foreach ($array as $book) {
                        if (Common::check($book)) {
                            $result = $books->add($book);
                            $res = array_merge_recursive($res, $result);
                        } else {
                            Common::response(['message' => 'Incorrect input data', 'data' => $book, 'status' => 200]);
                        }
                    }
                    Common::response($res);
                }
                break;

            case "PUT":
                $postdata = file_get_contents("php://input");
                $this->log->info('username= ' . $this->user, array('Method' => 'PUT', 'inputData' => $postdata));
                $array = json_decode($postdata, true);
                if (is_null($array) or !is_array($array)) {
                    Common::response(['message' => 'Incorrect input data', 'data' => $postdata, 'status' => 200]);
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
                        $result = $books->update($book, $id);
                        $res = array_merge_recursive($res, $result);
                    }
                    $finishResponseArray = array_merge_recursive($res, $responseFalseId);
                    Common::response($finishResponseArray);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    //error_reporting(0);
                    if (@is_array($array[0])) {
                        Common::response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
                    }
                    if (Common::check($array)) {
                        preg_match('/[0-9]+/', $_SERVER['REQUEST_URI'], $matches);
                        $id = implode($matches);
                        $result = $books->update($array, $id);
                        Common::response($result);
                    } else {
                        Common::response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
                    }
                }
                break;
            case "DELETE":
                $this->log->info('username= ' . $this->user, array('Method' => 'DELETE', 'inputUri' => $_SERVER['REQUEST_URI']));
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
                    $id = implode($matches[0]);
                    $result = $books->del($id);
                    Common::response($result);
                }
                break;
        }
        Common::response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
}
}
