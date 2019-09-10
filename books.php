<?php
class books
{	
private static $instance = null;
private function __construct()
{
$this->link = new PDO('mysql: host=localhost;dbname=library_bd', "root","root");
}
private function __clone() {}
public static function getInstance()
{
        if (self::$instance === null)
        {
            self::$instance = new books;
        }
        return self::$instance;
}
public function allBooks()
{
$result='';
$Query = "SELECT * FROM book";
$resQuery = $this->link->query($Query);
while ($row=$resQuery->fetch(PDO::FETCH_ASSOC))
{
$result=json_encode($row).','.$result;
}
echo $result;
}
public function booksID($id)
{
$Query = "SELECT * FROM book WHERE id = $id";
$resQuery = $this->link->query($Query);
echo json_encode($row=$resQuery->fetch(PDO::FETCH_ASSOC));
}
public function Count()
{
$Query = "SELECT id FROM book";
$resQuery = $this->link->query($Query);
while ($row=$resQuery->fetch(PDO::FETCH_ASSOC))
{
    $count=$row['id'];
}
echo $count;
}
}
?>
