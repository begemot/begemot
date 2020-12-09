<?php
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule("parsers")->basePath . "/assets/parsers.css")
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule("parsers")->basePath . "/assets/jquery.quicksearch.js"), CClientScript::POS_HEAD
);

require(dirname(__FILE__) . '/../menu.php');
?>

<?php $this->widget("bootstrap.widgets.TbMenu", array(
    "type" => "tabs", // "", "tabs", "pills" (or "list")
    "stacked" => false, // whether this is a stacked menu
    "items" => array(
        array("label" => "Изменились", "url" => "/parsers/default/do/file/" . $filename . "/tab/changed", "active" => $tab == "changed"),
        array("label" => "Новые изображения", "url" => "/parsers/default/do/file/" . $filename . "/tab/changedImages", "active" => $tab == "changedImages"),
        array("label" => "Игнорируемые изображения", "url" => "/parsers/default/do/file/" . $filename . "/tab/ignoredImages", "active" => $tab == "ignoredImages"),
        array("label" => "Новые", "url" => "/parsers/default/do/file/" . $filename . "/tab/new", "active" => $tab == "new"),
        array("label" => "Новые с возможностью связать по ID", "url" => "/parsers/default/do/file/" . $filename . "/tab/newWithId", "active" => $tab == "newWithId"),
        array("label" => "Все связи", "url" => "/parsers/default/do/file/" . $filename . "/tab/allSynched", "active" => $tab == "allSynched"),
        array("label" => "Связи с категориями", "url" => "/parsers/default/do/file/" . $filename . "/tab/categorySync", "active" => $tab == "categorySync"),
        array('label' => 'Cоздать новую связанную категорию для ' . $filename, 'url' => array('/parsers/parsersCategoryConnection/createFor/filename/' . $filename), 'active' => $tab == "createFor")


    ),
)); ?>

<h1>Парсер "<?php echo $filename ?>"</h1>

<?php //print_r($itemList["combinedAndChanged"]) ?>


<?php if ($tab == "changed"): ?>

    <form action="/parsers/default/doChecked" method="post">

        <table>
            <thead>
            <tr>
                <td><input type="checkbox" class="checkAll"></td>
                <td>Изображение</td>
                <td>Артикул</td>
                <td>Название</td>
                <td>Старая цена</td>
                <td>Новая цена</td>
                <td>Наличие</td>
                <td>Обновить</td>
            </tr>
            </thead>
            <tbody>
            <?php if ($itemList): ?>
                <?php foreach ($itemList as $item): ?>
                    <tr class="<?php echo $item->id ?>">
                        <td><input type="checkbox" name="item[<?php echo $item->item->id ?>][id]"
                                   value="<?php echo $item->item->id ?>"></td>
                        <td><img src="<?php echo $item->item->getItemMainPicture("innerSmall") ?>"></td>
                        <td><?php echo $item->fromId ?></td>
                        <td class="name"><?php echo $item->item->name ?></td>
                        <td><?php echo $item->item->price ?></td>
                        <td><input type="text" value="<?php echo $item->linking->price ?>"
                                   name="item[<?php echo $item->item->id ?>][price]" class="price input-small"></td>
                        <td><input type="text" value="<?php echo $item->linking->quantity ?>"
                                   name="item[<?php echo $item->item->id ?>][quantity]" class="quantity input-small">
                        </td>
                        <td>
                            <button type="button" class="updatePrice" data-id="<?php echo $item->item->id ?>">Обновить
                                цену и наличие
                            </button>
                        </td>
                    </tr>

                <?php endforeach ?>
            <?php endif ?>
            </tbody>
        </table>

        <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"] ?>">
        <input type="submit" class="btn btn-primary btn-medium" value="Применить выделенные">

    </form>
<?php endif ?>

