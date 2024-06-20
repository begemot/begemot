<?php

class m1697111034_categoriesRenew extends BaseMigration
{
    public function up()
    {
        $this->localStatusToggle();
        $cats = CatCategory::model()->findAll();
        $catsArray = [];
        foreach ($cats as $cat) {
            $catLine = [];
            $catLine['id'] = $cat->id;
            $catLine['pid'] = $cat->pid;
            $catLine['name'] = $cat->name;
            $catLine['level'] = $cat->level;
            $catLine['order'] = $cat->order;

            $catsArray[] = $catLine;
        }
        usort($catsArray, function ($a, $b) {
            return $a['level'] > $b['level'];
        });
        $currOrder = 0;
        foreach ($catsArray as &$item) {
            if ($item['level'] == 0) {
                $item['order'] = $currOrder++;
            }
        }
        //сортируем по уровню вложенности
        $itemsByLevelArray = [];
        foreach ($catsArray as $catItem) {
            if (!isset($itemsByLevelArray[$catItem['level']]) || !is_array($itemsByLevelArray[$catItem['level']])) $itemsByLevelArray[$catItem['level']] = [];
            $itemsByLevelArray[$catItem['level']][] = $catItem;
        }

        //разбивка элементов по pid
        $itemsByPidArray = [];
        foreach ($catsArray as $catItem) {
            if (!isset($itemsByPidArray[$catItem['pid']]) || !is_array($itemsByPidArray[$catItem['pid']])) $itemsByPidArray[$catItem['pid']] = [];
            $itemsByPidArray[$catItem['pid']][] = $catItem;
        }

        foreach ($itemsByPidArray as $id => $pidArray) {
            $itemsByPidArray[$id] = clearOrder($pidArray);
        }


        $baseArray = $itemsByPidArray[-1];
        unset($itemsByPidArray[-1]);
        $iteration = 0;

        while (count($itemsByPidArray) > 0) {
            $key = $iteration % count($baseArray);
            $idOfElement = $baseArray[$key]['id'];
            $orderCurrent = $baseArray[$key]['order'];
            if (isset($itemsByPidArray[$idOfElement])) {
                insertArrayToArrayByOrder($baseArray, $itemsByPidArray[$idOfElement], $orderCurrent);
                unset($itemsByPidArray[$idOfElement]);
            }

            $iteration++;
        }

        foreach ($baseArray as $baseArrayItem) {
            $cat = CatCategory::model()->findByPK($baseArrayItem['id']);
            $cat->order = $baseArrayItem['order'];
            $cat->status = 1;
            $cat->save();
        }

        return true;
    }

    public function down()
    {
        $this->localStatusToggle();

        return false;
    }

    public function getDescription()
    {
        return "Перевод старого формата хранения категорий каталога в новый.";
    }

    public function isConfirmed($returnBoolean = false)
    {

        return $this->checkLocalStatus(); //$this->tableExist('NewTable');
    }

    /*
     * ALTER TABLE `catItems`
    DROP COLUMN `top`;
     *
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

function insertArrayToArrayByOrder(&$baseArray, $pidArray, $orderFromMustInsert)
{
    $countOfPidArrayElements = count($pidArray);
    foreach ($baseArray as &$element) {
        if ($element['order'] > $orderFromMustInsert) {
            $element['order'] += $countOfPidArrayElements;
        }
    }

    foreach ($pidArray as &$pidArrayElement) {
        $pidArrayElement['order'] += $orderFromMustInsert;
        $baseArray[] = $pidArrayElement;
    }

    usort($baseArray, function ($a, $b) {
        return $a['order'] > $b['order'];
    });
}

function clearOrder($array)
{
    $currOrder = 1;
    foreach ($array as &$item) {
        $item['order'] = $currOrder++;
    }
    return $array;
}