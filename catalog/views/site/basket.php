<?php
Yii::app()->clientScript->registerScriptFile('/js/menu.js', 0);
Yii::app()->clientScript->registerScriptFile('/js/basket.js', 0);
?>
<div class="catalog container" id="basket">


    <?php
    $leftMenu = Yii::getPathOfAlias('webroot') . '/themes/classic_g2/views/catalog/site/leftMenu.php';
    require($leftMenu);

    ?>


    <div class="rightContainer" style="min-height: 1000px;margin-left:20px;">


        <h1 class="catalog-title">Корзина</h1>
        <hr>
        <?php
        Yii::import('catalog.components.CBasketState');
        $basketState = new CBasketState();

        ?>
        <?php if (count($basketState->getItems())>0): ?>
            <?php foreach ($basketState->getItems() as $id => $item): ?>

                <?php $catItem = CatItem::model()->findByPK($id); ?>
                <?php if (is_null($catItem)){continue;} ?>
                <?php

                Yii::import('pictureBox.components.PBox');
                $pbox = new PBox('catalogItem', $id);

                $image = $pbox->getFirstImage('main');

                ?>

                <div class="basketRow" style="display: flex">
                    <div class="basketRowImage"><img src="<?= $image ?>" alt=""></div>
                    <div class="basketRowTitle"><?= $catItem->name; ?></div>
                    <div class="basketRowCount"><a href="" class="button blue basketRow__btn">+</a>

                        <input class="ok basketRow__input" type="text" value="<?= $item['count'] ?>">
                        <a href="" class="button blue basketRow__btn">-</a>
                    </div>
                    <div><?= $catItem->price; ?></div>
                    <div><a href="javaScript:;"><img class="basketRow__deleteBtn"
                                                     src="/images/shop/basketDeleteCross.png"
                                                     alt=""></a></div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
        <br>
        <hr>
        <div class="basketResultRow">
            <div>ИТОГ:</div>
            <div>9000000</div>
        </div>
        <hr>
        <h2 class="catalog-title shipment">Доставка</h2>
        <div class="basketRadioRow">
            <div class="radio">
                <input id="self" type="radio" name="gender" value="self">
                <label for="self">Самовывоз</label>
            </div>
            <div>
                <input id="man" type="radio" name="gender" value="man">
                <label for="man">Курьер по Москве</label>
            </div>
            <div>
                <input id="post" type="radio" name="gender" value="post">
                <label for="post">Почта России(по РФ)</label>
            </div>
            <div>
                <input id="dellin" type="radio" name="gender" value="dellin">
                <label for="dellin">Деловые линии(по РФ)</label>
            </div>
        </div>
        <h2 class="catalog-title shipment">Личные данные</h2>
        <form id="basketForm" method="POST" class="form" _lpchecked="1">
            <div style="display: flex;flex-wrap: wrap">


                <div id="leftBasketForm">


                    <div class="row required ">
                        <label for="ContactForm[name]">Имя:</label>
                        <input type="text" name="ContactForm[name]" id="name" value="" tabindex="1"
                               style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">


                    </div>

                    <div class="row required ">
                        <label for="ContactForm[phone]">Телефон:</label>

                        <input class="ok" type="text" name="ContactForm[phone]" id="phone" value="" tabindex="2">

                    </div>

                    <div class="row required">
                        <label for="ContactForm[email]">Почта:</label>
                        <input type="text" name="ContactForm[email]" id="phone" value="" tabindex="2">
                    </div>
                    <div class="code">
                        <img id="yw0" src="/site/captcha/v/5a3674c51af07.html" alt=""><a id="yw0_button"
                                                                                         href="/site/captcha/refresh/1.html">Получить
                            новый код</a>
                    </div>

                    <div class="row required">
                        <label for="ContactForm[phone]">Защитный код:</label>
                        <input class="ok" type="text" name="ContactForm[verifyCode]" id="phone" value="" tabindex="2">
                    </div>
                    <br>

                </div>
                <div id="rightFormPart">
                    <div class="row required">
                        <label for="textarea">Сообщение:</label>
                        <textarea cols="40" rows="8" name="ContactForm[body]" id="textarea"></textarea>
                    </div>
                </div>

            </div>
            <div id="basketBtn">
                <div class="row">
                    <input type="submit" class="button yellow" value="Отправить">
                </div>
            </div>
        </form>
    </div>
</div>