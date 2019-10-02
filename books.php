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
    public function auth($login,$password)
    {
        $stmt = $this->link->prepare("SELECT id FROM users WHERE login = :login AND password = :password");
        $stmt->execute([':login' => $login, ':password' => $password]);
        
        if ( $stmt->rowCount() > 0)
        {
            return true;
        }
            return false;
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
