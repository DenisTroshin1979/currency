<?php
// получить курсы валют 
function getCurrencies()
{
    $urlCurrency1="http://www.cbr.ru/scripts/XML_daily.asp";       
    $myXMLFile=__DIR__ . DIRECTORY_SEPARATOR . 'cbr.xml';

    // если файл с курсами валют не существует, то обратиться к URL источнику курсов
    if (!file_exists($myXMLFile))
    {
        return getCurrenciesByURL($urlCurrency1, $myXMLFile);
    }
    else { // если файл существует, то получить данные о курсах из него
        $data=simplexml_load_file($myXMLFile);
        if ($data!==false) {
            $last_request_date=sprintf("%s", $data->attributes()['Date']); //например, 27.03.2024
            $now=date("d.m.Y");
            // если текущая дата больше даты из файла с курсами валют, то
            // обновить курсы валют по URL
            if (strtotime($now) > strtotime($last_request_date))
                return getCurrenciesByURL($urlCurrency1, $myXMLFile);
            
            return $data;
        }        
        else {
            return getCurrenciesByURL($urlCurrency1, $myXMLFile);
        }    

    }
}
//------------------------------------------------------------------------------
// получить курсы валют по URL и сохранить в файле
function getCurrenciesByURL($urlCurrency1, $myXMLFile) 
{
    $data=file_get_contents($urlCurrency1);
    if ($data===false) 
        return false;
    else {
        // сохранить свежие данные о курсах в файл
        if (file_put_contents($myXMLFile, $data)!==false)
            return simplexml_load_file($myXMLFile);
        else {
            return false;
        }
    }
}
//------------------------------------------------------------------------------
// получить информацию о валюте по ID
function getCurrencyByID($currencies, $ValuteID) : ?SimpleXMLElement      
{
    foreach ($currencies as $currency)
    {
        if ($currency->attributes()['ID']==$ValuteID)
            return $currency;
    }
    return null;
}
//------------------------------------------------------------------------------
// конвертировать валюту
function convertCurrency($amount, $srcCurrencyCost, $dstCurrencyCost)
{
    return ($srcCurrencyCost / $dstCurrencyCost) * $amount;
}
//------------------------------------------------------------------------------
