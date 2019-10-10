<?php
require_once('DB.php');
function auth($login, $password)
{
    $stmt = DB::getInstance()->link()->prepare("SELECT id FROM users WHERE 
        BINARY login = :login AND BINARY password = :password");
    $stmt->execute([':login' => $login, ':password' => $password]);
    if ($stmt->rowCount() > 0) {
        return true;
    }
    return false;
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
    $stmt = DB::getInstance()->link()->prepare(
        "INSERT INTO `book`(`name`, `genre`, `pages`) 
                        VALUES (:name, :genre, :pages);"
    );
    $stmt->bindParam(":name", $book['name'], PDO::PARAM_STR);
    $stmt->bindParam(":genre", $book['genre'], PDO::PARAM_STR);
    $stmt->bindParam(":pages", $book['pages'], PDO::PARAM_INT);
    $stmt->execute();
    $i = $stmt->rowCount();
    $id = DB::getInstance()->link()->lastInsertId();
    return ['data' => 'id=>' . $id, 'message' => $i . ' added books', 'status' => 200];
}

function update($data, $id)
{
    $arrayTable = array_flip(['name', 'genre', 'pages']);
    $book = array_intersect_key($data, $arrayTable);
    $set = '';
    foreach ($book as $key => $value) {
        $set .= "`$key`=:$key,";
    }
    $set = substr($set, 0, -1);
    $changes = [];
    $stmt = DB::getInstance()->link()->prepare("UPDATE `book` SET  $set  WHERE `id`=:id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    foreach ($book as $key => $value) {
        if ($key === 'pages') {
            $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
    $i = $stmt->rowCount();
    if ($i > 0) {
        $changes = $book;
    }
    return ['data' => $changes, 'message' => $i . ' changed books', 'status' => 200];
}

function del($id)
{
    $stmt = DB::getInstance()->link()->prepare("DELETE FROM `book` WHERE id=" . $id);
    $b = $stmt->execute();
    var_dump($b);
    $i = 0;
    $i = $stmt->rowCount() + $i;
    return ['data' => 'id=' . $id, 'message' => $i . ' book is deleted', 'status' => 200];
}

function allBooks()
{
    $query = "SELECT * FROM book";
    $resQuery = DB::getInstance()->link()->query($query);
    $data = $resQuery->fetchAll(PDO::FETCH_ASSOC);
    return ['data' => $data, 'message' => 'all books', 'status' => 200];
}

function booksId($id)
{
    $query = "SELECT * FROM book WHERE id = $id";
    $resQuery = DB::getInstance()->link()->query($query);
    $data = $resQuery->fetch(PDO::FETCH_ASSOC);
    return ['data' => $data, 'message' => 'book', 'status' => 200];
}
function counts()
{
    $query = "SELECT COUNT(*) FROM book";
    $resQuery = DB::getInstance()->link()->query($query);
    return ['data' => $resQuery->fetchColumn(), 'message' => 'count of books', 'status' => 200];
}


