<?php
// класс для подключения к БД
class DBConnection 
{
    private $host;
    private $db_name;
    private $username;
    private $password;
  
    public $conn;
    private $db_params;
    
    // в конструкторе получение параметров подключения к БД из файла
    function __construct()
    {
        $this->db_params=$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "currency/db1";
        $authdata=file($this->db_params, FILE_IGNORE_NEW_LINES);
        if ($authdata) 
        {
            list($this->host, $this->db_name, $this->username, $this->password)=$authdata;
        }    
        else 
        {
            $this->host="";
            $this->db_name="";
            $this->username="";
            $this->password="";
        }
   }
    
    // получаем соединение с БД
    public function getConnection(){
        
        $this->conn = null;

        try 
        {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } 
        catch(PDOException $exception)
        {
            // echo "Connection error: " . $exception->getMessage();
            echo "Нет доступа к базе данных.";
        }

        return $this->conn;
    }
}
?>