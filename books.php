<?php
class Books
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
            self::$instance = new Books;
        }
        return self::$instance;
    }
    public function auth($login, $password)
    {
        $stmt = $this->link->prepare("SELECT id FROM users WHERE 
        BINARY login = :login AND BINARY password = :password");
        $stmt->execute([':login' => $login, ':password' => $password]);
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function add($data)
    {
        $i = 0;
        $id = [];
        $arrayTable = ['name' => 0, 'genre' => 0, 'pages' => 0];
        if ((count($data, COUNT_RECURSIVE) - count($data)) > 0) {
            foreach ($data as $key => $array1) {
                var_dump(implode("','", array_intersect_key($array1, $arrayTable)));
                $stmt = $this->link->prepare("INSERT INTO `book`(`name`, `genre`, `pages`) VALUES
                (" . "'" . implode("','", array_intersect_key($array1, $arrayTable)) . "'" . ")");
                $stmt->execute();
                $i = $stmt->rowCount() + $i;
                $id[$i]['id'] = $this->link->lastInsertId();
            }
            return ['data' => $id, 'message' => $i . ' added books', 'status' => 200];
        }
        $stmt = $this->link->prepare("INSERT INTO `book`(`name`, `genre`, `pages`) VALUES
        (" . "'" . implode("','", array_intersect_key($data, $arrayTable)) . "'" . ")");
        $stmt->execute();
        $id =  $this->link->lastInsertId();
        $i = $stmt->rowCount();
        return ['data' => 'id=' . $id, 'message' => $i . ' added book ', 'status' => 200];
    }
    public function update($data, $id = null)
    {
        if ($id == null) {
            if ((count($data, COUNT_RECURSIVE) - count($data)) > 0) {
                $arrayTable = ['id' => 0, 'name' => 0, 'genre' => 0, 'pages' => 0];
                $i = 0;
                $changes = [];
                foreach ($data as $key => $array1) {
                    $array1 = (array_intersect_key($array1, $arrayTable));
                    $id = array_shift($array1);
                    $set = '';
                    foreach ($array1 as $key => $value) {
                        $set = $set . $key . '=' . "'" . $value . "'" . ",";
                    }
                    $set = substr($set, 0, -1);
                    $stmt = $this->link->prepare("UPDATE `book` SET $set WHERE id=$id");
                    $b = $stmt->execute();
                    var_dump($b);
                    $i = $stmt->rowCount() + $i;
                    if ($stmt->rowCount() > 0) {
                        $changes[] = $set;
                    }
                }
                return ['data' => $changes, 'message' => $i . ' changed books', 'status' => 200];
            }
            exit('accept only array of 2 or more books');
        }
        if ((count($data, COUNT_RECURSIVE) - count($data)) == 0) {
            $arrayTable = ['name' => 0, 'genre' => 0, 'pages' => 0]; // задаем для сравнения массив
            $i = 0;
            $changes = [];
            $array1 = (array_intersect_key($data, $arrayTable)); //сверили массив который пришел с нашим 'для сравнения'
            $set = '';
            foreach ($array1 as $key => $value) {                        //сформировали строку с массива для подстановки в sql запрос на место SET
                $set = $set . $key . '=' . "'" . $value . "'" . ",";
            }
            $set = substr($set, 0, -1); // убрали запятую в конце строки для подстановки SET в sql запрос, которая появилась после последней итерации цикла foreach
            $stmt = $this->link->prepare("UPDATE `book` SET $set 
                WHERE id=" . $id);
            $b = $stmt->execute();
            var_dump($b);
            var_dump($set);
            $i = $stmt->rowCount() + $i;
            if ($i > 0) {
                $changes[] = $set;
            }
            return ['data' => $changes, 'message' => $i . ' changed books', 'status' => 200];
        }
    }
    public function del($id)
    {
        $stmt = $this->link->prepare("DELETE FROM `book` WHERE id=" . $id);
        $b = $stmt->execute();
        var_dump($b);
        $i = 0;
        $i = $stmt->rowCount() + $i;
        return ['data' => 'id=' . $id, 'message' => $i . ' book is deleted', 'status' => 200];;
    }
    public function allBooks()
    {
        $query = "SELECT * FROM book";
        $resQuery = $this->link->query($query);
        $data = $resQuery->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $data, 'message' => 'all books', 'status' => 200];
    }
    public function booksId($id)
    {
        $query = "SELECT * FROM book WHERE id = $id";
        $resQuery = $this->link->query($query);
        $data = $resQuery->fetch(PDO::FETCH_ASSOC);
        return ['data' => $data, 'message' => 'book', 'status' => 200];
    }
    public function count()
    {
        $query = "SELECT COUNT(*) FROM book";
        $resQuery = $this->link->query($query);
        return ['data' => $resQuery->fetchColumn(), 'message' => 'count of books', 'status' => 200];
    }
    public function response($input)
    {
        $resp = ['data' => $input['data'], 'message' => $input['message'], 'status' => $input['status']];
        echo json_encode($resp);
        exit;
    }
}
