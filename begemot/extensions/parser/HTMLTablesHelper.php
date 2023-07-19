<?php
$protectedDir = Yii::getPathOfAlias ('webroot');
$file = "/protected/modules/begemot/extensions/parser/phpQuery-onefile.php";
require_once ($protectedDir.$file);

/**
 *
 * Из одной таблицы делает несколько. Создает несколько таблиц. Количество таблиц равно
 * количеству столцов минус один. Первый столбец приставляется к каждому столбцу и генерируется
 * таблица.
 *
 * Допустим есть таблица модификаций вездеходов. Слева первый столбец характеристик типа модификация,
 * мощность, габариты и т.д.
 *
 * Тогда первый столбец это набор этих характеристик для одной модификации, потом следующий ко
 * второй модификации и т.д.
 *
 * Эта функция делает из таблицы нескольких модификаций - несколько отдельных таблиц модификций.
 *
 * @param $htmlTable HTML код разбираемой таблицы
 * @return array Массив HHTML кода. Каждый элемент - таблица.
 */
function leftTableExtract($htmlTable)
{

    $doc = phpQuery::newDocumentHTML($htmlTable);
    phpQuery::selectDocument($doc);
    $lines = pq('tr')->eq(0)->find('th,td');


    $tableColCounts = count($lines);

    $tableColsHTMLArray = [];

    for ($colI = 0; $colI < $tableColCounts; $colI++) {
        $tableColsHTMLArray[$colI] = tableCut($htmlTable, $colI);

    }

    $resultsHTMLTables = [];

    if (count($tableColsHTMLArray) > 0) {
        $tableRow1 = $tableColsHTMLArray[0];
        for ($colI = 1; $colI < $tableColCounts; $colI++) {
            $tableRow2 = $tableColsHTMLArray[$colI];
            $resultsHTMLTables[] = concatTableRow($tableRow1, $tableRow2);
        }
    }
    return $resultsHTMLTables;
}

function tableCut($htmlTable, $cautedRow)
{
    phpQuery::newDocumentHTML($htmlTable);
    $tdOfRowArray = pq('tr');
    $resultRow = '<table>';
    foreach ($tdOfRowArray as $td) {

        $resultTd = pq($td)->find('th:eq(' . $cautedRow . '),' . 'td:eq(' . $cautedRow . ')');

        $resultRow .= '<tr>' . $resultTd . '</tr>';
    }
    $resultRow .= '</table>';
    return $resultRow;
}

function concatTableRow($tableRow1, $tableRow2)
{


    phpQuery::newDocumentHTML($tableRow1);

    $rowOneTDArray = [];
    $tdOfRowArray = pq('tr');

    foreach ($tdOfRowArray as $td) {
        $rowOneTDArray[] = pq($td)->find('th,td');
    }

    phpQuery::newDocumentHTML($tableRow2);

    $rowTwoTDArray = [];
    $tdOfRowArray = pq('tr');

    foreach ($tdOfRowArray as $td) {
        $rowTwoTDArray[] = pq($td)->find('th,td');
    }
//    print_r($rowTwoTDArray);
    $resultTable = '<table>';
    foreach ($rowOneTDArray as $key => $tdOne) {
        $tdTwo = $rowTwoTDArray[$key];
        $resultTable .= '<tr>';

        $resultTable .= '<td>' . $tdOne->html() . '</td><td>' . $tdTwo->html() . '</td>';

        $resultTable .= '</tr>';
    }
    $resultTable .= '</table>';

    return $resultTable;
}

/**
 * Соединяет таблицы из двух колонок по прнципу:
 * 1) Левый столбец у всех таблиц сливается в один левый столбец в итоговой таблице.
 * 2) Каждый столбец результирующей таблицы - правый столбец входящей.
 *
 * Функция удобна для генерации таблиц сравнения товара, или объединения таблиц комплектаций-опций
 *
 * @param $tablesArray Массив html-кода таблиц, которые надо слить.
 * @return string результирующая таблица - html-код
 */
function leftJoinForHTMLTablesArray($tablesArray)
{

    $tablesCount = count($tablesArray);

    $resultTableStructire = [];

    foreach ($tablesArray as $tableKey => $tableHTML) {
        phpQuery::newDocumentHTML($tableHTML);

        $rowArray = $tdOfRowArray = pq('tr');

        foreach ($rowArray as $td) {


            $rowTDArrayPQ = pq($td)->find('th,td');
            $rowTDArray = [];

            foreach ($rowTDArrayPQ as $key => $rowTD) {
                if ($key==0){
                    $rowKey = pq($rowTD)->text();
                }else {
                    $resultTableStructire[$rowKey][] = pq($rowTD)->text();
                }
            }

            //$rowOneTDArray[] = ;
        }

    }
//    print_r($resultTableStructire);

    $resultTableHTML = '<table>';
    foreach ($resultTableStructire as $leftCol => $rowArray ){
        $resultTableHTML .= '<tr>';
        $resultTableHTML .= '<td>'.$leftCol.'</td>';
        $tablesCount;
        for ($i=0;$i<$tablesCount;$i++){
            if (isset($rowArray[$i])) {
                $resultTableHTML = $resultTableHTML.'<td>' . $rowArray[$i] . '</td>';
            } else {
                $resultTableHTML = $resultTableHTML.'<td></td>';
            }
        }

        $resultTableHTML .= '</tr>';
    }

    $resultTableHTML .= '</table>';

    return  $resultTableHTML;
}

function appendTRToTableEnd($rowHTML,$tableHTML){
    $doc = phpQuery::newDocumentHTML($tableHTML);
    $table = $doc->find('table')->append($rowHTML);
    $tableHTML = $table;
    return $tableHTML;
}