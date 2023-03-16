<?php

/**
 *  Класс для преобразования массива набора данных в группы по определенным параметрам.
 *
 * The followings are the available columns in table 'SchmGroup':
 * @property integer $id
 * @property string $groupName
 * @property integer $countOfParams
 * @property integer $assignedId
 */
class SchmGroup extends CActiveRecord
{


    public function relations()
    {
        return array(
            'params' => array(self::HAS_MANY, 'SchmGroupParams', 'groupId'),
        );
    }


    /**
     * @param $fieldsIdArray
     *
     *         $fieldsIdArray = [
     * '636' => 'Модель',
     * '658' => 'Кузов',
     * '5' => 'год',
     * ];
     *
     * @param array $complectIds groupId которые разбиваем по этим параметрам
     */
    public static function groupClusterize($fieldsIdArray, $complectIds = [])
    {

        Yii::import('cache.models.Cache');
        $cache = new Cache();

        $fieldsCaheKey = '';
        if (is_array($fieldsIdArray) && count($fieldsIdArray)) {
            $fieldsCaheKey = implode('_', $fieldsIdArray);
        }
        $complectIdsCaheKeys = '';
        if (is_array($complectIds) && count($complectIds)) {
            $complectIdsCaheKeys = implode('_', $complectIds);
        }

        if (!($result = $cache->getValue('SchmGroup.groupClusterize', $fieldsCaheKey . '_' . $complectIdsCaheKeys . '_'))) {
            $dataBySchemaField = [];

            $wheresql = '';
            if (count($complectIds) > 0) {
                $wheresql = ' and groupId in(' . implode(',', $complectIds) . ')';
            }

            foreach ($fieldsIdArray as $schemaId => $fieldName) {
                $tmpArray = Yii::app()->db->createCommand()->select('*')->
                from('SchemaData')->
                where('`fieldId`=' . $schemaId . $wheresql)->
                leftJoin('SchmTypeString tb1', 'SchemaData.id=tb1.fieldDataId')
                    ->queryAll();

                $dataBySchemaField[$schemaId] = array_combine(array_column($tmpArray, 'groupId'), array_column($tmpArray, 'value'));
                $dataBySchemaField[$schemaId] = self::arrayClusterize($dataBySchemaField[$schemaId]);
            }

            $groups = [];


            //пересекаем начальные группы другими группами
            foreach ($dataBySchemaField as $fieldId => $groupData) {


                if (count($groups) == 0) {

                    $baseData = $dataBySchemaField[$fieldId];
                    foreach ($baseData as $value => $ids) {
                        $groups[] = [
                            'params' => [
                                $fieldId => $value
                            ],
                            'ids' => $ids
                            ,
                        ];
                    }

                    continue;
                }


                $newGroups = [];
                foreach ($groups as $group) {
                    $newGroups = array_merge(self::groupWithArrayIntersect($fieldId, $groupData, $group), $newGroups);
                }
                $groups = $newGroups;

            }
            $cache->setValue('SchmGroup.groupClusterize', $fieldsCaheKey . '_' . $complectIdsCaheKeys . '_', serialize($groups));

        } else {

            $groups = unserialize($result);

        }


        return $groups;
    }

    public static function groupsAssign($groups)
    {

        Yii::import('schema.models.SchmGroupParams');

        $schmGroupList = [];
        foreach ($groups as $group) {
            $schmGroupList[] = self::assignGroup($group);
        }
        return $schmGroupList;

    }

    public function getAssinedCatalogCategory($parentCategoryId = -1)
    {

        if ($this->assignedId == 0) {

            Yii::import('catalog.models.CatCategory');
            $catCategory = new CatCategory();
            $catCategory->pid = $parentCategoryId;
            $catCategory->name = $this->groupName;
            if ($catCategory->save()) {
                $this->assignedId = $catCategory->id;
                $this->save();
            }

        } else {
            $catCategory = CatCategory::model()->findByPk($this->assignedId);
        }
        return $catCategory;
    }

    public static function assignGroup($group)
    {
        Yii::import('schema.models.SchmGroupParams');
        //ищем по параметрам группу или создаем новую
        $params = $group['params'];

        $paramLists = [];

        $arrayForIntersect = [];
        foreach ($params as $paramId => $paramValue) {
            $paramLists[$paramId] = SchmGroupParams::model()->findAllByAttributes(['valueMd5' => md5($paramValue), 'fieldId' => $paramId]);
            if (count($paramLists[$paramId]) == 0) {
                //если хотя бы по одному параметру не нашли, значит точно группы такой нет
                return self::createSchmGroup($group);
            }

//            формируем массивы для дальнейшей работы
            $paramArray = [];
            foreach ($paramLists[$paramId] as $paramModel) {
                $paramArray[] = $paramModel->groupId;
            }
            $arrayForIntersect[$paramId] = $paramArray;
        }

        //пересекаем массивы. В итоге должны получить либо id группы либо пустой массив и это значит нужно создавать группу
        $i = 0;
        $resultIntersect = [];
        foreach ($arrayForIntersect as $tmpArr) {
            if ($i == 0) {
                $resultIntersect = $tmpArr;
                $i++;
                continue;
            }

            $resultIntersect = array_intersect($resultIntersect, $tmpArr);

            $i++;
        }

        if (count($resultIntersect) == 0) {
            return self::createSchmGroup($group);
        } else {
            if (count($resultIntersect) > 1) {
                throw new Exception('Почему-то два результата! Проверять! Не должно быть больше одного.');
            } else {
                return SchmGroup::model()->findByPk(array_shift($resultIntersect));
            }
        }


    }

