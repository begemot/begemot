<?php

use MongoDB\Client;
use MongoDB\Exception\Exception;

class MongoCounters
{
    private $client;
    private $countersCollection;

    /**
     * Конструктор класса
     * @param string $uri Строка подключения к MongoDB (например, "mongodb://localhost:27017")
     * @param string $database Имя базы данных
     */
    public function __construct()
    {
        try {
            $this->client = Yii::app()->mongoDb->getDb();
            $this->countersCollection = $this->client->selectCollection('counters');
        } catch (Exception $e) {
            throw new RuntimeException("Ошибка подключения к MongoDB: " . $e->getMessage());
        }
    }

    public function getValue(string $counterName)
    {
        if ($this->countersCollection->contDocuments(['_id' => $counterName]) != 0) {

            $this->countersCollection->findOne(['_id' => $counterName])->value;
        } else {
            return 1;
        }
    }
    // public function setValue(string $counterName, $value)
    // {
    //     if ($this->countersCollection->contDocuments(['_id' => $counterName]) != 0) {

    //         $this->countersCollection->findOne(['_id' => $counterName])->value;
    //     } else {
    //         return 1;
    //     }
    // }
    /**
     * Получение и увеличение значения счетчика по имени
     * @param string $counterName Имя счетчика (например, "catalog_counter")
     * @return int Новое значение счетчика
     * @throws RuntimeException Если не удалось получить или обновить значение
     */
    public function getNextValue(string $counterName): int
    {
        try {
            $result = $this->countersCollection->findOneAndUpdate(
                ['_id' => $counterName],
                ['$inc' => ['value' => 1]],
                [
                    'upsert' => true,           // Создать документ, если его нет
                    'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER // Вернуть обновленный документ
                ]
            );

            if ($result === null) {
                throw new RuntimeException("Не удалось получить значение счетчика для '$counterName'");
            }

            // Если счетчик только что создан, вручную установим начальное значение 1
            if (!isset($result->value)) {
                $this->countersCollection->updateOne(
                    ['_id' => $counterName],
                    ['$set' => ['value' => 1]]
                );
                return 1;
            }

            return (int) $result->value;
        } catch (Exception $e) {
            throw new RuntimeException("Ошибка при работе со счетчиком '$counterName': " . $e->getMessage());
        }
    }
}
