<?php

class CBOrderControllerBehavior extends CBehavior
{
    //Имя поля таблицы по которому групируем
    public $groupName;


    //Значение, по которому группируем
    public $groupId;


    public function orderUp($id)
    {
        $model = $this->getOwner()->loadModel($id);

        $firstLine = clone ($model);

        //Если группировка не задана, то меняем порядок относительно
        //всей таблицы.


        $criteria = new CDbCriteria;
        if ($this->groupId === null || $this->groupName === null)
            $criteria->condition = '`order`<=' . $firstLine->order . ' AND id<>`' . $id . '`';
        else
            $criteria->condition = '`order`<=' . $firstLine->order . ' AND `' . ($this->groupName) . '`=' . $this->groupId . ' AND id<>' . $id . '';


        // $criteria->select = 'id, MAX(`order`) as `order`';
        $criteria->order = '`order` DESC';
        $criteria->limit = '1';


        $secondLines = $model->findAll($criteria);

        if ($secondLines[0]->id == null) {
            return;
        } else {

            $secondModel = $this->getOwner()->loadModel($secondLines[0]->id);
            if ($firstLine->order == $secondModel->order) {

                $this->fixOrder($id);
                $this->orderUp($id);
                return;
            }
            $topOrder = $secondModel->order;

            $secondModel->order = $firstLine->order;
            if ($secondModel->save()) {
                echo 1;
            }

            $firstLine->order = $topOrder;
            $firstLine->save();
        }

    }

    public function orderDown($id)
    {
        $model = $this->getOwner()->loadModel($id);

        $firstLine = clone ($model);

        //Если группировка не задана, то меняем порядок относительно
        //всей таблицы.

        //по всей таблице
        $criteria = new CDbCriteria;
        if ($this->groupId === null || $this->groupName === null)
            $criteria->condition = '`order`>=' . $firstLine->order . ' AND id<>`' . $id . '`';
        else
            $criteria->condition = '`order`>=' . $firstLine->order . ' AND `' . ($this->groupName) . '`=' . $this->groupId . ' AND id<>' . $id . '';

        // $criteria->select = 'id, MAX(`order`) as `order`';
        $criteria->order = '`order`';
        $criteria->limit = '1';


        $secondLines = $model->findAll($criteria);


        if ($secondLines[0]->id == null) {
            return;
        } else {


            $secondModel = $this->getOwner()->loadModel($secondLines[0]->id);
            if ($firstLine->order == $secondModel->order) {

                $this->fixOrder($id);
                $this->orderDown($id);
                return;
            }
            $bottomOrder = $secondModel->order;

            $secondModel->order = $firstLine->order;
            $secondModel->save();

            $firstLine->order = $bottomOrder;
            $firstLine->save();
        }


    }

    public function fixOrder($id)
    {
        echo 12311;
        $model = $this->getOwner()->loadModel($id);
        $criteria = new CDbCriteria;
        $criteria->condition = ' `' . ($this->groupName) . '`=' . $this->groupId . '';
        $all = $model->findAll($criteria);
        $i = 0;
        foreach ($all as $each) {

            $each->order = $i;
            $each->save();
            $i++;
        }
    }
}

?>