<?php if ($tab == "changedImages"): ?>
    Поиск по ID: <input type="text" class='id'>
    <button class='search_id'>Поиск по ИД</button>
    <button class='showAllById'>Показать все</button>
    <table id='changedImages'>
        <thead>
        <tr>
            <td>ID карточки</td>
            <td>Артикул карточки</td>
            <td>Название карточки</td>
            <td>ID связи</td>
            <td>Новых изображений</td>
            <td>Добавить изображения</td>
        </tr>
        </thead>
        <tbody>
        <?php if ($itemList): ?>
            <?php foreach ($itemList as $item): ?>
                <?php $id = str_replace('/', '', $item['item']->item->id) ?>
                <tr class="<?php echo $id ?>">
                    <td><?php echo $item['item']->item->id ?></td>
                    <td><img src="<?php echo $item['item']->item->getItemMainPicture("innerSmall") ?>"></td>
                    <td class="name"><?php echo $item['item']->item->name ?>(<a style='text-decoration:underline'
                                                                                target='_blank'
                                                                                href='<?php echo $this->createUrl('/catalog/catItem/update', array('id' => $item['item']->item->id, 'tab' => 'photo')) ?>'>Редактировать</a>)
                    </td>
                    <td><?php echo $item['item']->fromId ?></td>
                    <td><?php echo count($item['images']) ?></td>
                    <td>
                        <button type="button" class="updateImages" data-id="<?php echo $id ?>">Добавить изображения
                        </button>
                    </td>
                    <td style='display:none'>
                        <table class='images-<?php echo $id ?>'>
                            <thead>
                            <tr>
                                <td>Изображение</td>
                                <td>Сохранить его?</td>
                                <td>Игнорировать изображение</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($item['images'] as $image): ?>
                                <tr class='<?php echo $image['md5'] ?>-<?php echo $image['sha1'] ?>'>
                                    <td><img src='<?php echo $image['imageUrl'] ?>' style='width: 100px'></td>
                                    <td><input type='checkbox' name='images[]' value='<?php echo $image['image'] ?>'>
                                    </td>
                                    <td><a href='#' class="ignoreImage" data-md5='<?php echo $image['md5'] ?>'
                                           data-sha1='<?php echo $image['sha1'] ?>'
                                           data-image='<?php echo $image['image'] ?>'>Игнорировать изображение</a></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </td>
                </tr>

            <?php endforeach ?>
        <?php endif ?>
        </tbody>
    </table>
    <div class="modal fade" id="save-images" style="display:none">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Сохранить изображения</h4>
                    <div id="error"></div>
                </div>
                <form data-hideafter=".modal" data-removeafter="">
                    <div class="modal-body">

                        <div class="tab-content">
                            <div id="table-holder"></div>
                            <input type="hidden" id='idHolder'>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
                        <button class="syncCard btn btn-primary" type="submit">Сохранить изображения</button>
                    </div>

                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        $(".search_id").click(function () {
            var id = $(".id").val();

            $("#changedImages").find("TBODY TR").hide();
            $("#changedImages").find("." + id).show();
        })

        $(".showAllById").click(function () {
            $("#changedImages").find("TR").show();
        })

        $(document).on("click", ".ignoreImage", function (e) {
            e.preventDefault;
            var link = $(this);
            var md5 = $(this).attr("data-md5");
            var sha1 = $(this).attr("data-sha1");
            var image = $(this).attr("data-image");


            $.post("/parsers/ignoreImages/create/", {"md5": md5, "sha1": sha1, "image": image}, function (data) {
                $("." + md5 + "-" + sha1).remove();

            }).fail(function (data) {
                alert(data.responseText);
            });
        })

        $(document).on("click", '.updateImages', function () {
            var className = $(this).parents("TR").attr("class");
            var id = $(this).parents("TR").attr("class");

            $("#save-images").show().addClass("in");
            $("#save-images").find("#idHolder").val(id);
            $("#save-images FORM").attr("data-removeafter", "." + className);

            var imageTable = ".images-" + id.replace(/\//g, '');
            $("#table-holder").html("");
            $("#table-holder").html($(imageTable).clone());
            $(".imageCount").html($("#table-holder TR").length - 1);
        });

        $(document).on("submit", "#save-images FORM", function (e) {
            e.preventDefault();

            //save images for synched card
            var images = [];
            var hideAfter = $(this).attr("data-hideafter");
            var removeAfter = $(this).attr("data-removeAfter");

            $("#table-holder INPUT:checked").each(function () {
                images.push($(this).val());
            })

            images = JSON.stringify(images);

            console.log(images);

            $.post("/pictureBox/default/uploadArray", {'images': images, 'id': $("#idHolder").val()}, function (data) {
                //data = $.parseJSON(data);

                console.log(data);

                alert("Сохранено");
                $(hideAfter).removeClass("in").hide();
                $(removeAfter).fadeOut(1000);
                setTimeout(function () {
                    $(removeAfter).remove();
                }, 1000)

            }).fail(function (data) {
                alert(data.responseText);
            });

        })
    </script>
<?php endif; ?>

<?php if ($tab == "ignoredImages"): ?>
    <?php

    if (count($itemList)) {


        Yii::import("begemot.extensions.grid.EImageColumn");

        $this->widget("bootstrap.widgets.TbGridView", array(
            "id" => "test-grid",
            "dataProvider" => $itemList->search(),
            "filter" => $itemList,
            "type" => "striped bordered condensed",
            'rowCssClassExpression' => function ($row, $data) {
                return "item-" . $data->id;
            },
            "columns" => array(
                'md5',
                'sha1',
                array(
                    'class' => 'EImageColumn',
                    'htmlOptions' => array('width' => 120),
                    // see below.
                    'imagePathExpression' => 'str_replace(Yii::getPathOfAlias("webroot"), "", $data->image)',
                    // Text used when cell is empty.
                    // Optional.
                    'emptyText' => '—',
                    // HTML options for image tag. Optional.
                    'imageOptions' => array(
                        'alt' => 'no',
                        'width' => 120,
                        'height' => 120,
                    ),
                ),
                array(
                    'class' => 'bootstrap.widgets.TbButtonColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => [
                            'label' => 'Удалить',
                            'icon' => 'remove',
                            'url' => 'Yii::app()->createUrl("/parsers/ignoreImages/delete", array("id"=>$data->id))',
                            'options' => [
                                'class' => 'btn btn-small',
                            ]
                        ]
                    ]
                ),
            ),
        ));
    } else echo "нету игнорируемых изображений"; ?>
