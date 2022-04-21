<?php
Yii::import('pictureBox.components.filters.*');
/**
 * Класс служит для пакетной работы с изображениями.
 *
 * Может пименять к изображению один фильтр или очередь фильтров.
 *
 */
class FiltersManager
{

    private $config;
    private $fileName;
    public $resultFiles = array();

    public function __construct($_fileName, $_config)
    {

        $this->config = $_config;
        $this->fileName = $_fileName;


        $this->checkFilters();
    }

    public function getFilteredImages()
    {
        return $this->resultFiles;
    }

    public function checkFilters()
    {

        $filePathInfo = pathinfo($this->fileName);

        $filePath = $filePathInfo['dirname'];
        $fileName = $filePathInfo['basename'];
        $fileExt = $filePathInfo['extension'];
        $fileNameClear = $this->delFileExt($fileName);

        foreach ($this->config['imageFilters'] as $filterName => $filters) {

            $filterCount = 0;
            foreach ($filters as $filter) {

                $filterCount++;

                $resultFileName = $filePath . '/' . $fileNameClear . '_' . $filterName . '.' . $fileExt;
                $resultFileNameForOutput = $fileNameClear . '_' . $filterName . '.' . $fileExt;

                $filterClassName = $filter['filter'] . 'Filter';

                // Если мы рендерим фотки из оригинала повторно, то нужно удалить
                // старые варианты, иначе менеджер будет считать старые изображения
                // результатом текущей уже запущенной очереди.  

                if ($filterCount == 1 && file_exists($resultFileName)) {
                    unlink($resultFileName);
                }

                if (file_exists($resultFileName)) {

                    /**
                     * Если существует, то фильтруем существующий
                     * т.к. очередь фильтров уже началась
                     */
                    $filterInstance = new $filterClassName($resultFileName, $resultFileName, $filter['param']);
                } else {
                    /**
                     * Если не существует, то фильтруем оригинал
                     */
                    $filterInstance = new $filterClassName($this->fileName, $resultFileName, $filter['param']);
                }

                $filterInstance->make();
                $this->resultFiles[$filterName] = $resultFileNameForOutput;
            }
        }

        if (isset($this->config['original'])) {
            foreach ($this->config['original'] as $originalFilter) {
                $filterClassName = $originalFilter['filter'] . 'Filter';
                $filterInstance = new $filterClassName($this->fileName, $this->fileName, $originalFilter['param']);
                $filterInstance->make();
            }
        }
    }

    //возвращаем 
    public function delFileExt($filename)
    {


        return pathinfo($filename, PATHINFO_FILENAME);
    }

}

?>
