$(document).ready(function(){
    updateCountersOnPage();
    updatePriceSumOnPage();
});

function removeFromBasket(id){
    $.ajax({
        url: '/catalog/basketControll/basketAjaxRemoveItem',
        dataType : "json",
        data:{
            id:id
        },
        success: function (data, textStatus) {
            updateCountersOnPage();
            updatePriceSumOnPage();

        }
    });
}

function addToBasket(id,elem){
    var elemForEvent = elem;
    $.ajax({
        url: '/catalog/basketControll/basketAjaxAddItem',
        dataType : "json",
        data:{
            id:id
        },
        success: function (data, textStatus) {
            $(window).trigger('hasAddedToBasket',{elem:elemForEvent});
            updateCountersOnPage();
            updatePriceSumOnPage();
        }
    });
}

function dinBasketItemCount(id,elem){
    console.log(elem);
    $.ajax({
        url: '/catalog/basketControll/ajaxBasketDinCount',
        dataType : "json",
        data:{
            itemId:id
        },
        success: function (data, textStatus) {

            $(elem).parent().find('input').val(data);
            updateCountersOnPage();
            updatePriceSumOnPage();

        }
    });
}

function addBasketItemCount(id,elem){
    $.ajax({
        url: '/catalog/basketControll/ajaxBasketAddCount',
        dataType : "json",
        data:{
            itemId:id
        },
        success: function (data, textStatus) {
            $(elem).parent().find('input').val(data);
            updateCountersOnPage();
            updatePriceSumOnPage();
        }
    });
}

function setBasketItemCount(id,count,elem){
    $.ajax({
        url: '/catalog/basketControll/ajaxBasketsetCount',
        dataType : "json",
        data:{
            itemId:id,
            count:+count
        },
        success: function (data, textStatus) {

            updateCountersOnPage();
            updatePriceSumOnPage();
        }
    });
}

function updateCountersOnPage(){


    $.ajax({
        url: '/catalog/basketControll/ajaxGetBasketCount',
        dataType : "json",

        success: function (data, textStatus) {
            count = data;

            $('.basketCountInformer').html(count);
        }
    });



}

function updatePriceSumOnPage(){


    $.ajax({
        url: '/catalog/basketControll/ajaxGetBasketPriceSum',
        dataType : "json",

        success: function (data, textStatus) {
            count = data;

            $('.basketPriceSumInformer').html(count);
        }
    });



}


