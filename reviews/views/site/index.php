<h1>Оставить отзыв:</h1>
<div style="margin-bottom: 100px;;">
    <?php if (!$sendFlag): ?>
        <div id="guestSay">
            <form method="post">


                <input name="name" placeholder="Имя" type="edit""><br><br>
                <textarea name="general" placeholder="Отзыв" type="edit"></textarea>

                <br/> <br/>
                <input type="hidden" value="1" name="formSend"/>
                <input type="submit"/>

            </form>
        </div>
    <?php else: ?>

        <?php if (!$errorsFlag): ?>

            <p>Спасибо, что оставили отзыв о нашей клинике!</p>
        <?php else: ?>
            <p>Произошла ошибка при отправке формы!</p>
            <?php

            foreach ($errors as $error) {
                echo '<p>' . $error[0] . '</p>';
            }
            ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php
$criteria = new CDbCriteria();
$criteria->condition = 'status = 1';
$criteria->order = 'created_at DESC'; // сортируем по дате публикации
$count = Reviews::model()->count($criteria);



// создаем модель для пагинации pagination
$pages = new CPagination($count);
$pages->setPageSize(10); // устанавливаем количество записей на странице
$pages->applyLimit($criteria); // привязываем Criteria

$reviews = Reviews::model()->findAll($criteria);
//Получаем результат для отображения

$dataProvider = new CArrayDataProvider($reviews);

$pagerSettings = array(
    'header' => '', // пейджер без заголовка

    'cssFile'=>'/css/pager.css',

    'firstPageLabel' => '<<',
    'prevPageLabel' => '<',
    'nextPageLabel' => '>',
    'lastPageLabel' => '<<',
    'pages' => $pages, // модель пагинации переданная во View
);
$this->widget('CLinkPager', $pagerSettings);
?>

<?php foreach ($reviews as $review): ?>
    <table width="100%" style="margin-top:30px;" celspadding="0" cellsspacing="0">
        <tbody>
        <tr>
            <td width="380" valign="top" style="padding:0px;margin:0;">
                <p>
                    <?=$review->general;?>
                </p>
            </td>
            <td style="text-align:left;" valign="top" class="guestName"><?=$review->name;?>
            </td>
        </tr>
        </tbody>
    </table>
<?php endforeach; ?>

<?php

$this->widget('CLinkPager', $pagerSettings);

?>
