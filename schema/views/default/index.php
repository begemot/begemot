<style>
    .subItems {
        /*border-left: 1px solid grey;*/
    }
    .level{
        margin-left: 20px;
    }

    .level-0> .subItems{  border-left: 1px solid red;}
    .level-1 > .subItems{  border-left: 1px solid green;}
    .level-2 > .subItems{  border-left: 1px solid blue;}
    .level-3 > .subItems{  border-left: 1px solid grey;}

</style>
<?php
/* @var $this DefaultController */
$this->menu = require dirname(__FILE__) . '/commonMenu.php';


printListLayer();

function printListLayer($schemaId = null, $level = 0)
{
    $all = Schema::model()->findAllByAttributes(['pid' => $schemaId]);
    foreach ($all as $item) {
        echo '<div class="level level-'.($level).'">';

        echo '<h'.(3+$level).'>'.$item->name.' :'.($item->id).'</h'.(3+$level).'>';
        echo '<div class="subItems">';
        if ($all = getChildSchemas($item->id)) {
            printListLayer($item->id, $level + 1);
        }


        $allFields = SchemaField::model()->findAllByAttributes(['schemaId' => $item->id]);
        if ($allFields) {
            echo '<ul>';
            foreach ($allFields as $allField) {

                echo '<li>'.$allField->name.' :'.$allField->id.'</li>';

            }
            echo '</ul>';
        }
        echo '</div>';
        echo '</div>';
    }

}

function getChildSchemas($id)
{
    return Schema::model()->findAllByAttributes(['pid' => $id]);
}

?>
