$(document).ready(function () {
    console.log('massselect ready');

    $('.btn-mass-cat-connect').click(function () {

        console.log('нажали кнопку массового прикрепления к разделу');

        var itemsIdsForMassCopyArray = new Array;

        var selectedItems = $("input:checkbox:checked.gridCheckbox");
        //console.log(selectedItems);
        $.each(selectedItems, function (index, value) {

            itemsIdsForMassCopyArray.push($(value).attr('data-id'));
        })

        var catsIdsForMassCopyArray = new Array;

        var selectedCatsItems = $("input:checkbox:checked.sectionCheckBox");
        $.each(selectedCatsItems, function (index, value) {

            catsIdsForMassCopyArray.push($(value).attr('data-id'));
        })

        //console.log(catsIdsForMassCopyArray);
        //for (key in selectedItems) {
        //    console.log(selectedItems[key]);
        //}
        $.each(itemsIdsForMassCopyArray, function (index, value) {

            $.ajax({
                url: '/catalog/catCategory/massItemsToCategoriesConnect',
                success: function () {
                    alert('Load was performed.');
                },
                data: {
                    itemId: value,
                    catIds: catsIdsForMassCopyArray,

                }

            });
        });


    });
    $('.btn-mass-move').click(function () {

        console.log('нажали кнопку массового перемещения');
    });

    $('.sectionsBtn').click(function () {


        $('#sectionsModal .sectionCheckBox').attr('checked', false);
        var selectedItemsCount = $("input:checkbox:checked.gridCheckbox").length;
        console.log($('#selectedCount'));
        $('#selectedCount').html(selectedItemsCount);
        if (selectedItemsCount == 0) {
            alert("Вы не выбрали ни одной позиции для копирования/перемещения!");
        }
        else
            $('#sectionsModal').modal('show');

    });

    $('.deleteAllBtn').click(function () {
        var deleteList = new Array;
        var $this = this;
        $('.gridCheckbox:checked').each(function (i, elem) {

            deleteList.push($(elem).attr('data-id'));


        });
        msg = console.log(deleteList, true);
        if (confirm("Удалить все выделенные позиции?")) {
            deleteList.forEach(function (entry) {
                $.ajax({
                    url: '/catalog/catItem/delete',
                    data: {
                        id: entry
                    }
                });
            });
            $.fn.yiiGridView.update("test-grid");

        }


    });

});
