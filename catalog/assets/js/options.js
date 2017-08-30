/**
 * Created by Николай Козлов on 25.08.2016.
 */
$(document).ready(function () {
    var $itemId = $('.optionsTable').attr('data-item-id');

    $('.ajaxOptionPrice').focusout(function(){
        $.ajax({
            url: '/catalog/catItem/update',
            data: "id="+$(this).attr('data-item-id')+"&changePrice="+$(this).val(),
            success: function(){
                alert('Load was performed.');
            }
        });
    });

    $('.optionsTable tbody').sortable(
        {
            stop: function(event, ui) {
                var i = 0;
                var sortCollection = {};
                $('.optionsTable tbody tr').each(function(){
                    i++;
                    sortCollection[i]=$(this).attr('data-item-id');

                });
                var itemId = $('.optionsTable').attr('data-item-id');
                $.ajax({
                    url: '/catalog/catItemOptions/ajaxNewOptionOrder',
                    data: {
                        sortCollection: sortCollection,
                        itemId:itemId
                    }
                })
            }
        }
    );


    $('#option-slect-close-btn').click(function(){
        $('#optionSelectmodal').modal('hide');
    });

    $('#optionSelectmodal').on('show', function () {
        var $selectedOptionId = $('#optionSelectmodal').attr('data-option-for-relation-id');

        $target =  $('#optionSelectmodal').attr('data-target');

        if ($target!='conflict'){
            $msg='Выбираем зависимые опции';
        } else {
            $msg='Выбираем конфликтные опции';
        }
        $('div.modal-header > h3').html($msg);
        $.ajax({
            url: '/catalog/catItemOptions/LayoutOptionsTable',
            data: {
                paentItemId: $itemId,
                selectedOptionId:$selectedOptionId,
                target: $('#optionSelectmodal').attr('data-target'),
            }
        }).done(function (data) {

            $('#optionSelectmodal .modal-body').html(data);

            $trHtml = $('#optionSelectmodal .modal-body table tbody .getIt').html();
            $('#optionSelectmodal .modal-body table tbody .getIt').remove();
            $('#optionSelectmodal .modal-header table tbody').html($trHtml);
            $('#optionSelectmodal .modal-header table tbody input').remove();
            $('#optionSelectmodal input').click(function () {

                var $selectedOptionId = $('#optionSelectmodal').attr('data-option-for-relation-id');

                var action = '';
                if ($(this).prop('checked')) {
                    action = 'connect';

                } else {
                    action = 'disconnect';
                }
                var childrenOptionId = $(this).parent().parent().attr('data-item-id');

                $.ajax({
                    url: '/catalog/catItemOptions/ajaxUpdateOptionRelation',
                    data: {
                        baseItemId: $selectedOptionId,
                        childrenOptionId:childrenOptionId,
                        action:action,
                        target: $('#optionSelectmodal').attr('data-target')
                    }
                })

                $checkedCount = $('#optionSelectmodal input:checkbox:checked').length;
                if ($checkedCount>0){
                    btnMsg = 'есть '+$checkedCount;
                } else {
                    btnMsg = 'нет опций';
                }
                if ($('#optionSelectmodal').attr('data-target')!='conflict') {
                    $('[data-item-id=' + $selectedOptionId + ']').find('a.btn-add-option-relation').html(btnMsg);
                } else {
                    $('[data-item-id=' + $selectedOptionId + ']').find('a.btn-add-option-conflict').html(btnMsg);
                }
                return;
                var selectedOptions = Array();
                var deselectedOptions = Array();
                $('#optionSelectmodal').find('input').each(function (index) {

                });

                var $selectedOptionId = $('#optionSelectmodal').attr('data-option-for-relation-id');


                //$('#optionSelectmodal input').length;

            })

        });
    });

    $('.choseOptions').click(function () {
        if ($(this).attr('data-status') == 0) {
            $('.ms-container').css('display', 'block');
            $(this).attr('data-status', 1);
            $(this).html("Скрыть");

        } else {
            $('.ms-container').css('display', 'none');
            $(this).attr('data-status', 0);
            $(this).html("Выбрать опции");
        }
    });

    $(".fancybox").fancybox({
        openEffect: 'none',
        closeEffect: 'none'
    });


    $('.optionsTable tr').each(function (elem) {
        createAllEventHandlerOnTr($(this));
    });


    $('.searchable').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по опциям...'>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Уже в опциях...'>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function (values) {
            this.qs1.cache();
            this.qs2.cache();
            optionSelected(values);
//                console.log(this.qs1.cache());
        },
        afterDeselect: function (values) {

            this.qs1.cache();
            this.qs2.cache();
            optionDeselected(values);
        }
    });

    $('.searchable2').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по опциям...'>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Карточка опция у этих позиций'>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function (values) {
            this.qs1.cache();
            this.qs2.cache();
            console.log('выбрали');
            console.log(values);

            var $itemId = values.shift();
            var $optionId = $('.optionsTable').attr('data-item-id');

            $.ajax({
                url: '/catalog/catItemOptions/ajaxNewOptionFor',
                data: {
                    itemId: $itemId,
                    optionId: $optionId
                },
                success: function (data) {

                }
            })

        },
        afterDeselect: function (values) {
            this.qs1.cache();
            this.qs2.cache();
            console.log('убрали');
            console.log(values);


            var $optionId = values.shift();
            var $itemId = $('.optionsTable').attr('data-item-id');

            $.ajax({
                url: '/catalog/catItemOptions/ajaxRemoveOptionFrom',
                data: {
                    itemId: $optionId,
                    optionId:  $itemId
                },
                success: function (data) {

                }
            })

        }
    });
});

