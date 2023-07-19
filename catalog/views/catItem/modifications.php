<h4>Модификации</h4>
    <?php
        $items = CatItem::model()->findAllByAttributes(['modOfThis'=>$model->id]);

        if (count($items)!=0){
            foreach ($items as $item) {
                Yii::app()->clientScript->registerCss('link',
                    "
                        .td-link {
                            margin-left:15px;
                            padding: 4px;
                            background-color: #ccc;
                            cursor: pointer;
                            border-radius: 5px;
                        }
                        .td-link.active {
                            background-color: orange;
                            border: 1px solid orange;
                        }
                        "
                );
//                $active = ($cat->through_display == 0) ? '' : 'active';
//                $linkTitle = (empty($active)) ? 'Неактивно' : 'Активно';
                echo "<div class='category-item'>";
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'ajaxButton',
                    'icon' => 'icon-remove',
                    'label' => '',
                    'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                    'size' => 'mini', // null, 'large', 'small' or 'mini'
                    'url' => '/catalog/catItem/deleteModifFromItem/itemId/'  . $item->id,
                    'ajaxOptions' => array('success' => 'function (){location.reload()}'),
                ));
                echo $item->name;
//                echo ($cat->catId != $cat->item->catId)
//                    ? ' <a href="?setMainCat=' . $cat->catId . '">' . CatCategory::model()->getCatName($cat->catId) . '</a>'
//                    : ' ' . CatCategory::model()->getCatName($cat->catId) . '  [<strong>основной раздел</strong>]';

//                if ($cat->cat != null && $cat->cat->pid != -1)
//                    echo "<a title='Сквозное отображение - $linkTitle' class='$active td-link' data-value='" . $cat->catId . "'><span class='icon icon-white icon-retweet'></span></a>";
                echo "</div>";
                echo "<br>";
            }
        } else {
            echo '<h4 style="color:red;">Модификаций у этого вездехода нет!</h4>';
        }
    ?>
    <h4>Поиск и добавление модификаций</h4>

    <form method='post'>
        <select id='custom-headers' multiple='multiple' name='modif[]' class='searchable'>
            <?php
            if (!$model->isNewRecord):
                $alreadyGot = CatItem::model()->findAllByAttributes(['modOfThis'=>$model->id]);

                $arrayOfItems = array();
                foreach ($alreadyGot as $item) {
                    array_push($arrayOfItems, $item->id);
                }
                $arrayOfItems = array_filter($arrayOfItems);
                $items = CatItem::model()->findAll(array('order'=>'id ASC'));

                if (is_array($items) && count($items)>0):

                    foreach ($items as $item): ?>

                        <?php $checked = in_array($item->id, $arrayOfItems) ? "selected" : "" ?>

                        <option <?php echo $checked?> value="<?php echo $item->id?>"><?php echo $item->name?>(<?php echo number_format($item->price, 0, ',', ' ');?> руб.)</option>


                    <?php endforeach;
                endif;
            endif;
            ?>
        </select>
        <script>
            $('.searchable').multiSelect({
                selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по товарам...'>",
                selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Текущие модификации'>",
                afterInit: function(ms){
                    var that = this,
                        $selectableSearch = that.$selectableUl.prev(),
                        $selectionSearch = that.$selectionUl.prev(),
                        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                        .on('keydown', function(e){
                            if (e.which === 40){
                                that.$selectableUl.focus();
                                return false;
                            }
                        });

                    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                        .on('keydown', function(e){
                            if (e.which == 40){
                                that.$selectionUl.focus();
                                return false;
                            }
                        });
                },
                afterSelect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                },
                afterDeselect: function(){
                    this.qs1.cache();
                    this.qs2.cache();
                }
            });
        </script>
        <br/>
        <input type="submit" name='saveModif' class='btn btn-primary' value='сохранить'/>
        <br/>



