<?php
//echo '<pre>';
//print_r($config);
//echo '</pre>';
//return;
?>
<ul class='tiles' id="sortable">
    <?php


    ?>
    <?php foreach ($data['images'] as $key => $image):


        ?>
        <li >
            <img class="tile-img" data-id="<?= $key ?>" src="<?php echo $image['admin'] ?>"/>

            <img title="Редактирование изображений" data-id="<?= $key ?>" data-gallery-id="<?= $id  ?>" class="all-images-btn" src="/protected/modules/pictureBox/assets/images-tiles/all-images-btn.png" />
            <img title="Редактирование title и alt" data-id="<?= $key ?>" data-gallery-id="<?= $id  ?>" class="title-btn" src="/protected/modules/pictureBox/assets/images-tiles/title-alt-btn.png" />
            <img title="Избранное" data-id="<?= $key ?>" data-gallery-id="<?= $id  ?>" class="fav-btn" src="/protected/modules/pictureBox/assets/images-tiles/star-grey.png" />
            <img title="Удалить" data-id="<?= $key ?>" data-gallery-id="<?= $id  ?>" class="delete-btn" src="/protected/modules/pictureBox/assets/images-tiles/delete.png" />
        </li>
    <?php endforeach; ?>
</ul>




