<?php

$menu = Yii::app()->controller->menu;
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    .menu-toggle-button {
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1050;
        /* Ensure it is above other elements */
        background-color: #007bff;
        border: none;
        border-radius: 0;
        padding: 10px 15px;
        color: #fff;
    }

    .menu-toggle-button i {
        font-size: 1.5rem;
    }
</style>
<button class="btn menu-toggle-button" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu" id="toggleButton">
    <i class="fas fa-arrow-right"></i>
</button>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
        
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <?php foreach ($menu as $menuItem) : ?>
                <li class="nav-item <?php echo isset($menuItem['items']) ? 'dropdown' : ''; ?>">
                    <?php if (isset($menuItem['url']) && is_array($menuItem['url'])) : ?>
                        <a href="<?php echo Yii::app()->createUrl($menuItem['url'][0], array_slice($menuItem['url'], 1)); ?>" class="nav-link align-middle px-0">
                            <?php echo $menuItem['label']; ?>
                        </a>
                    <?php elseif (isset($menuItem['url'])) : ?>
                        <a href="<?php echo Yii::app()->createUrl($menuItem['url']); ?>" class="nav-link align-middle px-0">
                            <?php echo $menuItem['label']; ?>
                        </a>
                    <?php elseif (isset($menuItem['items'])) : ?>
                        <a href="#" class="nav-link align-middle px-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $menuItem['label']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($menuItem['items'] as $subMenuItem) : ?>
                                <li>
                                    <?php if (isset($subMenuItem['url']) && is_array($subMenuItem['url'])) : ?>
                                        <a href="<?php echo Yii::app()->createUrl($subMenuItem['url'][0], array_slice($subMenuItem['url'], 1)); ?>" class="dropdown-item">
                                            <?php echo $subMenuItem['label']; ?>
                                        </a>
                                    <?php elseif (isset($subMenuItem['url'])) : ?>
                                        <a href="<?php echo Yii::app()->createUrl($subMenuItem['url']); ?>" class="dropdown-item">
                                            <?php echo $subMenuItem['label']; ?>
                                        </a>
                                    <?php else : ?>
                                        <span class="dropdown-item">
                                            <?php echo $subMenuItem['label']; ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <span class="nav-link align-middle px-0 fw-bold">
                            <?php echo $menuItem['label']; ?>
                        </span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var offcanvasMenu = document.getElementById('offcanvasMenu');
        var toggleButton = document.getElementById('toggleButton');

        offcanvasMenu.addEventListener('show.bs.offcanvas', function() {
            toggleButton.style.display = 'none';
        });

        offcanvasMenu.addEventListener('hidden.bs.offcanvas', function() {
            toggleButton.style.display = 'block';
        });
    });
</script>