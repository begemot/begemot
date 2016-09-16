//Глобальная коллекция объектов, что бы можно было управлять выделениями со стороны
var areaSelectCollection = {};

function PictureBox(options) {

    this.default = {};

    this.options = {};

    this.options = $.extend({}, this.default, options);

    var divId = this.options.divId;
    var id = this.options.id;
    var elementId = this.options.elementId;
    var theme = this.options.theme;

    var PictureBoxObject = this;

    var activeImageForWindow = null;

    this.selectParamSave = function (img, selection) {

        imageFilterId = $(img).attr('image-filter');
        areaSelectCollection[imageFilterId].resizeData.selection = selection;

    }

    this.resizeAreaPreview = function (img, selection) {

        imageFilterId = $(img).attr('image-filter');

        if ( $(img).attr('data-is-filtered-image') == "1") {
            $(img).attr('data-is-filtered-image','');
            $(img).parent().children('div').children('img').attr('src',$(img).attr('src'));
        }

        resizeData = areaSelectCollection[imageFilterId].resizeData;

        var imageForScale = $(img).parent().children("div").children('img');

        if (resizeData.originalSize == true) {
            var scaleX = resizeData.width / (selection.width || 1);
            var scaleY = resizeData.height / (selection.height || 1);

            $(imageForScale).css({
                width: Math.round(scaleX * resizeData.originalWidth) + 'px',
                height: Math.round(scaleY * resizeData.originalHeight) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
            });
        } else {
            var scaleX = resizeData.width / (selection.width * (resizeData.originalWidth / 500) || 1);
            var scaleY = resizeData.height / (selection.height * (resizeData.originalWidth / 500) || 1);

            //console.log(scaleY);
            $(imageForScale).css({
                width: Math.round(scaleX * resizeData.originalWidth) + 'px',
                height: Math.round(scaleY * resizeData.originalHeight) + 'px',
                marginLeft: '-' + Math.round(scaleX * (resizeData.originalWidth / 500) * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * (resizeData.originalWidth / 500) * selection.y1) + 'px'
            });
        }
    }

    this.refreshPictureBox = function () {

        $.ajax({
            url: "/pictureBox/default/ajaxLayout",
            data: {
                id: id,
                elementId: elementId,
                theme: theme
            },
            cache: false,
            async: false,
            success: function (html) {

                $("#" + divId).html(html);

                $("#sortable").sortable(
                    {
                        cursor: "move",
                        stop: function (event, ui) {
                            PictureBoxObject.updateSortArray()
                        },
                        cancel: "img.delete-btn,img.fav-btn,img.title-btn,img.all-images-btn"
                    }
                );

                $("img.fav-btn").click(function () {

                    var imageId = $(this).attr('data-id');
                    var favArray = PictureBoxObject.getFavArray();


                    if (favArray[imageId] != undefined) {


                        $.ajax({
                            url: '/pictureBox/default/ajaxDelFav/',
                            cache: false,
                            async: false,
                            data: {
                                pictureId: imageId,
                                elementId: elementId,
                                id: id

                            },
                            success: function (html) {
                                favArray = PictureBoxObject.getFavArray();
                                PictureBoxObject.favRefresh(favArray);
                            }
                        });
                    } else {
                        $.ajax({
                            url: '/pictureBox/default/ajaxAddFav/',
                            cache: false,
                            async: false,
                            data: {
                                pictureId: imageId,
                                elementId: elementId,
                                id: id

                            },
                            success: function (html) {
                                favArray = PictureBoxObject.getFavArray();
                                PictureBoxObject.favRefresh(favArray);
                            }
                        });
                    }


                });

                var favArray = PictureBoxObject.getFavArray();


                PictureBoxObject.favRefresh(favArray);

                $("img.delete-btn").click(function (index, domElement) {

                    domElementForDelete = $(this).parent();
                    if (confirm("Вы уверены, что хотите удалить изображение?"))
                        $.ajax({
                            url: "/pictureBox/default/ajaxDeleteImage",
                            cache: false,
                            async: false,
                            data: {
                                pictureId: $(this).attr("data-id"),
                                elementId: elementId,
                                id: id

                            },
                            success: function (html) {

                                $(domElementForDelete).remove();
                            }
                        });
                });

                $("img.title-btn").click(function (e) {

                    var imageId = $(this).attr('data-id');

                    var data = PictureBoxObject.loadtAltAndTitle(imageId);


                    $('#titleModal #altInput').attr('value',data[imageId]['alt']);
                    $('#titleModal #titleInput').attr('value',data[imageId]['title']);


                    PictureBoxObject.activeImageForWindow = imageId;

                    $('#titleModal').modal('show');
                });

                $('#resizeImgSaveBtn').click(function (index, domElement) {


                    for (resizeObject in areaSelectCollection) {
                        var resizeData = areaSelectCollection[resizeObject].resizeData;

                        if (resizeData.selection == undefined) continue;

                        if (resizeData.originalSize != true) {
                            var scaleX = 500 / resizeData.originalWidth;
                            var scaleY = resizeData.height / (resizeData.selection.height * (resizeData.originalWidth / 500) || 1);
                            // alert('scale Y:'+scaleX+'scale X:'+scaleY);
                            resizeData.selection.x1 = Math.round(resizeData.selection.x1 / scaleX);
                            resizeData.selection.y1 = Math.round(resizeData.selection.y1 / scaleX);


                            resizeData.selection.width = Math.round(resizeData.selection.width / scaleX);
                            resizeData.selection.height = Math.round(resizeData.selection.height / scaleX);

                        }
                        //alert(resizeData.selection.x1+' '+resizeData.selection.y1+' '+resizeData.selection.width+' '+resizeData.selection.height+' ');
                        $.ajax(
                            {
                                data:{
                                    id:resizeData.pb_id,
                                    elementId:resizeData.pb_element_id,
                                    pictureId:resizeData.pb_picture_id,
                                    filterName:resizeData.pb_filter_name,
                                    x:resizeData.selection.x1,
                                    y:resizeData.selection.y1,
                                    width:resizeData.selection.width,
                                    height:resizeData.selection.height

                       //'/x/'+resizeData.selection.x1+'/y/'+resizeData.selection.y1+'/width/'+resizeData.selection.width+'/height/'+resizeData.selection.height

                                },
                                url: '/pictureBox/default/ajaxMakeFilteredImage',
                                success: function () {
                                }
                            })
                    }

                    $('#imageModal').modal('hide');

                });

                $("img.all-images-btn").click(function (index, domElement) {

                    $.ajax({
                        url: "/pictureBox/default/ajaxLayout",
                        cache: false,
                        async: false,
                        data: {
                            theme: 'tileImagesRisize',
                            pictureId: $(this).attr("data-id"),
                            elementId: elementId,
                            id: id

                        },
                        success: function (html) {
                            //htmlResult = alert(html);

                            jQuery('#imageModal .modal-body').html(html)

                        }
                    });

                    jQuery('#imageModal').modal({'show': true});

                    //Удаляем все объекты, иначе после закрытия окна они останутся висеть в воздухе
                    $('#imageModal').on('hidden.bs.modal', function (e) {
                        for (imageFilter in areaSelectCollection) {
                            areaSelectCollection[imageFilter]["imageAraeSelectInstance"].remove();
                        }
                    })

                    $(".ladybug_ant").each(function () {

                        filterWidth = $(this).attr('filter-width');
                        filterHeight = $(this).attr('filter-height');
                        var imgAreaObject = $(this).imgAreaSelect({
                            aspectRatio: filterWidth + ":" + filterHeight,
                            handles: true,
                            instance: true,
                            onSelectChange: PictureBoxObject.resizeAreaPreview,
                            onSelectEnd: PictureBoxObject.selectParamSave
                        });

                        var imageFilter = $(this).attr('image-filter');

                        areaSelectCollection["" + imageFilter] = {};

                        areaSelectCollection["" + imageFilter]["imageAraeSelectInstance"] = imgAreaObject;

                        pb_id = $(this).attr('pb-id');
                        pb_element_id = $(this).attr('pb-element-id');
                        pb_picture_id = $(this).attr('pb-picture-id');
                        pb_filter_name = $(this).attr('image-filter');

                        var resizeData = {
                            sourceImg: null,
                            width: 1,
                            height: 2,
                            originalWidth: 0,
                            originalHeight: 0,
                            originalSize: false,
                            selection: null,
                            pb_id: pb_id,
                            pb_element_id: pb_element_id,
                            pb_picture_id: pb_picture_id,
                            pb_filter_name: pb_filter_name
                        };

                        resizeData.sourceImg = $(this).attr("src");

                        href = resizeData.sourceImg;

                        img = new Image();


                        var $originalImage = this;
                        img.onload = function () {

                            resizeData.originalWidth = this.width;
                            resizeData.originalHeight = this.height;

                            if (img.width > 500) {
                                resizeData.originalSize = false;
                                $($originalImage).css('width', '500px');
                            } else {
                                resizeData.originalSize = true;
                                $($originalImage).css('width', 'auto');
                            }

                        }


                        img.src = href;


                        resizeData.width = filterWidth;
                        resizeData.height = filterHeight;


                        areaSelectCollection["" + imageFilter]["resizeData"] = resizeData;
                        //console.log(resizeData);

                    });


                });

                $("#altTitleSaveBtn").click(function (index, domElement) {

                    var imageId = PictureBoxObject.activeImageForWindow
                    PictureBoxObject.saveAltAndTitle(imageId);
                    $('#titleModal').modal('hide');
                });


            },
            error: function (param, param1, param2) {
                alert(param.responseText);
            }
        });


    }

    this.saveAltAndTitle = function (imageId) {

        var title = $('#titleInput').val();
        var alt = $('#altInput').val();


        $.ajax({
            url: "/pictureBox/default/ajaxSetTitle",
            cache: false,
            async: false,
            data: {
                pictureId: imageId,
                elementId: elementId,
                id: id,
                alt: alt,
                title: title

            },
            success: function (html) {


            }
        });
    }

    this.loadtAltAndTitle = function (imageId) {
        var json;
        $.ajax({
            url: "/pictureBox/default/ajaxGetAltArray",
            cache: false,
            async: false,
            data: {

                elementId: elementId,
                id: id

            },
            success: function (html) {

                json = $.parseJSON(html);
            }
        });

        return json;
    }

    this.getFavArray = function () {
        var json;
        $.ajax({
            url: "/pictureBox/default/ajaxGetFavArray",
            cache: false,
            async: false,
            data: {

                elementId: elementId,
                id: id

            },
            success: function (html) {

                json = $.parseJSON(html);
            }
        });

        return json;
    }

    this.favRefresh = function favRefresh(data) {
        $("img.fav-btn").each(function (indx, element) {
            if (data[$(element).attr("data-id")] == undefined) {
                $(element).attr("src", "/protected/modules/pictureBox/assets/images-tiles/star-grey.png");
            } else {
                $(element).attr("src", "/protected/modules/pictureBox/assets/images-tiles/star-yellow.png")
            }
        });

    }

    this.updateSortArray = function () {
        var sortArray = Object();
        var sortIndex = 0;
        $("div#" + divId + " ul.tiles img.tile-img").each(
            function (index, domElement) {

                sortArray[$(domElement).attr("data-id") + ""] = sortIndex;
                sortIndex++;

            });

        $.ajax({
            url: "/pictureBox/default/newSortOrder",
            data: {
                sort: sortArray,
                galleryId: id,
                id: elementId

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


    var myDropzone = Object();

    var favArray = PictureBoxObject.getFavArray();


    PictureBoxObject.favRefresh(favArray);

    myDropzone.$divId = new Dropzone("#dropzone_" + divId,
        {
            url: '/pictureBox/default/upload',
            acceptedFiles: 'image/*',
            paramName: 'Filedata',
            success: function () {
                var state;
                PictureBoxObject.refreshPictureBox();

            },
            params: {
                id: id,
                elementId: elementId,
                config: $config
            }
        });


}

