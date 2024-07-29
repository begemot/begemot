<?php
class MaxSmartResizeFilter extends BaseFilter{
       
    public function make (){
        throw new Exception('Не удалось сохранить изображение!');
        if ($this->checkImageformat($this->fileName)) {

            $image = new Imagick($this->fileName);
        } else {
            throw new Exception('Фильтр не создался!');
        }



        // Устанавливаем новую ширину
        $newWidth = $this->param['width'];
        $newHeight = $this->param['height'];

        $originalWidth = $image->getImageWidth();
        $originalHeight = $image->getImageHeight();

        if (!$originalWidth || !$originalHeight){
            throw new Exception('Размер изображения не импортировался!');
        }

        if ($originalWidth<$newWidth) return;
        if ($originalHeight<$newHeight) return;

        $aspectRatio = $newWidth / $newHeight;
        if ($aspectRatio >= 1) {
            // Получаем текущие размеры изображения


            // Вычисляем новую высоту, чтобы сохранить пропорции
            $resizeHeight = ($newWidth / $originalWidth) * $originalHeight;

            // Изменяем размер изображения пропорционально
            $image->resizeImage($newWidth, $resizeHeight, Imagick::FILTER_LANCZOS, 1);

            // Определяем параметры кропа
            $cropHeight = $newHeight;
            $cropWidth = $newWidth;
            $cropX = 0; // Кроп по горизонтали с начала изображения
            $cropY = ($resizeHeight - $cropHeight) / 2; // Кроп по центру по вертикали
        } else {
            // Получаем текущие размеры изображения


            // Вычисляем новую высоту, чтобы сохранить пропорции
            $resizeWidth = ($newHeight / $originalHeight) * $originalWidth;

            // Изменяем размер изображения пропорционально
            $image->resizeImage($resizeWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);

            // Определяем параметры кропа
            $cropHeight = $newHeight;
            $cropWidth = $newWidth;
            $cropX = ($resizeWidth - $cropWidth) / 2; // Кроп по горизонтали с начала изображения
            $cropY = 0; // Кроп по центру по вертикали 
        }


        // Кроп изображения
        $image->cropImage($cropWidth, $cropHeight, $cropX, $cropY);

        // Сохраняем измененное изображение
        if (!$image->writeImage($this->newFileName)){
            throw new Exception('Не удалось сохранить изображение!');
        }





    }
    
}
?>
