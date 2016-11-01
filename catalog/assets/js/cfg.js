$(document).ready(function () {

    $('.pictureBase').click(function () {
        var src = $(this).attr('data-image');
        $('#mainPicture img').attr('src',src);

    });

    $('.pictureOption').click(function () {
        var src = $(this).attr('data-image');
        html = '<img class="option" src="'+src+'"/>';
        $('#optionPicture').html(html);
    });

});

