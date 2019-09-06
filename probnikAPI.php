<?php
function books()
{
    $allBooks=['1','2','3','4'];
    echo json_encode($allBooks);
}
function booksID($a)
{
    switch ($a) {
        case 1:
            echo 'Neznayka';
            break;
        case 2:
            echo 'Chipolino';
            break;
        case 3:
            echo 'Tri Mushkitera';
            break;
        default:
            echo 'takoi net';   
            break;
    }

}
function booksCount($a)
{
    if ($a==='true')
    {
       echo 'all books = 42'; 
    }
    else 
    {
    echo 'mistake';
    }
}
if (count($_GET)==1)            /* из-за начального одинакового для всех случаев метода books, 
                             необходимо определить есть ли еще данные для передачи, 
                             которые могут влият на выбор функции */
{
    $_GET['method']();
}
if (count($_GET)==2)
{
    $a=current($_GET); /* для универсальности преобразовую значение и ключи массива $_GET 
                        в строки с целью вызова необходимой функции, которую запросил клиент*/
    next($_GET);    // перевожу курсор на следующий элемент массива
    $b=key($_GET);  // ключ второго элемента массива
    $d=current($_GET);// значение второго элемента массива
    $c=$a.''.$b;// обьединяю $a and $b  в одну строку
    $c($d);// вызываю функцию имя которой $C и передаю аргумент $d
}
?>