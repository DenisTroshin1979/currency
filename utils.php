<?php

// обезвреживание строки
function sanitizeString($var)
{
    //$var = stripslashes($var);
    //$var = strip_tags($var);
    $var = htmlentities($var); 
    return $var;
}
//------------------------------------------------------------------------------
// уничтожение сессии
function destroySession()
{
    session_start();
    $_SESSION=array();
    if (session_id() != "" || isset($_COOKIE[session_name()]))
    setcookie(session_name(), '', time()-2592000, '/');
    session_destroy();
}
//------------------------------------------------------------------------------
function validate_login($login) 
{
    if ($login == "") 
        return false;
    else if ( (mb_strlen($login) < 3) || (mb_strlen($login) > 20) )
        return false;
    else if (preg_match("/[^a-zA-Z0-9_-]/", $login))
        return false;
    return true;
}
//------------------------------------------------------------------------------
function validate_password($pass) 
{
    if ($pass == "")  // 
        return true;  // для тестирования разрешить пустой пароль
    if (mb_strlen($pass) > 20)
        return false;
    else if (preg_match("/[^a-zA-Z0-9_-]/", $pass))
        return false;
    return "";
}
//------------------------------------------------------------------------------
?>