<?php
Yii::import('begemot.extensions.bootstrap.widgets.TbDataColumn');


class CDataNestedColumn extends TbDataColumn {
    public $categoryIdName = 'pid';

    public function renderDataCell($row)
    {
        $data=$this->grid->dataProvider->data[$row];
        $options=$this->htmlOptions;
        if($this->cssClassExpression!==null)
        {
            $class=$this->evaluateExpression($this->cssClassExpression,array('row'=>$row,'data'=>$data));
            if(!empty($class))
            {
                if(isset($options['class']))
                    $options['class'].=' '.$class;
                else
                    $options['class']=$class;
            }
        }
        echo CHtml::openTag('td',$options);
        if (isset( $data['level'])){
            for ($i=0;$i<$data['level'];$i++){
                echo '===>';
            }
        }
        $this->renderDataCellContent($row,$data);
        echo '</td>';
    }


}