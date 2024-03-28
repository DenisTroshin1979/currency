<?php
require_once "setup.php";
require_once "utils.php";
require_once "objects/user.php";

if ($loggedin)
{
    header("Location: index.php");
    die();
}    

$idCurrentUser=0;
$login="";
$pass="";

$info=""; // вывод сообщений об ошибке, предупреждений и т.д.

if ($_SERVER['REQUEST_METHOD']=='POST')
{
    // проверка полей авторизации
    if (filter_has_var(INPUT_POST, 'btnEnter'))
    {
        if (filter_has_var(INPUT_POST, 'LoginEdit'))
        {
            $login=filter_input(INPUT_POST, 'LoginEdit');
            
            if (validate_login($login)) 
            {
                $login=mb_strtolower($login);

                if (filter_has_var(INPUT_POST, 'PasswordEdit'))
                {
                    $pass=filter_input(INPUT_POST, 'PasswordEdit');
                    
                    if (validate_password($pass)) 
                    {
                        $user=new user;
                        $userinfo=$user->getUserByLogin($login);

                        if ( !is_array($userinfo) || $userinfo['login']!=$login || 
                             !password_verify($pass, $userinfo['password']) )
                        {
                            $info="Логин и/или пароль введены неверно!";
                        }
                        else // успешная авторизация
                        {    
                            $idCurrentUser=$userinfo['id'];
                            $_SESSION['idCurrentUser'] = $idCurrentUser;
                            $_SESSION['loginCurrentUser']=$userinfo['login'];
                            header("Location: index.php");
                            die();
                        }    
                    }
                    else 
                        $info="Логин и/или пароль введены неверно!";
                }

            }
            else 
                $info="Логин и/или пароль введены неверно!";
        }
    }    
}
// вывод страницы (форма авторизации)
echo <<< _START
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="style.css" rel="stylesheet">  
        <title>Конвертация валют</title>
    </head>
    <body>
        <div class="container">
_START;

    // если есть сообщения об ошибках - вывести
    if (!empty($info)) {echo '<div class="errmsg">' . $info . '</div>'; }
echo <<< _FORM
        <form class="LoginForm" name="LoginForm" action="login.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
        <h1 align="left">Конвертация валют</h1><br>     
        <fieldset>
                <legend>Авторизация</legend>
                <p>
                <label>Логин</label><br> 
                <input class="MyEdit" name="LoginEdit" type="text" title="Введите логин" id="idLoginEdit" maxlength="20"  size="50" tabindex="1" required><br> 
                <p>
                <label>Пароль</label> <br> 
                <input class="MyEdit" name="PasswordEdit" type="password" title="Введите пароль" id="idPasswordEdit" maxlength="20" size="50" tabindex="2"><br> 
                <center><i>Для теста программы ввести один из логинов:</i><br> 
                <i>denis, alex, nick, mary и пустой пароль.</i></center>
            </fieldset>
            <br>
            <button name="btnEnter" type="submit" tabindex="3">Войти</button>
        </form>
_FORM;
echo <<< _END
</div><!--Конец container-->
    </body>
</html>
_END;
?>