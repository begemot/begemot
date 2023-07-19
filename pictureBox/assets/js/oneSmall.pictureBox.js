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

            },
            error: function (param, param1, param2) {
                alert(param.responseText);
            }
        });


    }

    //Dropzone.autoDiscover = false;
    var myDropzone = Object();


     console.log("#dropzone_" + divId+' .previewDropzone');
    //$("#dropzone_" + divId+" + .startBtn").click(function(){
    //    alert(123);
    //});
    myDropzone.$divId = new Dropzone("#dropzone_" + divId,
        {
            url: '/pictureBox/default/upload',
            acceptedFiles: 'image/*',
            paramName: 'Filedata',
            //maxFiles:1,
            //parallelUploads: 50,
            //uploadMultiple: true,
            clickable:"#dropzone_" + divId+' + .startBtn',
            init:function(){

            },
            //previewsContainer:"#dropzone_" + divId+' .dropzone-previews',
            //autoProcessQueue:false,
            success: function () {
                var state;
                PictureBoxObject.refreshPictureBox();

            },
            params: {
                id: id,
                elementId: elementId,
                config: $config,
                mode:"killEmAll"
            }
        });


    //myDropzone.$divId.on("addedfile", function(file) {
    //
    //    console.log("добавили файл");
    //    if (!file.type.match(/image.*/)) {
    //        if(file.type.match(/application.zip/)){
    //            myDropzone.emit("thumbnail", file, "path/to/img");
    //        } else {
    //            myDropzone.emit("thumbnail", file, "path/to/img");
    //        }
    //    }
    //});
    //
    //myDropzone.$divId.on ('success',function(e){
    //    console.log("success");
    //    //PictureBoxObject.refreshPictureBox();
    //})
    //myDropzone.$divId.on ('sending',function(e){
    //    console.log("sending");
    //
    //})
    //myDropzone.$divId.on("complete", function(file) {
    //    console.log("закончили");
    //    myDropzone.$divId.removeFile(file);
    //});
}

