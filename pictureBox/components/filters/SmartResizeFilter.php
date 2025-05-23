<?php
class SmartResizeFilter extends BaseFilter
{

    public function make()
    {

        if ($this->checkImageformat($this->fileName)) {

            $image = new Imagick($this->fileName);
            $image->setResourceLimit(Imagick::RESOURCETYPE_AREA, 1024 * 1024); // 1 GB
        } else return false;



        // Устанавливаем новую ширину
        $newWidth = $this->param['width'];
        $newHeight = $this->param['height'];

        $aspectRatio = $newWidth / $newHeight;
        if ($aspectRatio >= 1) {
            // Получаем текущие размеры изображения
            $originalWidth = $image->getImageWidth();
            $originalHeight = $image->getImageHeight();

            // Вычисляем новую высоту, чтобы сохранить пропорции
            $resizeHeight = ($newWidth / $originalWidth) * $originalHeight;

            // Изменяем размер изображения пропорционально
            $image->resizeImage((int)$newWidth, (int)$resizeHeight, Imagick::FILTER_LANCZOS, 1);

            // Определяем параметры кропа
            $cropHeight = $newHeight;
            $cropWidth = $newWidth;
            $cropX = 0; // Кроп по горизонтали с начала изображения
            $cropY = ($resizeHeight - $cropHeight) / 2; // Кроп по центру по вертикали
        } else {
            // Получаем текущие размеры изображения
            $originalWidth = $image->getImageWidth();
            $originalHeight = $image->getImageHeight();

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
        if (!$image->cropImage((int)$cropWidth, (int)$cropHeight, (int)$cropX, (int)$cropY)) {
            throw new Exception();
        }


            // Сохраняем измененное изображение
        ;
        if (!$image->writeImage($this->newFileName)) {
            throw new Exception();
        }
    }
}
