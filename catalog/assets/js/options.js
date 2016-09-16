/**
 * Created by Николай Козлов on 25.08.2016.
 */
$(document).ready(function () {
    var $itemId = $('.optionsTable').attr('data-item-id');


    $('#optionSelectmodal #option-slect-end-btn').click(function () {

        var selectedOptions = Array();
        var deselectedOptions = Array();
        $('#optionSelectmodal').find('input').each(function (index) {
            if ($(this).prop('checked')) {
                selectedOptions.push($(this).parent().parent().attr('data-item-id'));

            } else {
                deselectedOptions.push($(this).parent().parent().attr('data-item-id'));
            }
        });
        console.log(selectedOptions);
        console.log($itemId);
        var $selectedOptionId = $('#optionSelectmodal').attr('data-option-for-relation-id');
        $.ajax({
            url: '/catalog/catItemOptions/ajaxUpdateOptionRelation',
            data: {
                itemId: $selectedOptionId,
                optionIdArray: selectedOptions,
                deselectedOptions:deselectedOptions
            }
        })
    });

    $('#option-slect-close-btn').click(function(){
        $('#optionSelectmodal').modal('hide');
    });

    $('#optionSelectmodal').on('show', function () {
        var $selectedOptionId = $('#optionSelectmodal').attr('data-option-for-relation-id');
        $.ajax({
            url: '/catalog/catItemOptions/LayoutOptionsTable',
            data: {
                paentItemId: $itemId,
                selectedOptionId:$selectedOptionId
            }
        }).done(function (data) {

            $('#optionSelectmodal .modal-body').html(data);

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
        //selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Уже в опциях...'>",
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
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });

    $('.searchable2').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Поиск по опциям...'>",
        //selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Уже в опциях...'>",
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
        afterSelect: function () {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
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


