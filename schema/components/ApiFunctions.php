<?php
class ApiFunctions
{
    /**
     * Возвращает данные схемы в виде вложенной структуры
     */
    static public function getSchemaData($linkType, $linkId)
    {
        // Преобразуем параметры в массив для вывода в формате JSON
        $result = [
            'linkType' => $linkType,
            'linkId' => $linkId,
        ];
        $SL = new CSchemaLink($linkType, $linkId);
        return $SL->getData();
    }


    /**
     * Возвращает данные схемы в виде линейного массива
     */
    static public function getLineSchemaData($linkType, $linkId)
    {
        // Преобразуем параметры в массив для вывода в формате JSON
        $result = [
            'linkType' => $linkType,
            'linkId' => $linkId,
        ];
        $SL = new CSchemaLink($linkType, $linkId);
        return $SL->getSchemasFieldsData();
    }
}