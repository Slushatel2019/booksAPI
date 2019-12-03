<?php

namespace SiteApp\Models;

use SiteApp\Models\DB;

class Books
{
    //public $log;

    public function __construct($loger)
    {
        $this->log = $loger;
        $this->db = DB::getInstance();
    }
    public function checkInputData($user, $method)
    {
        $postdata = file_get_contents("php://input");
        $this->log->info('username= ' . $user, array('Method' => $method, 'inputData' => $postdata));
        $array = json_decode($postdata, true);
        return (is_null($array) or !is_array($array)) ? Common::response([
            'message' => 'Incorrect input data',
            'data' => $postdata, 'status' => 200
        ]) : $array;
    }
    public function addBooks($array)
    {
        $res = [];
        foreach ($array as $book) {
            if (Common::checkBook($book)) {
                $result = $this->addBook($book);
                $res = array_merge_recursive($res, $result);
            } else {
                return Common::response(['message' => 'Incorrect input data', 'data' => $book, 'status' => 200]);
            }
        }
        return Common::response($res);
    }
    function addBook($data)
    {
        $arrayTable = array_flip(['name', 'genre', 'pages']);
        $book = array_intersect_key($data, $arrayTable);
        $dif = array_diff_key($arrayTable, $book);
        if (count($book) != count($arrayTable)) {
            return ['data' => $data, 'message' => 'Needed fields are ' .
                implode(',', array_keys($dif)), 'status' => 500];
        }
        $query = 'INSERT INTO `book` (`name`, `genre`, `pages`) 
        VALUES (:name, :genre, :pages)';
        $params = [
            ['key' => ':name', 'value' => $book['name'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':genre', 'value' => $book['genre'], 'type' => \PDO::PARAM_STR_CHAR],
            ['key' => ':pages', 'value' => $book['pages'], 'type' => \PDO::PARAM_INT]
        ];
        $result = $this->db->executeInsert($query, $params);
        return ['data' => ['id' => $result['lastInsertId']], 'message' => $result['rowCount'] . ' added book', 'status' => 200];
    }
    public function updateBooksWithId($array)
    {
        $responseFalseId = [];
        $res = [];
        foreach ($array as $book) {
            if (!isset($book['id']) or !is_integer($book['id']) or is_null($book['id'])) {
                $response = ['message' => 'no id, no changes', 'data' => $book, 'status' => 200];
                $responseFalseId = array_merge_recursive($responseFalseId, $response);
                continue;
            }
            $id = $book['id'];
            $result = $this->internalFunctionUpdateBook($book, $id);
            $res = array_merge_recursive($res, $result);
        }
        $finishResponseArray = array_merge_recursive($res, $responseFalseId);
        Common::response($finishResponseArray);
    }
    public function updateBookByUriId($array)
    {
        if (Common::checkBook($array)) {
            preg_match('/[0-9]+/', $_SERVER['REQUEST_URI'], $matches);
            $id = implode($matches);
            $result = $this->internalFunctionUpdateBook($array, $id);
            Common::response($result);
        } else {
            Common::response(['message' => 'Incorrect input data', 'data' => $array, 'status' => 200]);
        }
    }
    public function internalFunctionUpdateBook($data, $id)
    {
        $arrayTable = array_flip(['name', 'genre', 'pages']);
        $book = array_intersect_key($data, $arrayTable);
        $setArr = [];
        $params = [];
        foreach ($book as $key => $value) {
            $setArr[] = "$key=:$key";
            if ($key === 'pages') {
                array_push($params, ['key' => $key, 'value' => $value, 'type' => \PDO::PARAM_INT]);
            }
            array_push($params, ['key' => $key, 'value' => $value, 'type' => \PDO::PARAM_STR_CHAR]);
        }
        $set = implode(',', $setArr);
        $query = "UPDATE `book` SET  $set  WHERE `id`=:id";
        array_push($params, ['key' => 'id', 'value' => $id, 'type' => \PDO::PARAM_INT]);
        $this->log->info('Query - ' . $query);
        $result = $this->db->executeUpdate($query, $params);
        return ['data' => $book, 'message' => ['changed book' => $result['rowCount']], 'status' => 200];
    }

    function deleteById($id)
    {
        $query = "DELETE FROM `book` WHERE id=:id";
        $params = [['key' => 'id', 'value' => $id, 'type' => \PDO::PARAM_INT]];
        $result = $this->db->executeDelete($query, $params);
        return ['data' => ['id' => $id], 'message' => $result['rowCount'] . ' book is deleted', 'status' => 200];
    }
    function getAllBooks()
    {
        $query = "SELECT * FROM book";
        $data = $this->db->executeSelect($query, 'fetchAll');
        return ['data' => $data, 'message' => 'all books', 'status' => 200];
    }
    function getBooksById($id)
    {
        $query = "SELECT * FROM book WHERE id=" . $id;
        $data = $this->db->executeSelect($query, 'fetch');
        return ['data' => $data, 'message' => 'book', 'status' => 200];
    }
    function getCountBooks()
    {
        $query = "SELECT COUNT(*) FROM book";
        $data = $this->db->executeSelect($query, 'fetchColumn');
        return ['data' => $data, 'message' => 'count of books', 'status' => 200];
    }
}
