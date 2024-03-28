<?php
require_once "setup.php";
require_once "utils.php";
require_once "objects/DBConnection.php";

// класс для работы с пользователями
class User 
{
    private $conn; // объект класса PDO 
    //
    /* Эти свойства не используются
    private $id;
    private $login;
    private $password;
    */
    
    public function __construct() 
    {
        // создание подключения
        $dbc = new DBConnection();
        $this->conn = $dbc->getConnection();        
    }
    //--------------------------------------------------------------------------
    // получить данные пользователя с заданным логином
    public function getUserByLogin($login)
    {
        if (!$this->conn)
            return false;
        
        $query = "SELECT id, login, password " .
                 "FROM users " .
                 "WHERE login = ?";

        // подготовка запроса
        $stmt = $this->conn->prepare($query);

        // очистка
        $l= sanitizeString($login);

        // привязываем значение
        $stmt->bindParam(1, $l);

        // выполняем запрос
        $stmt->execute();

        // получаем извлеченную строку
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // установим значения свойств объекта
        //$this->id = $row["id"];
        //$this->login = $row["login"];
        //$this->password = $row["password"];
        return $row;
    }
    //--------------------------------------------------------------------------
}