<?php


/* @var $this CatItemController */
/* @var $model CatItem */

Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('catalog')->basePath . '/assets/js/editItem.js'),
    CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerCssFile('/protected/modules/catalog/assets/css/admin-catalog.css');

$this->breadcrumbs = array(
    'Cat Items' => array('index'),
    $model->name => array('view', 'id' => $model->id),
    'Update',
);

$this->menu = require dirname(__FILE__) . '/commonMenu.php';
?>

<h4>Редактирование позиции "<?php echo $model->name; ?>"</h4>

<ul class="nav nav-tabs">
    <!-- Данные -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'data' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>">
            Данные
        </a>
    </li>

    <!-- Разделы -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'cat' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/cat">
            Разделы
        </a>
    </li>

    <!-- Опции (с выпадающим меню) -->
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle <?php echo ($tab == 'options' || $tab == 'configuration') ? 'active' : ''; ?>" 
           data-toggle="dropdown" 
           href="#" 
           role="button" 
           aria-haspopup="true" 
           aria-expanded="false">
            Опции
        </a>
        <div class="dropdown-menu">
            <?php if (isset(Yii::app()->modules['parsers'])): ?>
                <a class="dropdown-item <?php echo $tab == 'options' ? 'active' : ''; ?>" 
                   href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/options">
                    Список опций
                </a>
                <a class="dropdown-item <?php echo $tab == 'optionsImport' ? 'active' : ''; ?>" 
                   href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/optionsImport">
                    Импорт опций
                </a>
            <?php endif; ?>
        </div>
    </li>

    <!-- Парсер -->
    <?php if (isset(Yii::app()->modules['parsers'])): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'parser' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/parser">
            Парсер
        </a>
    </li>
    <?php endif; ?>

    <!-- Перемещение позиции -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'position' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/position">
            Перемещение позиции
        </a>
    </li>

    <!-- Изображения -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'photo' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/photo">
            Изображения
        </a>
    </li>

    <!-- Видео -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'video' ? 'active' : ''; ?>" 
           >
            Видео
        </a>
    </li>

    <!-- Модификации -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'modifications' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/modifications">
            Модификации
        </a>
    </li>

    <!-- Цвета -->
    <li class="nav-item">
        <a class="nav-link <?php echo $tab == 'colors' ? 'active' : ''; ?>" 
           href="/catalog/catItem/update/id/<?php echo $model->id; ?>/tab/colors">
            Цвета
        </a>
    </li>
</ul>
