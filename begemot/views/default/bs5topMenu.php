<?php


$begemotMenuArray = require_once (dirname(__FILE__).'/../menu.php');

//$configPath = Yii::getPathOfAlias('webroot.protected.config');
//$localMenuFile = $configPath . '/adminLocalMenu.php';
//if (file_exists($localMenuFile)) {
//    $localMenu = require($localMenuFile);
//    array_unshift($begemotMaenuArray, $localMenu);
//}

function checkVisiblesOfAllSubMenu($menuArray)
{
    $booleanResult = false;

    foreach ($menuArray as $menuItem) {
        if ($menuItem['visible'] == true) {
            $booleanResult = true;
            break;
        }
    }
    return $booleanResult;
}

?>

<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Begemot</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">

                <?php foreach ($begemotMenuArray as $menuItem): ?>

                    <?php

                    if (isset($menuItem['visible']) && $menuItem['visible']==false) {
                        continue;
                    }

                    $url = '#';
                    if (isset($menuItem['url'])) {
                        $url = $this->createUrl($menuItem['url'][0], []);
                    }
                    ?>

                    <?php if (!isset($menuItem['items'])): ?>

                        <li class="nav-item ">
                            <a class="nav-link" aria-current="page" href="<?= $url ?>"><?= $menuItem['label'] ?></a>
                        </li>
                    <?php else: ?>
                        <?php
                        if (!checkVisiblesOfAllSubMenu($menuItem['items'])) continue;
                        ?>
                        <li class="nav-item dropdown">
                            <a  class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <?= $menuItem['label'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($menuItem['items'] as $menuSubItem): ?>
                                    <?php

                                    if (!$menuSubItem['visible']) continue;
                                    $suburl = '#';
                                    if (isset($menuSubItem['url'])) {
                                        $suburl = $this->createUrl($menuSubItem['url'][0], []);
                                    }
                                    ?>
                                    <li><a class="dropdown-item" href="<?= $suburl ?>"><?= $menuSubItem['label'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>


                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>
            <!--            <form class="d-flex" role="search">-->
            <!--                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">-->
            <!--                <button class="btn btn-outline-success" type="submit">Search</button>-->
            <!--            </form>-->
        </div>
    </div>
</nav>
<script>
    function closeAll(){
        $('nav ul.dropdown-menu').removeClass('show')
    }
    function open(element){
        closeAll()
       $(element).parent().find('ul.dropdown-menu').addClass('show')
      // /  console.log( $(element).parent().find('ul.dropdown-menu'))
    }
    $(document).on('click', function() {

        closeAll()
    });


    $('nav li.dropdown').click(function(event) {
        // event.stopPropagation();
        console.log(event.target)
        open(event.target)
        event.stopPropagation();
    });


</script>