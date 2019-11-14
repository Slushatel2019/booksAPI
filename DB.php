<?php
class DB
{
    private static $instance = null;

    private function __construct()
    {
        $this->link = new PDO('mysql: host=localhost;dbname=library_bd', "root", "root");
    }

    private function __clone()
    { }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DB;
        }
        return self::$instance;
    }
    function add($data)
{
    // необходимые ключи для таблицы
    $arrayTable = array_flip(['name', 'genre', 'pages']);
    // оставляем только необходимые ключи
    $book = array_intersect_key($data, $arrayTable);
    // каких ключей не хватает
    $dif = array_diff_key($arrayTable, $book);
    // если ключей меньше, чем нужно - возвращаем ошибку
    if (count($book) != count($arrayTable)) {
        return ['data' => $data, 'message' => 'Needed fields are ' .
            implode(',', array_keys($dif)), 'status' => 500];
    }
    $stmt = $this->link->prepare(
        "INSERT INTO `book`(`name`, `genre`, `pages`) 
                        VALUES (:name, :genre, :pages);"
    );
    $stmt->bindParam(":name", $book['name'], PDO::PARAM_STR);
    $stmt->bindParam(":genre", $book['genre'], PDO::PARAM_STR);
    $stmt->bindParam(":pages", $book['pages'], PDO::PARAM_INT);
    $stmt->execute();
    $successfulAdd = $stmt->rowCount();
    $id = $this->link->lastInsertId();
    return ['data' => 'id=>' . $id, 'message' => $successfulAdd . ' added books', 'status' => 200];
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
    $stmt = $this->link->prepare("UPDATE `book` SET  $set  WHERE `id`=:id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    foreach ($book as $key => $value) {
        if ($key === 'pages') {
            $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    $successfulUpdate = $stmt->rowCount();
    return ['data' => $book, 'message' => $successfulUpdate . ' changed books with id='.$id, 'status' => 200];
}
function del($id)
{
    $stmt = $this->link->prepare("DELETE FROM `book` WHERE id=" . $id);
    $stmt->execute();
    $successfulDel = $stmt->rowCount();
    return ['data' => 'id=' . $id, 'message' => $successfulDel . ' book is deleted', 'status' => 200];
}
function allBooks()
{
    $query = "SELECT * FROM book";
    $resQuery = $this->link->query($query);
    $data = $resQuery->fetchAll(PDO::FETCH_ASSOC);
    return ['data' => $data, 'message' => 'all books', 'status' => 200];
}
function booksId($id)
{
    $query = "SELECT * FROM book WHERE id = $id";
    $resQuery = $this->link->query($query);
    $data = $resQuery->fetch(PDO::FETCH_ASSOC);
    return ['data' => $data, 'message' => 'book', 'status' => 200];
}
function count()
{
    $query = "SELECT COUNT(*) FROM book";
    $resQuery = $this->link->query($query);
    return ['data' => $resQuery->fetchColumn(), 'message' => 'count of books', 'status' => 200];
}
function auth($login, $password)
{
    $stmt = $this->link->prepare("SELECT id FROM users WHERE 
        BINARY login = :login AND BINARY password = :password");
    $stmt->execute([':login' => $login, ':password' => $password]);
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return false;
}
}

