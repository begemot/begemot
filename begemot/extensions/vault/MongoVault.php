<?php

require(__DIR__ . '/Vault.php');

class MongoVault implements Vault
{
    private $collection; // Коллекция MongoDB
    private $directory; // Путь к директории

    /**
     * Конструктор.
     * Принимает только путь к директории и коллекцию MongoDB.
     *
     * @param MongoDB\Collection $collection - коллекция MongoDB
     * @param string $directory - путь к директории
     */

    public function __construct($directory)
    {
        $this->collection = Yii::app()->mongoDb->getCollection('vault');
        $this->collection = $collection;
        $this->directory = $directory;
    }

    /**
     * Сохраняет коллекцию данных в MongoDB.
     *
     * @param array $collection - данные для сохранения
     * @param string $tag - тег коллекции (по умолчанию 'default')
     * @return bool - true, если данные успешно сохранены
     */
    public function pushCollection($collection, $tag = 'default')
    {
        // Создаем документ для вставки
        $document = [
            'directory' => $this->directory,
            'tag' => $tag,
            'data' => $collection,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];

        // Вставляем документ в коллекцию
        $result = $this->collection->insertOne($document);

        // Возвращаем true, если вставка прошла успешно
        return $result->getInsertedCount() > 0;
    }

    /**
     * Получает коллекцию данных из MongoDB.
     *
     * @param string $tag - тег коллекции (по умолчанию 'default')
     * @return array - данные коллекции или пустой массив, если данные не найдены
     */
    public function getCollection($tag = 'default')
    {
        // Ищем документ по директории и тегу
        $document = $this->collection->findOne(
            [
                'directory' => $this->directory,
                'tag' => $tag
            ],
            [
                'sort' => ['created_at' => -1] // Получаем последнюю версию
            ]
        );

        // Если документ найден, возвращаем данные
        if ($document) {
            return $document['data'];
        } else {
            return [];
        }
    }

    /**
     * Сохраняет переменную в MongoDB.
     *
     * @param string $name - имя переменной
     * @param mixed $value - значение переменной
     * @return bool - true, если данные успешно сохранены
     */
    public function setVar($name, $value)
    {
        // Создаем документ для вставки
        $document = [
            'directory' => $this->directory,
            'name' => $name,
            'value' => $value,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];

        // Вставляем документ в коллекцию
        $result = $this->collection->insertOne($document);

        // Возвращаем true, если вставка прошла успешно
        return $result->getInsertedCount() > 0;
    }

    /**
     * Получает переменную из MongoDB.
     *
     * @param string $name - имя переменной
     * @return mixed - значение переменной или false, если переменная не найдена
     */
    public function getVar($name)
    {
        // Ищем документ по директории и имени
        $document = $this->collection->findOne(
            [
                'directory' => $this->directory,
                'name' => $name
            ],
            [
                'sort' => ['created_at' => -1] // Получаем последнюю версию
            ]
        );

        // Если документ найден, возвращаем значение
        if ($document) {
            return $document['value'];
        } else {
            return false;
        }
    }
}
