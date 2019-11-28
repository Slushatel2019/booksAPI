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
    function add($data)
    {
        $arrayTable = array_flip(['name', 'genre', 'pages']);
        $book = array_intersect_key($data, $arrayTable);
        $dif = array_diff_key($arrayTable, $book);
        if (count($book) != count($arrayTable)) {
            return ['data' => $data, 'message' => 'Needed fields are ' .
                implode(',', array_keys($dif)), 'status' => 500];
        }
        $query = "INSERT INTO `book`(`name`, `genre`, `pages`) 
        VALUES (:name, :genre, :pages)";
        $result = $this->db->executeAdd($query,$book);
        return ['data' => 'id=>' . $result['id'], 'message' => $result['successfulAdd'] . ' added book', 'status' => 200];
    }
    function update($data, $id)
    {
        $arrayTable = array_flip(['name', 'genre', 'pages']);
        $book = array_intersect_key($data, $arrayTable);
        $setArr = [];
        foreach ($book as $key => $value) {
            $setArr[]= "`$key`=:$key";
        }
        $set = implode(',',$setArr);
        $query = "UPDATE `book` SET  $set  WHERE `id`=" .$id;
        $this->log->info('Query - '.$query);
        $successfulUpdate = $this->db->executeUpdate($query,$book);
        return ['data' => $book, 'message' => ['changed book' => $successfulUpdate], 'status' => 200];

    }
   
    function del($id)
    {
        $query = "DELETE FROM `book` WHERE id=" . $id;
        $successfulDel = $this->db->executeCommon($query,'rowCount');
        return ['data' => ['id' => $id], 'message' => $successfulDel . ' book is deleted', 'status' => 200];
    }
    function allBooks()
    {
        $query = "SELECT * FROM book";
        $data = $this->db->executeCommon($query,'fetchAll');
        return ['data' => $data, 'message' => 'all books', 'status' => 200];
    }
    function booksId($id)
    {
        $query = "SELECT * FROM book WHERE id=" . $id;
        $data = $this->db->executeCommon($query,'fetch');
        return ['data' => $data, 'message' => 'book', 'status' => 200];
    }
    function count()
    {
        $query = "SELECT COUNT(*) FROM book";
        $data = $this->db->executeCommon($query,'fetchColumn');
        return ['data' => $data, 'message' => 'count of books', 'status' => 200];
    }

}
