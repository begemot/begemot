<?php
class MongoToTable
{

    public static function actionGetCharacteristics($linkType, $groupId)
    {
        // Подключение к MongoDB
        $mongo = Yii::app()->mongoDb;
        $db = $mongo->getDb();

        try {
            // 1. Получаем schemaData по linkType и groupId
            $schemaData = $db->schemaData->findOne([
                'linkType' => $linkType,
                'groupId' => (int)$groupId
            ]);

            if (!$schemaData) {
                MysqlToMongo::migrate();
                throw new CHttpException(404, 'Данные не найдены');
            }

            // 2. Получаем все поля для данной схемы, отсортированные по order

            $schemaFields = iterator_to_array($db->schemaField->find(
                ['schemaId' => $schemaData['schemaId']],
                ['sort' => ['order' => 1]]
            ));

            // 3. Получаем все единицы измерения
            $schemaUnits = iterator_to_array($db->schemaUnitOfMeasurement->find());
            $dataKey = 'data';
            // Создаем массив для результата
            $result = [
                'title' => $schemaData['fields']['Название']['value'] ?? '',
                $dataKey => []
            ];

            // Преобразуем единицы измерения в удобный формат
            $unitsMap = [];
            foreach ($schemaUnits as $unit) {
                $unitsMap[$unit['id']] = $unit;
            }

            // Формируем массив характеристик
            foreach ($schemaFields as $field) {
                $fieldName = $field['name'];

                // Пропускаем если нет такого поля в данных или значение "нет данных"
                if (!isset($schemaData['fields'][$fieldName])) {
                    continue;
                }

                $fieldValue = $schemaData['fields'][$fieldName]['value'];
                if ($fieldValue === 'нет данных') {
                    continue;
                }

                // Получаем единицу измерения если есть
                $unit = null;
                if (isset($field['UoFId']) && $field['UoFId'] !== null) {
                    $unitId = $field['UoFId'];
                    if (isset($unitsMap[$unitId])) {
                        $unit = [
                            'name' => $unitsMap[$unitId]['name'],
                            'abbreviation' => $unitsMap[$unitId]['abbreviation']
                        ];
                    }
                }

                $result[$dataKey][] = [
                    'name' => $fieldName,
                    'value' => $fieldValue,
                    'unit' => $unit,
                    'order' => $field['order']
                ];
            }

            // Возвращаем результат в JSON формате
            // header('Content-Type: application/json');
            return $result;
            // Yii::app()->end();
        } catch (MongoConnectionException $e) {
            throw new CHttpException(500, 'Ошибка подключения к базе данных');
        }
    }
}
