$(document).ready(function(){
    console.log('massselect ready');

    $('.deleteAllBtn').click(function(){
        var deleteList =  new Array;
        var $this = this;
        $('.gridCheckbox:checked').each(function(i,elem){

            deleteList.push($(elem).attr('data-id'));


        });
        msg = console.log(deleteList,true);
        if (confirm("Удалить все выделенные позиции?")) {
            deleteList.forEach(function(entry){
                $.ajax({
                    url: '/catalog/catItem/delete',
                    data:{
                        id:entry
                    }
                });
            });
            $.fn.yiiGridView.update("test-grid");

        }


    });

});
