<?php

$menu= array(
    array('label' => 'Семантическое Ядро (СЯ)'),
    array('label' => 'Группы слов', 'url' => array('/seo/seoWordGroup/index')),
    array('label' => 'Слова', 'url' => array('/seo/seoWord/admin')),
    array('label' => 'CSV Загрузка', 'url' => array('/seo/seoWord/CsvUpload')),
    array('label' => 'CSV Экспорт', 'url' => array('/seo/seoWord/csvExport')),

    array('label' => 'Парсер'),

    array('label' => 'Страницы', 'url' => array('/seo/seoPages/admin')),

    array('label' => 'Ссылки', 'url' => array('/seo/seoLinks/admin')),
    array('label' => 'Спарсить', 'url' => array('/seo/webParser/index')),
    array('label' => 'Теги', 'url' => array('/seo/webParser/tags')),
    array('label' => 'Уникальность', 'url' => array('/seo/webParser/pagesCheck')),

    array('label' => 'Прочее'),
    array('label' => 'Редактор meta-тегов', 'url' => array('/seo/title/index')),
);

return $menu;
?>