<?php endif ?>

<?php if ($tab == "new"): ?>
    <?php
    Yii::import("begemot.extensions.grid.EImageColumn");

    $this->widget("bootstrap.widgets.TbGridView", array(
        "id" => "test-grid",
        "dataProvider" => $itemList->search(),
        "filter" => $itemList,
        "type" => "striped bordered condensed",
        'rowCssClassExpression' => function ($row, $data) {
            return "item-" . preg_replace('/[^A-Za-z0-9\-]/', '', $data->id);
        },
        "columns" => array(
            "id" => array('name' => 'id', 'htmlOptions' => array('class' => 'id')),
            "name" => array('name' => 'name', 'htmlOptions' => array('class' => 'name')),
            "price" => array('name' => 'price', 'htmlOptions' => array('class' => 'price')),
            array(
                "header" => "Связать с ...",
                "type" => "raw",
                "value" => array($this, "getSyncButtons")
            ),
            array(
                "header" => "Добавить как новый",
                "type" => "raw",
                "value" => array($this, "getAddAsNewButton"),
            ),
        ),
    )); ?>
<?php endif ?>

<?php if ($tab == "newWithId"): ?>
    <?php
    Yii::import("begemot.extensions.grid.EImageColumn");

    $this->widget("bootstrap.widgets.TbGridView", array(
        "id" => "test-grid",
        "dataProvider" => $itemList->search(),
        "filter" => $itemList,
        "type" => "striped bordered condensed",
        'rowCssClassExpression' => function ($row, $data) {
            return "item-" . $data->id;
        },
        "columns" => array(
            "id" => array('name' => 'id', 'htmlOptions' => array('class' => 'id')),
            "name" => array('name' => 'name', 'htmlOptions' => array('name')),
            "price" => array('name' => 'price', 'htmlOptions' => array('price')),
            array(
                "header" => "Связать с ...",
                "type" => "raw",
                "value" => array($this, "getSyncButtons")
            ),
            array(
                "header" => "Добавить как новый",
                "type" => "raw",
                "value" => array($this, "getAddAsNewButton"),
            ),
        ),
    )); ?>
