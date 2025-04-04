<?php class MngSchemaFieldModel extends CModel
{
    // Атрибуты модели
    public $_id;
    public $name;
    public $schemaId;
    public $type;
    public $order;

    // Коллекция MongoDB
    private $_collection;

    public function __construct($scenario = 'insert')
    {
        // Устанавливаем сценарий
        $this->setScenario($scenario);

        // Подключение к коллекции schemaField
        $this->_collection = Yii::app()->mongoDb->getCollection('schemaField');
    }

    // Правила валидации
    public function rules()
    {
        return array(
            array('name, schemaId, type', 'required'),
            array('name', 'length', 'max' => 255),
            array('schemaId', 'length', 'max' => 50),
            // array('type', 'in', 'range' => array('Text', 'Number', 'Date', 'Boolean')), // Пример типов
            array('order', 'numerical', 'integerOnly' => true, 'allowEmpty' => true),
        );
    }

    // Метки для атрибутов
    public function attributeLabels()
    {
        return array(
            '_id' => 'ID',
            'name' => 'Name',
            'schemaId' => 'Schema ID',
            'type' => 'Type',
            'order' => 'Order',
        );
    }

    // Возвращает список атрибутов
    public function attributeNames()
    {
        return array('_id', 'name', 'schemaId', 'type', 'order');
    }

    // Генерация нового ID перед сохранением
    public function generateId()
    {
        //  $this->_id = new MongoId(); // Генерация уникального ID
    }

    // Сохранение модели в MongoDB
    public function save()
    {
        if ($this->validate()) {
            if ($this->getScenario() === 'insert') {
                $counters = new MongoCounters();
                $this->_id = $counters->getNextValue('schemaFieldId'); // Генерация ID перед созданием новой записи
            }

            $data = array(
                '_id' => $this->_id,
                'name' => $this->name,
                'schemaId' => $this->schemaId,
                'type' => $this->type,
                'order' => $this->order,
            );

            if ($this->getScenario() === 'insert') {
                $this->_collection->insertOne($data);
            } else {
                $this->_collection->update(array('_id' => $this->_id), $data);
            }

            return true;
        }

        return false;
    }

    // Поиск записи по ID
    public static function findById($id)
    {
        $collection = Yii::app()->mongoDb->getCollection('schemaField');
        $data = $collection->findOne(array('_id' => (int)$id));

        if ($data) {
            $model = new self('update');
            $model->setAttributes($data->getArrayCopy(), false);
            return $model;
        }

        return null;
    }
    // Поиск записи по Name
    public static function findByName($name)
    {
        $collection = Yii::app()->mongoDb->getCollection('schemaField');
        $data = $collection->findOne(array('name' => $name));

        if ($data) {
            $model = new self('update');
            $model->setAttributes($data->getArrayCopy(), false);
            return $model;
        }

        return null;
    }
    // Удаление записи
    public function delete()
    {
        return $this->_collection->remove(array('_id' => $this->_id));
    }
}
