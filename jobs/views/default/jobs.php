<?php
require(dirname(__FILE__).'/mainMenu.php');
?>


<h1>Все работы</h1>
<div class="success" style='color: green'></div>
<table>
    <thead>
    <tr>
        <td>Название файла</td>
        <td>Действия</td>
    </tr>
    </thead>
    <tbody>

    <?php $number = 0; ?>
    <?php foreach($itemList as $item): ?>
        <tr class='item-<?php echo $number ?>'>
            <?php $itemName = new $item; ?>
            <td><?php  echo $itemName->getName();?></td>
            <td>
                <input type="button"
                       class='btn btn-info run'
                       data-name='<?php echo $item?>'
                       value='Запустить'>
                <input type="button"
                       class='btn btn-inverse manualStart'
                       data-name='<?php echo $item?>'
                       value='Ручной режим'>
                <input type="button"
                       class='btn btn-primary setTask'
                       data-name='<?php echo $item?>'
                       data-turn='0'
                       value='Запланировать'>

                <?php if ($jobManager->isTaskSettedUp($item) == true): ?>
                    (уже установлено хотя бы одно задание)
                <?php endif ?>



                <div class='additionalFields' style='display: none;'><?php $item = new $item; echo $item->getHtmlOfParameters(); ?></div>
            </td>
        </tr>
        <?php $number++; ?>
    <?php endforeach ?>

    </tbody>
</table>



<div class="modal fade" style='display:none'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Установить задачу (<span class='name'></span>)</h4>
            </div>
            <form action="/jobs/default/setTask" method='post' class='ajaxSubmit' data-hideafter='.modal' data-removeafter=''>
                <div class="modal-body">
                    Примеры значений: <br/>
                    *<br/>
                    1,2,3,4,5<br/>
                    10/3<br/>
                    <table>
                        <tr>
                            <td>Минуты(0-59): </td>
                            <td><input type='text' name='min'/</td>
                        </tr>
                        <tr>
                            <td>Часы(0-23): </td>
                            <td><input type='text' name='hour'/></td>
                        </tr>
                        <tr>
                            <td>Дни(1-31):</td>
                            <td><input type='text' name='day'/></td>
                        </tr>
                        <tr>
                            <td>Месяца(1-12):</td>
                            <td><input type='text' name='month'/></td>
                        </tr>
                        <tr>
                            <td>Дни недели(0-6):</td>
                            <td><input type='text' name='dayWeek'/></td>
                        </tr>
                    </table>
                    <input type="hidden" name='filename' class='filename'/>
                    <input type="hidden" class='listname'/>

                    <div class='additional'></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
                    <button type='submit' name='createNew' class='syncCard btn btn-primary' type='submit'>Сохранить</button>


                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

    $(document).on("click", ".manualStart", function(){

        var button = $(this);
        var params =
        {
            'name': $(this).attr("data-name"),
            'type':'manual'
        };
        button.val('Делается...')

        $(".doing").html("делается").fadeIn();
        $.post('/jobs/default/runJob/', params,  function(data){
            $(".success").html("Задача запустилась").fadeIn();
            button.val('Запустить')
            $(".doing").fadeOut();

            setTimeout(function(){$(".success").fadeOut()}, 10000);
        }).fail(function(){
            alert("Не вышло");
        });

    })

    $(document).on("click", ".removeTask", function(){
        var button = $(this);
        var params = {'name': $(this).attr("data-name")};


        $.post('/jobs/default/removeTask/', params,  function(data){
            $(button).parents("TR").find(".setTask").show();
            $(button).parents("TR").find(".removeTask").hide();

        }).fail(function(){
            alert("Не вышло");
        });

    })

    $(document).on("click", ".run", function(){
        var button = $(this);
        var params = {'name': $(this).attr("data-name")};
        button.val('Делается...')

        $(".doing").html("делается").fadeIn();
        $.post('/jobs/default/runJob/', params,  function(data){
            $(".success").html("Задача запустилась").fadeIn();
            button.val('Запустить')
            $(".doing").fadeOut();

            setTimeout(function(){$(".success").fadeOut()}, 10000);
        }).fail(function(){
            alert("Не вышло");
        });

    })


    $(document).on("click", ".closeModal", function(e){
        $(".modal").removeClass('in').hide();
    })

    $(".close").click(function(){
        $(".modal").removeClass("in").addClass("out");
    })

    $(document).on("click", ".setTask", function(){
        var button = $(this);

        $(".modal").show().addClass("in");
        $(".modal .filename").val($(this).attr("data-name"));
        $(".modal .name").html($(this).attr("data-name"));
        $(".modal .listname").val($(this).parents("TR").attr("class"));
        $(".modal .additional").html($(this).parents("TR").find(".additionalFields").html());

    })

    $(document).on("submit", ".ajaxSubmit", function(e){
        e.preventDefault();
        var form = $(this);
        var hideAfter = $(this).attr("data-hideafter");
        var removeAfter = $(this).attr("data-removeAfter");
        var status = true;
        $(this).find(".required").removeClass("form_error");

        $(this).find(".required").each(function(){
            if ($(this).val() == "") {status = false}
            $(this).addClass("form_error");
        })
        if (!status) return false;

        $.post(form.attr("action"), form.serialize(), function(data){

            $(removeAfter).fadeOut(1000);
            setTimeout(function(){
                $(removeAfter).remove();
                $(hideAfter).removeClass('in').hide();
            }, 1000)

            $(form).find("#success").html("Сохранено");

            $("." + $(form).find(".listname").val()).find(".setTask").hide();

            $("." + $(form).find(".listname").val()).find(".removeTask").show();
        }).fail(function(){
            alert("Не вышло");
        });
    })
</script>
