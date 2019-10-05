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
        $stmt = $this->link->prepare("SELECT id FROM users WHERE BINARY login = :login AND BINARY password = :password");
        $stmt->execute([':login' => $login, ':password' => $password]);
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function add($data)
    {
        $i = 0;
        $arrayTable = ['name' => 0, 'genre' => 0, 'pages' => 0];
        if ((count($data, COUNT_RECURSIVE) - count($data)) > 0) {
            foreach ($data as $key => $array1) {
                var_dump(implode("','", array_intersect_key($array1, $arrayTable)));
                $stmt = $this->link->prepare("INSERT INTO `book`(`name`, `genre`, `pages`) VALUES (" . "'" . implode("','", array_intersect_key($array1, $arrayTable)) . "'" . ")");
                $stmt->execute();
                $i = $stmt->rowCount() + $i;
            }
            return $i;
        }
        $stmt = $this->link->prepare("INSERT INTO `book`(`name`, `genre`, `pages`) VALUES (" . "'" . implode("','", array_intersect_key($data, $arrayTable)) . "'" . ")");
        $stmt->execute();
        var_dump($stmt->rowCount());
    }
    public function update($data, $url)
    {
        if (preg_match("[^/api/books(|/)$]", $url)) {
            $arrayTable = ['id' => 0, 'name' => 0, 'genre' => 0, 'pages' => 0];
            $i = 0;
            if ((count($data, COUNT_RECURSIVE) - count($data)) > 0) {
                foreach ($data as $key => $array1) {
                    $array1 = (array_intersect_key($array1, $arrayTable));
                    $id = array_shift($array1);
                    $set = '';
                    foreach ($array1 as $key => $value) {
                        $set = $set . $key . '=' . "'" . $value . "'" . ",";
                    }
                    $set = substr($set, 0, -1);
                    $stmt = $this->link->prepare("UPDATE `book` SET $set WHERE id=$id");
                    var_dump($stmt);
                    $b = $stmt->execute();
                    var_dump($b);
                    $i = $stmt->rowCount() + $i;
                }
            }
            return $i;
        }
        if (preg_match("[^/api/books/([0-9]{1,})(|/)$]", $url)) { //пришел урл с id
            $arrayTable = ['name' => 0, 'genre' => 0, 'pages' => 0]; // задаем для сравнения массив
            $i = 0;
            if ((count($data, COUNT_RECURSIVE) - count($data)) == 0) { // проверили что пришел одномерный массив
                $array1 = (array_intersect_key($data, $arrayTable)); //сверили массив который пришел с нашим 'для сравнения'
                $set = '';
                foreach ($array1 as $key => $value) {                        //сформировали строку с массива для подстановки в sql запрос на место SET
                    $set = $set . $key . '=' . "'" . $value . "'" . ",";
                }
                $set = substr($set, 0, -1); // убрали запятую в конце строки для подстановки SET в sql запрос, которая появилась после последней итерации цикла foreach
                preg_match_all('/[0-9]/', $url, $matches); //выделили с урл номер id для sql запроса
                $stmt = $this->link->prepare("UPDATE `book` SET $set WHERE id=" . implode($matches[0]));
                var_dump($stmt);
                $b = $stmt->execute();
                var_dump($b);
                $i = $stmt->rowCount() + $i;
            }
            return $i;
        }
    }
    public function del($url)
    {
        preg_match_all('/[0-9]/', $url, $matches); //выделили с урл номер id для sql запроса
        $stmt = $this->link->prepare("DELETE FROM `book` WHERE id=" . implode($matches[0]));
        $b = $stmt->execute();
        var_dump($b);
        $i=0;
        $i = $stmt->rowCount() + $i;
        return $i;
    }
    public function allBooks()
    {
        $query = "SELECT * FROM book";
        $resQuery = $this->link->query($query);
        echo json_encode($resQuery->fetchAll(PDO::FETCH_ASSOC));
    }
    public function booksId($id)
    {
        $query = "SELECT * FROM book WHERE id = $id";
        $resQuery = $this->link->query($query);
        echo json_encode($resQuery->fetch(PDO::FETCH_ASSOC));
    }
    public function count()
    {
        $query = "SELECT COUNT(*) FROM book";
        $resQuery = $this->link->query($query);
        echo $resQuery->fetchColumn();
    }
}