//function removeOption(id, subid) {
//    $.ajax({
//        url: '/catalog/catItem/options/id/' + id + '/subid/' + subid,
//        success: function () {
//            $('#' + subid).remove();
//        }
//    });
//}

function optionSelected(itemId) {

    var $optionId = itemId.shift();
    var $itemId = $('.optionsTable').attr('data-item-id');

    $.ajax({
        url: '/catalog/catItemOptions/ajaxNewOptionFor',
        data: {
            itemId: $itemId,
            optionId: $optionId
        },
        success: function (data) {

        }
    }).done(function () {
        addTableRow($optionId, $itemId)
    });


}

function optionDeselected(itemId) {

    var $optionId = itemId.shift();
    var $itemId = $('.optionsTable').attr('data-item-id');

    $.ajax({
        url: '/catalog/catItemOptions/ajaxRemoveOptionFrom',
        data: {
            itemId: $itemId,
            optionId: $optionId
        },
        success: function (data) {

        }
    })
  $('table.optionsTable [data-item-id = '+$optionId+']').remove();

}

function addTableRow($itemId, $optionId) {
    $.ajax({
        url: '/catalog/catItemOptions/ajaxRenderTableOptionRow',
        data: {
            itemId: $itemId,
            optionForThisId: $optionId
        },
        success: function (data) {

        }
    }).done(function (data) {

        $('.optionsTable').append(data);
        elem = $('[data-item-id=' + $itemId + ']');
        createAllEventHandlerOnTr(elem);
    });
}


function createAllEventHandlerOnTr(jqElem) {


    jqElem.find('.btn-add-option-relation').click(function () {
        itemId = $(this).parent().parent().attr('data-item-id');
        $('#optionSelectmodal').attr('data-option-for-relation-id',itemId);
        $('#optionSelectmodal').attr('data-target','relation');
        $('#optionSelectmodal').modal({show: true});
        //data-option-for-relation-id
    });

    jqElem.find('.btn-add-option-conflict').click(function () {
        itemId = $(this).parent().parent().attr('data-item-id');
        $('#optionSelectmodal').attr('data-option-for-relation-id',itemId);
        $('#optionSelectmodal').attr('data-target','conflict');
        $('#optionSelectmodal').modal({show: true});
        //data-option-for-relation-id
    });

    jqElem.find('input').click(function () {


        optionId = $(this).parent().parent().attr('data-item-id');
        itemId = $(this).parent().parent().parent().parent().attr('data-item-id');
        $.ajax({
            url: '/catalog/catItemOptions/ajaxChangeIsBaseState',
            data: {
                itemId: itemId,
                optionId: optionId
            },
            success: function (data) {

            }
        }).done(function () {
        });
    });

    jqElem.find('.removeOption').click(function () {

        optionId = $(this).parent().parent().attr('data-item-id');
        itemId = $(this).parent().parent().parent().parent().attr('data-item-id');
        console.log($(this).parent().parent().parent().parent());
        $.ajax({
            url: '/catalog/catItemOptions/ajaxRemoveOptionFrom',
            data: {
                itemId: itemId,
                optionId: optionId
            },
            success: function (data) {

            }
        }).done(function () {

        });
        optionId = $(jqElem).parent().parent().attr('data-item-id');
        $('#custom-headers').multiSelect('deselect', [optionId]);
        //console.log($('#custom-headers [value='+itemId+"]").prop('checked',false));
        $(this).parent().parent().remove();
    });
}


