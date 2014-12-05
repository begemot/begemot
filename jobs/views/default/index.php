<?php
$this->menu = array(
    array('label' => 'Все задания', 'url' => array('/jobs/default/index')),
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
            <td>Исполняется в</td>
            <td>Действия</td>
        </tr>
        </thead>
        <tbody>
    
        <?php $number = 0; ?>
        <?php foreach($itemList as $key => $item): ?>
            <tr class='item-<?php echo $number ?>'>
                <td><?php echo $key?></td>
                <td><?php echo (isset($item['executable']) & $item['executable'] == true) ? "Да" : "Нет"?></td>
                <td class='period'><?php if(isset($item['time'])) echo JobManager::timeToString($item['time']);?></td>
                <td class='time'>
                    <?php if(isset($item['hour'])) echo JobManager::timeToString($item['hour']) . ":00";?></td>
                <td>
                    <input type="button"
                           class='btn btn-primary turnOnOff'
                           data-name='<?php echo $key?>'
                           data-turn='1'
                           value='Включить'
                           style='display: <?php echo ($item['executable'] == true) ? 'none' : 'inline'?>'>
                    <input type="button"
                           class='btn btn-danger turnOnOff'
                           data-name='<?php echo $key?>'
                           data-turn='0'
                           value='Отключить'
                           style='display: <?php echo ($item['executable'] == true) ? 'inline' : 'none'?>'>
                    <input type="button"
                           class='btn btn-danger removeTask'
                           data-name='<?php echo $key?>'
                           value='Удалить задачу'>
                    <input type="button" name='<?php echo $key?>' class='btn btn-info changeTime' value='Поменять период'>
                </td>
            </tr>
            <?php $number++; ?>
        <?php endforeach ?>

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

                    Временой период: <select name="time">
                        <option value="604800">Раз в неделю в понедельник</option>
                        <option value="302400">Два раза в неделю(в понедельник и четверг)</option>
                        <option value="86400">Каждый день</option>
                    </select><br/>
                    Точное время выполнения: <select name="hour">
                        <option value='3600'>1</option>
                        <option value='10800'>3</option>
                        <option value='25200'>7</option>
                        <option value='32400'>9</option>
                        <option value='36000'>10</option>
                        <option value='43200'>12</option>
                        <option value='54000'>15</option>
                        <option value='64800'>18</option>
                        <option value='82800'>23</option>
                    </select> час<br/>

                    <input type="hidden" name='name' value='' class='name'>
                    <input type="hidden" class='item' />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
                    <button class='syncCard btn btn-primary' type='submit'>Сохранить</button>


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

            $("." + $(form).find(".item").val()).find(".time").html($(form).find("SELECT[name='hour'] OPTION:selected").html() + ":00");
        }).fail(function(){
            alert("Не вышло");
        });
    })
</script>