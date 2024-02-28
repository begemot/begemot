<?php

$menu = Yii::app()->controller->menu;
?>
<ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">

<?php foreach ($menu as $menuItem): ?>
    <li class="nav-item">
        <a href="#" class="nav-link align-middle px-0">
            <?=$menuItem['title']?>
        </a>
    </li>
<?php endforeach; ?>




</ul>