<?php endif ?>

<?php if ($tab == "allSynched"): ?>

    <?php

    Yii::import('begemot.extensions.grid.EImageColumn');

    $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'test-grid',
        'dataProvider' => $itemList->search(),
        'filter' => $itemList,
        'type' => 'striped bordered condensed',
        'columns' => array(

            'fromId',
            array(
                'class' => 'EImageColumn',
                'htmlOptions' => array('width' => 120),
                // see below.
                'imagePathExpression' => '$data->item->getItemMainPicture("innerSmall")',
                // Text used when cell is empty.
                // Optional.
                'emptyText' => '—',
                // HTML options for image tag. Optional.
                'imageOptions' => array(
                    'alt' => 'no',
                    'width' => 120,
                    'height' => 120,
                ),
            ),

            array(
                'header' => 'Связан с ',
                'type' => 'text',
                'value' => '$data->item->name',
            ),
            array(
                'header' => 'Удалить связь',
                'type' => 'raw',
                'value' => array($this, "deteleLinkingButton"),
            )

        ),
    )); ?>

<?php endif ?>

<?php if ($tab == "new" | $tab == "newWithId"): ?>
    <div class="modal fade" style="display:none" id='form-modal'>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Связать элемент с ...</h4>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#combineWith" aria-controls="combineWith"
                                                                  role="tab" data-toggle="tab">Объединить с ...</a></li>
                        <li role="presentation"><a href="#images" aria-controls="images" role="tab" data-toggle="tab">Изображения
                                (<span class='imageCount'>0</span>)</a></li>
                    </ul>

                    <div id="error"></div>
                </div>
                <form action="/parsers/default/syncCard" method="post" class="ajaxSubmit" data-hideafter=".modal"
                      data-removeafter="">
                    <div class="modal-body">

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="combineWith">
                                Нажмите на название карточки когда выберите в списке.<br/>
                                <input type="text" id="search" placeholder="Поиск">
                                <ul id="search_in_items">
                                    <?php foreach ($allItems as $item): ?>
                                        <li data-itemid="<?php echo $item->id ?>"><?php echo $item->name ?>
                                            (<?php echo $item->id ?>) <a
                                                    href="<?php echo $this->createUrl("/catalog/catItem/update", array("id" => $item->id)) ?>">Просмотреть</a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="images">
                                <div id="table-holder"></div>
                            </div>
                        </div>

                        <input type="hidden" name="ParsersLinking[filename]" id="filename" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
                        <button class="syncCard btn btn-primary" type="submit">Соединить карточку (<span
                                    class="cardName"></span>)
                        </button>
                        <br/>
                        <div class="bottom" style="margin-top: 10px;">
                            <div style="margin:auto">Сохранится для id: <input type="text" name="ParsersLinking[fromId]"
                                                                               id="name" class="required" required>
                            </div>
                            <div style="margin:auto">Привяжет к ID карточки: <input type="text"
                                                                                    name="ParsersLinking[toId]"
                                                                                    id="itemIdModal" required></div>
                        </div>

                    </div>

                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


<?php endif ?>

