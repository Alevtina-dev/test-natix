<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader;
use \Bitrix\Main\Data\Cache;

//$d = date('Y');
//$m = date('m');
//$year = (isset($_GET['year'])&&!empty($_GET['year']))?$_GET['year']:$d ; //вывод новостей только за текущий год
$year = (isset($_GET['year'])&&!empty($_GET['year']))?$_GET['year']:"";
$month = $_GET['month'];
$cirMon = Array( // Кирилизируем месяцы
'01'=>'Январь',
'02'=>'Февраль',
'03'=>'Март',
'04'=>'Апрель',
'05'=>'Май',
'06'=>'Июнь',
'07'=>'Июль',
'08'=>'Август',
'09'=>'Сентябрь',
'10'=>'Октябрь',
'11'=>'Ноябрь',
'12'=>'Декабрь');


$cache = Cache::createInstance();
if ($cache->initCache(7200, "list_date_active")) { // проверяем кеш и задаём настройки
    $arrYear = $cache->getVars(); // достаем переменные из кеша
} elseif ($cache->startDataCache()) {
    if (Loader::includeModule("iblock")) {
        $arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y", 'ACTIVE_DATE'=>'Y');
        $arrNews = \CIBlockElement::GetList(array('ACTIVE_FROM'=>'DESC'), $arFilter, false, false, ["DATE_ACTIVE_FROM"]);
        while ($itemNews = $arrNews->GetNext())
        {
            // записываем в маcсив года и месяцы в формате "год-месяц"
            $arrYear[] = substr($itemNews['DATE_ACTIVE_FROM'],6,4).'-'.substr($itemNews['DATE_ACTIVE_FROM'],3,2);
        }
    }
    $cache->endDataCache($arrYear); // записываем в кеш
}

$resultYear = array_keys(array_count_values($arrYear));

$cc = '';
$mm = '';
for($i = 0; $i < sizeof($resultYear); $i++) {
    $cYear = substr($resultYear[$i], 0, 4); // Выделяем год
    if ($cYear) {
        if ($cYear != $cc) {
            $act_year = ($cYear == $year) ? true : false;
            $Year_mas[] = array('NAME' => substr($resultYear[$i], 0, 4), 'ACTIVE' => $act_year);
        }
        if ($cYear == $year) {
            $cMon = substr($resultYear[$i], 5, 2); // Выделяем месяц
            if ($mm != $cMon) {
                $act = ($cMon == $month) ? true : false;
                $arrMonth[] = array('VALUE' => $cMon, 'NAME' => $cirMon[$cMon], 'ACTIVE' => $act);
                $mm = $cMon;
            }
        }
        $cc = $cYear;
    }
}
$arResult["YEARS"] = $Year_mas; // массив годов новотей (без повторений)
$arResult["MONTH"] = $arrMonth; // массив месяцев по определенному году
//$arResult["CURRENT_YEAR"] = $year;