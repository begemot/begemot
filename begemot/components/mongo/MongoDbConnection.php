<?php
use MongoDB\Client;
use MongoDB\Collection;
// use MongoDB\Database;

class MongoDbConnection extends CApplicationComponent
{
    // Параметры конфигурации
    public $connectionString = 'mongodb://localhost:27017';
    public $database = '';
    public $username = 'admin';
    public $password = '123123123';    
    public $host = 'test_mongodb';
    public $port = '27017';

    // Приватные свойства для хранения подключения
    private $_client = null;
    private $_db = null;

    /**
     * Инициализация компонента
     */
    public function init()
    {
        parent::init();
        $this->connect();
    }

    /**
     * Установка подключения к MongoDB
     */
    protected function connect()
    {

        // Параметры подключения
        $username = $this->username; // Замените на ваш логин
        $password = $this->password; // Замените на ваш пароль
        $host = $this->host;         // Хост сервера MongoDB
        $port = $this->port;               // Порт (по умолчанию 27017)
        $database = $this->database;    // Имя базы данных

    
        // Формируем строку подключения
        $connectionString = "mongodb://{$username}:{$password}@{$host}:{$port}/{$database}?authSource=admin&authMechanism=SCRAM-SHA-256";
        
        // Создаём клиент MongoDB
        try {
            $this->_client = new Client($connectionString);
            $db = $this->database;
            $this->_db = $this->_client->$db;
            // Выбираем базу данных и коллекцию
          //  $collection = $client->$database->schemaData;
         } catch (Exception $e) {
            // Обработка ошибок
            echo "Ошибка подключения: " . $e->getMessage() . "\n";
     }
    }

    /**
     * Получение объекта клиента
     * @return Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->connect();
        }
        return $this->_client;
    }

    /**
     * Получение объекта базы данных
     * @return Database
     */
    public function getDb()
    {
        if ($this->_db === null) {
            $this->connect();
        } 
        return $this->_db;
    }

    /**
     * Получение коллекции
     * @param string $collectionName Название коллекции
     * @return Collection
     */
    public function getCollection($collectionName)
    {
        return $this->getDb()->selectCollection($collectionName);
    }

    /**
     * Вставка документа в коллекцию
     * @param string $collectionName Название коллекции
     * @param array $data Данные для вставки
     * @return mixed Результат вставки (например, InsertOneResult)
     */
    public function insert($collectionName, $data)
    {
        $collection = $this->getCollection($collectionName);
        return $collection->insertOne($data);
    }

    /**
     * Поиск документов в коллекции
     * @param string $collectionName Название коллекции
     * @param array $query Условие поиска
     * @param array $options Опции поиска (например, проекция, сортировка)
     * @return \MongoDB\Driver\Cursor
     */
    public function find($collectionName, $query = array(), $options = array())
    {
        $collection = $this->getCollection($collectionName);
        return $collection->find($query, $options);
    }

    /**
     * Поиск одного документа
     * @param string $collectionName Название коллекции
     * @param array $query Условие поиска
     * @param array $options Опции поиска
     * @return array|null
     */
    public function findOne($collectionName, $query = array(), $options = array())
    {
        $collection = $this->getCollection($collectionName);
        return $collection->findOne($query, $options);
    }

    /**
     * Обновление документов в коллекции
     * @param string $collectionName Название коллекции
     * @param array $filter Условие для поиска
     * @param array $update Новые данные
     * @param array $options Опции обновления
     * @return mixed Результат обновления (например, UpdateResult)
     */
    public function update($collectionName, $filter, $update, $options = array())
    {
        $collection = $this->getCollection($collectionName);
        return $collection->updateOne($filter, $update, $options);
    }

    /**
     * Удаление документов из коллекции
     * @param string $collectionName Название коллекции
     * @param array $filter Условие для удаления
     * @param array $options Опции удаления
     * @return mixed Результат удаления (например, DeleteResult)
     */
    public function remove($collectionName, $filter = array(), $options = array())
    {
        $collection = $this->getCollection($collectionName);
        return $collection->deleteOne($filter, $options);
    }
}