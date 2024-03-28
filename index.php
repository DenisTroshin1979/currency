<?php
require_once 'setup.php';
require_once 'utils.php';
require_once 'currency.php';

if (!$loggedin)
{
    header("Location: login.php");
    die();
}    
$login=$_SESSION['loginCurrentUser'];

$info=""; // вывод сообщений об ошибке, предупреждений и т.д.

$strInput="";   // данные из поля Сумма формы Конвертация валют
$combo1_itemID=0; // идентификатор валюты 1 из комбобокса формы
$combo2_itemID=0; // идентификатор валюты 2 из комбобокса формы
$strResult="";  // результат конвертации валюты

// получить валюты с сайта и сохранить в файл cbr.xml
$data=getCurrencies();
if ($data===false)
    die("Не удалось получить данные о курсах валют.");

$currencies=$data->Valute;

if ($_SERVER['REQUEST_METHOD']=='POST')
{
    if (filter_has_var(INPUT_POST, 'btnConvert'))
    {
        // валидация текстового поля Сумма
        if (filter_has_var(INPUT_POST, 'editInput'))
        {
           $strInput = filter_input(INPUT_POST, 'editInput', FILTER_VALIDATE_FLOAT, ["options" => ["min_range" => 1]]);
           if (!$strInput) $strInput="";
           if ( (mb_strlen($strInput)<1) || ( mb_strlen($strInput) > 9) )
                { $strInput=""; $info .= "Поле Сумма должно быть целым числом от 1 до 9 символов.<br>";}
        }    
        // валидация списка Валюта 1 
        if (filter_has_var(INPUT_POST, 'comboCurrency1'))
        {
            $combo1_itemID=filter_input(INPUT_POST, 'comboCurrency1');
            if ( (mb_strlen($combo1_itemID)<1) || ( mb_strlen($combo1_itemID) > 10) || preg_match("/[^a-zA-Z0-9]/", $combo1_itemID) )
                { $combo1_itemID="0"; $info .= "Идентификатор валюты 1 должен быть от 1 до 10 символов и включать только цифры и буквы латинского алфавита.<br>";}
            else if ($combo1_itemID=="0")
                $info .= "Выберите валюту 1.<br>";

        }    
        // валидация списка Валюта 2 
        if (filter_has_var(INPUT_POST, 'comboCurrency2'))
        {
           $combo2_itemID=filter_input(INPUT_POST, 'comboCurrency2');
           if ( (mb_strlen($combo2_itemID)<1) || ( mb_strlen($combo2_itemID) > 10) || preg_match("/[^a-zA-Z0-9]/", $combo2_itemID) )
                { $combo2_itemID="0"; $info .= "Идентификатор валюты 2 должен быть от 1 до 10 символов и включать только цифры и буквы латинского алфавита.<br>";}
            else if ($combo2_itemID=="0")
                $info .= "Выберите валюту 2.<br>";
        }    
        // если форма заполнена правильно, то выполнить конвертацию валют 
        if (!empty($strInput) && !empty($combo1_itemID) && !empty($combo2_itemID))
        {
            // по id получить информацию о валюте 1 и валюте 2
            $srcCurrency=getCurrencyByID($currencies, $combo1_itemID);
            $dstCurrency=getCurrencyByID($currencies, $combo2_itemID);
            
            // получить поле со стоимостью валюты 1
            $srcCost=sprintf("%s", $srcCurrency->VunitRate);
            // заменить в стоимости символ  ',' на '.'
            $ind=mb_strpos($srcCost, ",");
            if ($ind!==false)
                $srcCost[$ind]='.';
            
            // получить поле со стоимостью валюты 2
            $dstCost=sprintf("%s", $dstCurrency->VunitRate);
            // заменить в стоимости символ ',' на '.'
            $ind=mb_strpos($dstCost, ",");
            if ($ind!==false)
                $dstCost[$ind]='.';
            // преобразовать валюту
            $strResult=convertCurrency($strInput, $srcCost, $dstCost);
        }
    }
}

// вывод страницы
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
            <p>Вы вошли как <b>{$login}</b>&nbsp;<a href="logout.php">Выйти</a>         
            <h1 align="left">Конвертация валют</h1><br>     
            <p>    
_START;

// если есть сообщения об ошибках - вывести
if (!empty($info)) {echo '<div class="errmsg">'  . $info . '</div><p>';}         

// вывод формы Конвертация валют
showCurrencyForm();

echo <<< _END
        </div><!--Конец container-->
    </body>
</html>
_END;
//------------------------------------------------------------------------------
// вывод формы конвертации валют
function showCurrencyForm()
{
global $strInput;
global $combo1_itemID;
global $combo2_itemID;
global $strResult;
global $currencies;

echo <<< _BLOCK1
<form class="CurrencyForm" name="CurrencyForm" action="index.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8"> 
<fieldset>
<legend>Конвертация валют</legend>
<p>

<label for="comboCurrency1"></label>Валюта 1<br> 
_BLOCK1;
    
updateCurrencyCombo("comboCurrency1", "comboCurrency1", "Выберите валюту 1...", 1, $combo1_itemID, $currencies);

echo <<< _BLOCK2
<p><label class="form-label" for="editInput">Сумма</label><br>
<input class="form-control" type="text" name="editInput" value='$strInput' id="editInput" maxlength="9" size="35" tabindex="2"><br> 
<br><label class="form-label" for="comboCurrency2">Валюта 2</label><br>
_BLOCK2;

updateCurrencyCombo("comboCurrency2", "comboCurrency2", "Выберите валюту 2...", 3, $combo2_itemID, $currencies);

echo <<< _BLOCK3

<p><label class="form-label" for="editResult">Результат</label><br>
<input class="form-control" type="text" name="editResult" value='$strResult' id="editResult" maxlength="20" size="35" tabindex="4"><br> 
<br>
<button class="btn btn-primary form-control" name="btnConvert" type="submit" tabindex="5">Конвертировать</button>&nbsp;
</fieldset>
</form>
_BLOCK3;
}
//------------------------------------------------------------------------------
// вывод combobox cо списком валют
// параметры: name комбобокса, ID комбобокса, название первого элемента комбобокса, 
// индекс текущего элемента комбобокса, данные для записи в комбобокс
function updateCurrencyCombo($strComboName, $comboID, $strFirstItem, $intComboTabIndex, $comboItemID, $currencies)
{
    echo "<select class='form-control' name='$strComboName' id='$comboID' tabindex='$intComboTabIndex'>";                        
    if ($comboItemID==0) echo "<option selected value=\"0\">$strFirstItem</option>";
        else echo "<option value=\"0\">$strFirstItem</option>";
    
    foreach ($currencies as $currency)    
    {
        $currencyID=sprintf("%s", $currency->attributes()['ID']);
        if ($currencyID==$comboItemID)
        echo "<option selected value='{$currencyID}'>{$currency->Name}</option>";
        else    
            echo "<option value='{$currencyID}'>{$currency->Name}</option>";
    }
    echo "</select>";
} 
//------------------------------------------------------------------------------
?>