    private static function createSchmGroup($group)
    {

        $schemaGroup = new SchmGroup();
        $schemaGroup->countOfParams = count($group['params']);

        $params = $group['params'];

        $groupName = '';

        foreach ($params as $paramId => $paramValue) {
            $groupName = $groupName . ' ' . $paramValue;
        }

        $schemaGroup->groupName = $groupName;
        if ($schemaGroup->save()) {
            //группу создали теперь создаем параметры к ней
            foreach ($params as $paramId => $paramValue) {

                $schmGroupParam = new SchmGroupParams();
                $schmGroupParam->groupId = $schemaGroup->id;
                $schmGroupParam->fieldId = $paramId;
                $schmGroupParam->valueMd5 = md5($paramValue);
                $schmGroupParam->value = $paramValue;
                $schmGroupParam->save();

            }
        }


        return $schemaGroup;
    }


    private static function groupWithArrayIntersect($fieldId, $dataForIntersectData, $group)
    {
        $resultGroups = [];

        $groupIds = $group['ids'];


        foreach ($dataForIntersectData as $value => $forIntersectIdList) {


            $groupParams = $group['params'];
            $resultIds = array_intersect($groupIds, $forIntersectIdList);

            if (count($resultIds) > 0) {
                $groupParams[$fieldId] = $value;

                $resultGroups[] = [
                    'params' => $groupParams,
                    'ids' => $resultIds
                ];
            }


        }

        return $resultGroups;
    }

    private static function arrayClusterize($array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (!isset($result[$value]) || !is_array($result[$value])) {
                $result[$value] = [];
            }
            $result[$value][] = $key;
        }

        return $result;
    }

    public function getGroupIds()
    {
        Yii::import('schema.models.*');
        Yii::import('schema.components.*');
        $params = $this->params;
        $complectIds = [];
        foreach ($params as $param) {
            //$SchemaField = SchemaField::model()->findByPk($param->fieldId);
            $complectIds = SchemaLists::equalList($param->field->name, $param->value, 'catItem', $complectIds);
        }
        return $complectIds;
    }

    /**
     * @param $linkedId id связанной сущности,
     * @param $linkType тип связи или ее идентификатор
     * @param $fieldIds массив полей схемы, которые нужно вытащить для группы
     * @return array возвращает сгруппированные данные по groupId
     */
    public function getGroupData($linkedId, $linkType, $fieldIds=[],$limit = 0)
    {
        $schemaGroup = SchmGroup::model()->findByAttributes(['assignedId' => $linkedId]);
        // Define the search criteria

        $groupIds = $schemaGroup->getGroupIds(); // array of group ids
        $groupIds = "'" . implode("','", $groupIds) . "'";



        $fieldIdsSqlPart = '';

        if(is_array($fieldIds) && count($fieldIds)>0){
            $fieldIds = implode(',', $fieldIds);
            $fieldIdsSqlPart = "AND `sd`.`fieldId` IN (:fieldIds)";

        }
        $limitSqlString = '';
        if($limit!=0){
            $limitSqlString = ' limit 1';
        }

        $sql = "SELECT `sd`.*, `sts`.`value`
        FROM `SchemaData` sd
        JOIN `SchmTypeString` sts ON sd.id = sts.`fieldDataId`
        WHERE sd.linkType = \":linkType\"
          AND `sd`.`groupId` IN (:groupIds)
          ".$fieldIdsSqlPart.$limitSqlString;

        $command = Yii::app()->db->createCommand($sql);
//            $command->bindParam();
//            $command->bindParam();
//            $command->bindParam();
        $params = [
            ':linkType' => $linkType,
            ':groupIds' => $groupIds,
            ':fieldIds' => $fieldIds

        ];


        $sql = $command->getText();

        foreach ($params as $name => $value) {
            $sql = str_replace($name, $value, $sql);
        }


        $command = Yii::app()->db->createCommand($sql);
        //$command = Yii::app()->db->createCommand($sql);
        $results = $command->queryAll();

        $groupedArray = [];

        foreach ($results as $element) {
            $groupId = $element['groupId'];
            if (!array_key_exists($groupId, $groupedArray)) {
                $groupedArray[$groupId]['groupId'] = $groupId;
            }
            $groupedArray[$groupId][$element['fieldId']] = $element['value'];
        }

        return $groupedArray;
    }


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'SchmGroup';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('countOfParams', 'numerical', 'integerOnly' => true),
            array('groupName', 'length', 'max' => 100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, groupName, countOfParams', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'groupName' => 'Group Name',
            'countOfParams' => 'Count Of Params',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SchmGroup the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
