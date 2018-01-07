<?php
$this->menu = array(
    array('label' => 'Все задания', 'url' => array('/jobs/default/index')),
    array('label' => 'Все работы', 'url' => array('/jobs/default/jobs')),
    array('label' => 'Все работы', 'url' => array('/jobs/default/jobs')),
);
?>
<h1>Установленные работы по рассписанию</h1>

<form action="/parsers/default/parseChecked" method='get'>
    <table>
        <thead>
        <tr>
            <td>Название файла</td>
            <td>Включенно</td>
            <td>Период исполнения</td>
            <td>Последний раз выполнялось</td>
            <td>Действия</td>
        </tr>
        </thead>
        <tbody>
    
        <?php $number = 0; ?>
        <?php if ($itemList): ?>
            <?php foreach($itemList as $key => $item): ?>
                <tr class='item-<?php echo $number ?>'>
                    <td><?php echo $key?></td>
                    <td><?php
                        if (isset($item['executable']))
                            echo ($item['executable'] == true) ? "Да" : "Нет"?></td>
                    <td class='period'><?php

                        if (isset($item['type'])&&$item['type']=='manual'){
                            echo 'Ручной режим';
                        } else {
                            $jobManager = new JobManager(); echo $jobManager->getPeriodOfItem($key);
                        }


                        ?></td>

                    <td>
                        <?php
                        if ($item['lastExecuted'] == 0) {
                             echo "Еще не выполнялась<br/>";
                        } else  echo date("d.m.Y H:i", $item['lastExecuted']) ."<br/>"; ?>
                    </td>
                    <td>
                        <input type="button"
                               class='btn-mini btn-primary turnOnOff'
                               data-name='<?php echo $key?>'
                               data-turn='1'
                               value='Включить'
                               style='display: <?php echo ($item['executable'] == true) ? 'none' : 'inline'?>'>
                        <input type="button"
                               class='btn-mini btn-danger turnOnOff'
                               data-name='<?php echo $key?>'
                               data-turn='0'
                               value='Отключить'
                               style='display: <?php echo ($item['executable'] == true) ? 'inline' : 'none'?>'>
                        <input type="button"
                               class='btn-mini btn-danger removeTask'
                               data-name='<?php echo $key?>'
                               value='Удалить задачу'>
                        <input type="button" name='<?php echo $key?>' class='btn-mini btn-info changeTime' value='Поменять период'>

                        <?php
                        if (isset($item['type'])&&$item['type']=='manual'):?>
                            <input type="button"
                                   class='btn-mini btn-primary manualStart'
                                   data-name='<?php echo $key?>'
                                   data-turn='1'
                                   value='Принудительный запуск'>
                        <?php endif;?>
                        <input type="hidden" name='min' value='<?php echo (isset($item['min'])) ? $item['min'] : ""?>'>
                        <input type="hidden" name='hour' value='<?php echo (isset($item['hour'])) ? $item['hour'] : ""?>'>
                        <input type="hidden" name='day' value='<?php echo (isset($item['day'])) ? $item['day'] : ""?>'>
                        <input type="hidden" name='month' value='<?php echo (isset($item['month'])) ? $item['month'] : ""?>'>
                        <input type="hidden" name='dayWeek' value='<?php echo (isset($item['dayWeek'])) ? $item['dayWeek'] : ""?>'>
                    </td>
                </tr>
                <?php $number++; ?>
        <?php endforeach ?>
        <?php endif ?>
        

        </tbody>
    </table>

</form>


<div class="modal fade" style='display:none'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Поменять период</h4>
            </div>
            <form action="/jobs/default/changeTime" method='post' class='ajaxSubmit' data-hideafter='.modal' data-removeafter=''>
                <div class="modal-body">
                    <div id="error"></div>
                    <div id="success" style='color: green'></div>
                    <h3>Изменение периода для <span class='name'></span></h3>
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
                    <input type="hidden" name='name' value='' class='name'>
                    <input type="hidden" class='item' />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
                    <button class='syncCard btn btn-primary' type='submit' name='changeTime'>Сохранить</button>


                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>


    $(document).on("click", ".turnOnOff", function(){
        var button = $(this);
        var params = {'name': $(this).attr("data-name"), 'turn': $(this).attr("data-turn")};


        $.post('/jobs/default/turnOnOff/', params,  function(data){

            button.parents("TD").find('input').css("display", 'inline');
            button.css("display", 'none');

        }).fail(function(){
            alert("Не вышло");
        });

    })
    $(document).on("click", ".manualStart", function(){
        var button = $(this);
        var params = {'name': $(this).attr("data-name")};

        button.val('Выполняется...');

        $.post('/jobs/default/manualRunJob/', params,  function(data){
            $(".success").html("Задача запустилась").fadeIn();
            button.val('Задача запустилась');
            $(".doing").fadeOut();

            setTimeout(function(){$(".success").fadeOut()}, 10000);
            location.reload();
        }).fail(function(){
            alert("Не вышло");
        });


    })
    $(document).on("click", ".removeTask", function(){
        var button = $(this);
        var params = {'name': $(this).attr("data-name")};


        $.post('/jobs/default/removeTask/', params,  function(data){
            $(button).parents("TR").remove();
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

    $(document).on("click", ".changeTime", function(){
        var button = $(this);

        $(".modal").show().addClass("in");
        $(".modal .name").val($(this).attr("name"));
        //$(".modal .name").html($(this).attr("name"));
        $(".modal .item").val(button.parents("TR").attr("class"));

        $(".modal INPUT[name='min']").val($(this).parents("TR").find("INPUT[name='min']").val());
        $(".modal INPUT[name='hour']").val($(this).parents("TR").find("INPUT[name='hour']").val());
        $(".modal INPUT[name='day']").val($(this).parents("TR").find("INPUT[name='day']").val());
        $(".modal INPUT[name='month']").val($(this).parents("TR").find("INPUT[name='month']").val());
        $(".modal INPUT[name='dayWeek']").val($(this).parents("TR").find("INPUT[name='dayWeek']").val());
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

            $("." + $(form).find(".item").val()).find(".period").html(data);

            $("." + $(form).find(".item").val()).find(".time").html($(form).find("SELECT[name='hour'] OPTION:selected").html() + ":" + $(form).find("SELECT[name='minutes'] OPTION:selected").html());
        }).fail(function(){
            alert("Не вышло");
        });
    })
</script>