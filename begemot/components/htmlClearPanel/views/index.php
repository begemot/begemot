<?php
$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Убрать теги',
    'type' => 'primary',
    'size' => 'mini',
    'htmlOptions' => array('onClick' => 'clearTags("' . $id . '");')
));
echo ' ';
$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Очистить html атрибуты',
    'type' => 'primary',
    'size' => 'mini',
    'htmlOptions' => array('onClick' => 'clearTableStyles("' . $id . '");')
));
$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Убрат strong и b',
    'type' => 'primary',
    'size' => 'mini',
    'htmlOptions' => array('onClick' => 'clearTagStrongAndP("' . $id . '");')
));
$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Убрат пустые строки',
    'type' => 'primary',
    'size' => 'mini',
    'htmlOptions' => array('onClick' => 'clearDrawLine("' . $id . '");')
));
?>
<script>
    function clearDrawLine(id) {

        var str = CKEDITOR.instances[id].getData();
        str = str.replace(/<p>&nbsp;<\/p>/g, '');
        var val = CKEDITOR.instances[id].setData(str);

    }

    function clearTags(id) {

        var str = CKEDITOR.instances[id].getData();
        str = str.replace(/<\/?\w+((\s+\w+(\s*=\s*(?:".*?"|'.*?'|[^'">\s]+))?)+\s*|\s*)\/?>/g, '');
        var val = CKEDITOR.instances[id].setData(str);

    }

    function clearTagStrongAndP(id) {

        var str = CKEDITOR.instances[id].getData();
        str = str.replace(/<strong[^>]*>/g, '');
        str = str.replace(/<b[^>]*>/g, '');
        var val = CKEDITOR.instances[id].setData(str);

    }

    //newString = "my XXzz".replace(/(X+)(z+)/, replacer)


    function clearTableStyles(id) {

        var str = CKEDITOR.instances[id].getData();
        str = str.replace(/(<[a-zA-Z]+) [^>]*/g, "$1");
        var val = CKEDITOR.instances[id].setData(str);

    }
</script>