<script>

    $(document).on("change", ".search", function () {
        $(this).quicksearch(".tableToSearch tbody tr", {
            selector: $(this).attr("data-selector")
        });
    })

    $(document).on("click", ".addAsNew", function () {
        console.log('Нажали кнопку добавить как новое');
        var button = $(this);
        var params = {
            "CatItem": {
                "name": $(this).attr("data-name"),
                "price": $(this).attr("data-price"),
                "text": $(this).attr("data-text")
            }, "returnId": true
        };

        $.post("/catalog/catItem/ajaxCreate", params, function (data) {
            button.parents("TR").find(".composite").html("Уже обьединено");
            button.parents("TR").find("BUTTON").attr("disabled", true);
            button.parent().html('<a href="/catalog/catItem/update/id/' + data + '" target="_blank">Редактировать</a>');
            var toId = data;


            $.post(
                "/parsers/default/syncCard",
                {
                    "ParsersLinking": {
                        "fromId": button.attr("data-id"),
                        "toId": toId,
                        "filename": button.attr("data-filename")
                    }
                },

                function (data) {
                    data = $.parseJSON(data);
                    console.log(data);
                    if (data.code) {

                        if (data.toUpdate != "") {
                            $("#toDo").find("TBODY").append(data.toUpdate);
                            $(".countChanged").html(parseInt($(".countChanged").html()) + 1);
                        }

                        if (data.toAllLinks != "") {
                            $("#combined").find("TBODY").append(data.toAllLinks);
                        }
                        console.log(data);
                    }
                });

            if (button.attr("data-images") != "") {
                $.post("/pictureBox/default/uploadArray", {
                    "images": button.attr("data-images"),
                    'id': toId
                }, function (data) {
                    //data = $.parseJSON(data);
                }).fail(function (data) {
                    alert(data.responseText);
                });
            }
            ;

            if (button.attr("data-groups") != "") {
                $.post("/parsers/default/updateCategories", {
                    "groups": button.attr("data-groups"),
                    'id': toId
                }, function (data) {
                    //data = $.parseJSON(data);
                }).fail(function (data) {
                    alert(data.responseText);
                });
            }
            ;


            if (button.attr("data-parents") != "") {

                $.post("/parsers/default/updateOptions", {
                    "fromId": button.attr("data-id"),
                    'id': toId,
                    'parents': button.attr("data-parents")
                }, function (data) {

                }).fail(function (data) {
                    alert(data.responseText);
                });
            }

            if (button.attr("data-modifs") != "") {

                $.post("/parsers/default/updateModifs", {
                    "fromId": button.attr("data-id"),
                    'id': toId,
                    'modifs': button.attr("data-modif")
                }, function (data) {

                }).fail(function (data) {
                    alert(data.responseText);
                });
            }

            $.post("/parsers/default/updateOtherFields", {"id": button.attr("data-id")}, function (data) {
            }).fail(function (data) {
                alert(data.responseText);
            });

        })

    })

    $(document).on("click", ".deleteLinking", function (e) {

        var link = $(this);
        var id = $(this).attr("data-id");


        $.get("/parsers/default/deleteLinking/id/" + id, function (data) {
            if (data == "1") {
                link.parents("TR").fadeOut("1000");

                if ($("." + id).attr("class") != "") {
                    if (parseInt($(".countChanged").html() >= 0)) {
                        $(".countChanged").html(parseInt($(".countChanged").html()) - 1);
                    }

                    $("." + id).remove();
                }
                ;
                setTimeout(function () {
                    link.parents("TR").remove();
                }, 1000)

            } else {
                alert("error");
            }

        }).fail(function (data) {
            alert(data.responseText);
        });

    })


    $(document).on("click", ".composite", function () {
        var name = $(this).parents("TR").find(".name").html();
        var id = $(this).parents("TR").find(".id").html();
        var className = $(this).parents("TR").attr("class");
        var price = $(this).parents("TR").find(".price").html();
        var filename = $(this).attr("data-filename");

        var imageTable = "#images-" + id.replace(/\//g, '');
        if ($(imageTable).length) {
            $("#table-holder").html($(imageTable).clone());
            $(".imageCount").html($("#table-holder TR").length - 1);
        } else {
            $("#table-holder").html("");
            $(".imageCount").html("0");
        }

        $("#search").quicksearch("UL#search_in_items li").search(name);
        $("#search").val(name);
        $(".error").hide();


        $("#search_in_items LI").each(function () {
            if ($(this).css("display") != "none") {
                $(this).addClass("active");
                $("#itemIdModal").val($(this).attr("data-itemid"));
                return false;
            }

        })

        $("#form-modal").show().addClass("in");
        $("#form-modal FORM").attr("data-removeafter", "." + className);
        $("#form-modal FORM").find(".cardName").html(name);
        $("#form-modal FORM").find("#name").val(id);
        $("#form-modal FORM").find("#filename").val(filename);
    })

    $(document).on("click", "#search_in_items LI", function () {
        $(this).siblings().removeClass("active");
        $(this).addClass("active");

        $("#itemIdModal").val($(this).attr("data-itemid"));

    })

    $(document).on("click", ".checkAll", function () {

        if ($(this).prop("checked")) {
            $(this).parents("TABLE").find("input[type='checkbox']").prop("checked", true);
        }
        else $(this).parents("TABLE").find("input[type='checkbox']").prop("checked", false);
    })


    $(document).on("submit", ".ajaxSubmit", function (e) {
        e.preventDefault();
        var form = $(this);
        var hideAfter = $(this).attr("data-hideafter");
        var removeAfter = $(this).attr("data-removeAfter");
        var status = true;
        $(this).find(".required").removeClass("form_error");

        $(this).find(".required").each(function () {
            if ($(this).val() == "") {
                status = false
            }
            $(this).addClass("form_error");
        })
        if (!status) return false;

        $.post(form.attr("action"), form.serialize(), function (data) {
            data = $.parseJSON(data);
            if (data.code) {
                $(hideAfter).removeClass("in").hide();
                $(removeAfter).fadeOut(1000);
                setTimeout(function () {
                    $(removeAfter).remove();
                }, 1000)

                if (data.toUpdate != "") {
                    $("#toDo").find("TBODY").append(data.toUpdate);
                    $(".countChanged").html(parseInt($(".countChanged").html()) + 1);
                }

                if (data.toAllLinks != "") {
                    $("#combined").find("TBODY").append(data.toAllLinks);
                }

                console.log(data);

                //save images for synched card
                var images = [];

                $("#table-holder INPUT:checked").each(function () {
                    images.push($(this).val());
                })

                imagesJson = JSON.stringify(images);

                if (images.length > 0) {


                    $.post("/pictureBox/default/uploadArray", {
                        'images': imagesJson,
                        'id': $("#itemIdModal").val()
                    }, function (data) {

                        //data = $.parseJSON(data);
                        console.log(data);
                    }).fail(function (data) {
                        alert(data.responseText);
                    });
                }

            } else {
                $("#error").html(data.html);
                $("#error").fadeIn();
                $(".modal-body").scrollTo(0);
            }
        }).fail(function (data) {
            alert(data.responseText);
        });
    })

    $(document).on("click", ".closeModal", function (e) {
        $(".modal").removeClass("in").hide();
    })

    $(".close").click(function () {
        $(".modal").removeClass("in").addClass("out");
    })

    $(document).on("click", ".updatePrice", function (e) {

        var button = $(this);
        var params = {
            "id": $(this).attr("data-id"),
            "price": $(this).parents("TR").find(".price").val(),
            "quantity": $(this).parents("TR").find(".quantity").val()
        };


        $.post("/parsers/default/updateCard", params, function (data) {
            if (data == "1") {
                button.parents("TR").fadeOut("1000");
                setTimeout(function () {
                    button.parents("TR").remove();
                }, 1000)

            }
            ;

        }).fail(function (data) {
            alert(data.responseText);
        });

    })


    $("input#search").quicksearch("UL#search_in_items li");


    $.fn.scrollTo = function (target, options, callback) {
        if (typeof options == "function" && arguments.length == 2) {
            callback = options;
            options = target;
        }
        var settings = $.extend({
            scrollTarget: target,
            offsetTop: 50,
            duration: 500,
            easing: "swing"
        }, options);
        return this.each(function () {
            var scrollPane = $(this);
            var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
            var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
            scrollPane.animate({scrollTop: scrollY}, parseInt(settings.duration), settings.easing, function () {
                if (typeof callback == "function") {
                    callback.call(this);
                }
            });
        });
    }

</script>