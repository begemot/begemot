<?php

/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 20.12.2018
 * Time: 18:37
 */
class Catalog extends BaseDataType
{
    public $title = 'Каталог - позиция';


    public $tableName = 'catItems';
    public $tableFieldTitle = 'name';

    public $actions = [
        ['id' => 'edit'],
        ['id' => 'create']
    ];

    public function getDataFields()
    {
        Yii::app()->db->schema->refresh();
        $table = Yii::app()->db->schema->getTable('catItems');
        $result = $table->columns;
        unset($result['id']);

        $resultArray = [];

        foreach ($result as $rowKey => $rowMeta) {
            $resultArray[$rowKey]['name'] = $rowMeta->name;
        }

        return $resultArray;
    }

    /**
     * @param $id идентефикатор в CatItem
     * @param $taskId идентификатор задания
     */
    public function import($id, $taskId)
    {

        $addedModel = new ContentTaskAdded();
        $addedModel->type = 'Catalog';
        $addedModel->taskId = $taskId;
        $addedModel->contentId = $id;

        if ($addedModel->save()) {

            Yii::import('catalog.models.CatItem');

            $model = CatItem::model()->findByPk($id);

            $contentTask = ContentTask::model()->findByPk($taskId);
            $data = unserialize($contentTask->dataElementsList);
            echo '<pre>';
            print_r($data);

            foreach ($data as $field) {
                $fieldName = $field['name'];
                $model->$fieldName;

                /*
                 * Создаем базовые данные, которые используются как образец и не меняются
                 * isBaseData определяет это дело
                 */

                $contentTaskData = new ContentTaskData();

                $contentTaskData->groupId = $id;
                $contentTaskData->name = $field['name'];
                $contentTaskData->data = $model->$fieldName;
                $contentTaskData->dataType = 'Catalog';
                $contentTaskData->taskId = $taskId;
                $contentTaskData->subTaskId = $addedModel->id;
                $contentTaskData->isBaseData = 1;
                $contentTaskData->save();

                //А это данные, которые меняет контентер
                $contentTaskData = new ContentTaskData();

                $contentTaskData->groupId = $id;
                $contentTaskData->name = $field['name'];
                $contentTaskData->data = $model->$fieldName;
                $contentTaskData->dataType = 'Catalog';
                $contentTaskData->taskId = $taskId;
                $contentTaskData->subTaskId = $addedModel->id;
                $contentTaskData->isBaseData = 0;
                $contentTaskData->save();
            }
        }

    }

    /**
     * выкатываем на сайт, обратная операция import
     *
     * @param $subtaskId идентификатор подзадания
     * @param $taskId идентификатор задания
     */
    public function export($taskId, $subtaskId)
    {
        /*
         * Определяем крайнюю итерацию.
         */
        $added = ContentTaskAdded::model()->findByAttributes(['taskId' => $taskId, 'id' => $subtaskId]);
        if ($added) {
            Yii::import('catalog.models.CatItem');
            $catItem = CatItem::model()->findByPk($added->contentId);

            if ($catItem) {
                /*
                   * план такой. Определяем крайнюю итерацию.
                   * достаем данные из крайней итерации
                   * сохраняем на сайт перебором
                   */
                $iteration = $added->iteration;

                $contentData = ContentTaskData::model()->findAllByAttributes([
                    'isBaseData' => 0,
                    'iteration' => $iteration,
                    'subTaskId' => $subtaskId
                ]);

                foreach ($contentData as $dataItem) {
                    $fieldName = $dataItem->name;
                    $catItem->$fieldName = $dataItem->data;
                }
                if (!$catItem->save()){
                    echo 'Сохранение модели не удалось!';
                    print_r($catItem->errors);
                }
            } else {
                echo 'Связанное позиции не найдено!';
                echo 'id '.$added->contentId;
            }


        } else {
            echo "не нашли";
            print_r(['taskId' => $subtaskId, 'id' => $taskId]);
        }
    }

    public function create($taskId)
    {
        echo 'Зашли в создание позиции';
        Yii::import('catalog.models.CatItem');
        $catItem = new CatItem();
        $tableFieldTitle = $this->tableFieldTitle;
        $catItem->$tableFieldTitle = "Временный. Элемент создан в contentTask";
        if ($catItem->save()) {


            $addedModel = new ContentTaskAdded();
            $addedModel->type = 'Catalog';
            $addedModel->taskId = $taskId;
            $addedModel->contentId = $catItem->id;
            $addedModel->new = 1;

            if ($addedModel->save()) {
                $id = $catItem->id;

                $model = CatItem::model()->findByPk($id);

                $contentTask = ContentTask::model()->findByPk($taskId);
                $data = unserialize($contentTask->dataElementsList);
                echo '<pre>';
                print_r($data);

                foreach ($data as $field) {
                    $fieldName = $field['name'];
                    $model->$fieldName;

                    /*
                     * Создаем базовые данные, которые используются как образец и не меняются
                     * isBaseData определяет это дело
                     */

                    $contentTaskData = new ContentTaskData();

                    $contentTaskData->groupId = $id;
                    $contentTaskData->name = $field['name'];
                    $contentTaskData->data = $model->$fieldName;
                    $contentTaskData->dataType = 'Catalog';
                    $contentTaskData->taskId = $taskId;
                    $contentTaskData->subTaskId = $addedModel->id;
                    $contentTaskData->isBaseData = 1;
                    $contentTaskData->save();

                    //А это данные, которые меняет контентер
                    $contentTaskData = new ContentTaskData();

                    $contentTaskData->groupId = $id;
                    $contentTaskData->name = $field['name'];
                    $contentTaskData->data = $model->$fieldName;
                    $contentTaskData->dataType = 'Catalog';
                    $contentTaskData->taskId = $taskId;
                    $contentTaskData->subTaskId = $addedModel->id;
                    $contentTaskData->isBaseData = 0;
                    $contentTaskData->save();
                }
            }
        } else {
            echo "ошибка";
            print_r($catItem->errors);
        }
    }


}