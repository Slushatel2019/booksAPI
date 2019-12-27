<?php

namespace SiteApp\Controllers;

use SiteApp\Models\Books;
use SiteApp\Models\Common;
use SiteApp\Models\Users;

class Route
{
    public $log;

    public function __construct($loger)
    {
        $this->log = $loger;
        $this->model = new Books($this->log);
        $this->user = Users::getInstance();
    }

    public function run()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case "GET":
                $this->log->info('Method - GET ' . 'inputUri - ' . $_SERVER['REQUEST_URI']);
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $this->model->getAllBooks();
                    Common::response($result);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match("/[0-9]{1,}/", $_SERVER['REQUEST_URI'], $matches);
                    $result = $this->model->getBooksById($matches[0]);
                    Common::response($result);
                }
                if (preg_match("[^/api/books/count(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $this->model->getCountBooks();
                    Common::response($result);
                }
                if (preg_match("[^/api/users(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $result = $this->user->getAllUsers();
                    Common::response($result);
                }
                break;

            case "POST":
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->model->checkInputData('POST');
                    if (!isset($array[0]) or !is_array($array[0])) {
                        $array = [$array];
                    }
                    $result = $this->model->addBooks($array);
                    Common::response($result);
                }
                if (preg_match("[^/api/users(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $inputData = Users::getInstance()->firstCheckInputData();
                    if ($inputData) {
                        $pattern = ['login' => '', 'password' => '', 'email' => '', 'userType' => ''];
                        if (count(array_diff_key($inputData, $pattern)) == 0) {
                            return (Common::checkUserForm($inputData)) ? Users::getInstance()->addNewUser($inputData) : false;
                        }
                    }
                }
                break;

            case "PUT":
                if (preg_match("[^/api/books(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->model->checkInputData('PUT');
                    if (!isset($array[0]) or !is_array($array[0])) {
                        $array = [$array];
                    }
                    $result = $this->model->updateBooksWithId($array);
                    Common::response($result);
                }
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    $array = $this->model->checkInputData('PUT');
                    if (@is_array($array[0])) {
                        Common::response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
                    }
                    $result = $this->model->updateBookByUriId($array);
                    Common::response($result);
                }
                break;

            case "DELETE":
                $this->log->info('Method - DELETE  inputUri - ' . $_SERVER['REQUEST_URI']);
                if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])) {
                    preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
                    $id = implode($matches[0]);
                    $result = $this->model->deleteBookById($id);
                    Common::response($result);
                }
                if (
                    preg_match("[^/api/users/([0-9]{1,})(|/)$]", $_SERVER['REQUEST_URI'])
                    and $this->user->getUserTypeByToken($_COOKIE['token']) == 'admin'
                ) {
                    preg_match_all('/[0-9]/', $_SERVER['REQUEST_URI'], $matches);
                    $id = implode($matches[0]);
                    $result = $this->user->deleteUserById($id);
                    Common::response($result);
                }
                break;
        }
        Common::response(['message' => 'incorrect URL', 'data' => 'error', 'status' => 404]);
    }
}
