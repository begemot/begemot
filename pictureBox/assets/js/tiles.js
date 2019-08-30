var domElementForDelete;
function loadPage(page, state, divId) {

    state.imageNumber = page;

    refreshPictureBox(divId, state)
}

function setTitleAlt(state, divId) {
    var title = $("#" + divId + " input[name=title]").val();
    var alt = $("#" + divId + " input[name=alt]").val();
    var data = {};
    data.title = title;
    data.alt = alt;
    data.id = state.id;
    data.elementId = state.elementId;
    data.pictureId = state.pictureBoxPage;

    $.ajax({
        url: "/pictureBox/default/ajaxSetTitle",
        data: data,
        cache: false,
        async: true,
        type: "post",
        success: function (html) {

            alert("Сохранено. ");
            refreshPictureBox(divId, state);
        }
    });
}

function refreshPictureBox(divId, state) {

    $.ajax({
        url: "/pictureBox/default/ajaxLayout",
        data: {
            id:$id,
            elementId:$elementId
        },
        cache: false,
        async: false,
        success: function (html) {

            $("#" + divId).html(html);


        },
        error: function (param, param1, param2) {
            alert(param.responseText);
        }
    });
}
function favRefresh(data) {
    $("img.fav-btn").each(function (indx, element) {
        if (data[$(element).attr("data-id")] == undefined) {
            $(element).attr("src", "/protected/modules/pictureBox/assets/images-tiles/star-grey.png");
        } else {
            $(element).attr("src", "/protected/modules/pictureBox/assets/images-tiles/star-yellow.png")
        }
    });

}

function favGetDataAndRefresh(){
    $.ajax({
        url: "/pictureBox/default/ajaxGetFavArray",
        cache: false,
        async: false,
        data: {
            pictureId: $(this).attr("data-id"),
            elementId: $elementId,
            id: $id

        },
        success: function (html) {

            favRefresh($.parseJSON(html));
        }
    });
}

$(function () {
    $("#sortable").sortable(
        {
            cursor: "move",
            stop: function (event, ui) {
                updateSortArray()
            },
            cancel: "img.delete-btn,img.fav-btn"
        }
    );


    $("img.fav-btn").click(function () {

        imageId =  $(this).attr('data-id');
        favArray = getFavArray();


        if (favArray[imageId]!=undefined){


            $.ajax({
                url: '/pictureBox/default/ajaxDelFav/',
                cache: false,
                async: false,
                data: {
                    pictureId: $(this).attr("data-id"),
                    elementId: $elementId,
                    id: $id

                },
                success: function (html) {
                    favArray = getFavArray();
                    favRefresh(favArray);
                }
            });
        } else {
            $.ajax({
                url: '/pictureBox/default/ajaxAddFav/',
                cache: false,
                async: false,
                data: {
                    pictureId: $(this).attr("data-id"),
                    elementId: $elementId,
                    id: $id

                },
                success: function (html) {
                    favArray = getFavArray();
                    favRefresh(favArray);
                }
            });
        }



    });

    function getFavArray(){
        var json;
        $.ajax({
            url: "/pictureBox/default/ajaxGetFavArray",
            cache: false,
            async: false,
            data: {
                pictureId: $(this).attr("data-id"),
                elementId: $elementId,
                id: $id

            },
            success: function (html) {

                json = $.parseJSON(html);
            }
        });

        return json;
    }

    $("img.delete-btn").click(function (index, domElement) {

        domElementForDelete = $(this).parent();
        if (confirm("Вы уверены, что хотите удалить изображение?"))
        $.ajax({
            url: "/pictureBox/default/ajaxDeleteImage",
            cache: false,
            async: false,
            data: {
                pictureId: $(this).attr("data-id"),
                elementId: $elementId,
                id: $id

            },
            success: function (html) {

                $(domElementForDelete).remove();
            }
        });
    });


    //  $( "#sortable" ).disableSelection();
});



function updateSortArray() {
    var sortArray = Object();
    var sortIndex = 0;
    $("div#" + $divId + " ul.tiles img.tile-img").each(
        function (index, domElement) {

            sortArray[$(domElement).attr("data-id") + ""] = sortIndex;
            sortIndex++;

        });

    $.ajax({
        url: "/pictureBox/default/newSortOrder",
        data: {
            sort: sortArray,
            galleryId: $id,
            id: $elementId

        },
        cache: false,
        async: false,
        success: function (html) {


        },
        error: function (param, param1, param2) {
            alert(param.responseText);
        }
    });

}

$(document).ready(function(){

    var myDropzone = Object();

    favGetDataAndRefresh();

         myDropzone.$divId = new Dropzone("#dropzone_"+$divId,
        {
            url: '/pictureBox/default/upload',
            acceptedFiles:'image/*',
            paramName :'Filedata',
            success:function(){
                var state;
                refreshPictureBox($divId, state);

            },
            params: {
                id:$id,
                elementId: $elementId,
                config:$config
            }
        });



});
