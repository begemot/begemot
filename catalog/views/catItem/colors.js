$(document).ready (function(){

    $('.colorDeleteBtn').click(function(){

        if (confirm("Вы удалите этот цвет из ВСЕХ моделей! Если вы хотите убрать этот цвет только в этой модели воспользуйтесь полем ")) {
            $this = this;
            var $colorId = $(this).parent().parent().attr('data-color-id');

            $.ajax({
                url: '/catalog/catItem/deleteColor',
                data:{
                    colorId:$colorId
                },

                success: function(data) {
                    $($this).parent().parent().remove();
                }
            });
        }
    });



    $('.colorTd').click(function(){
        $color = $(this).css("backgroundColor");
        $('#cp11').colorpicker('setValue', $color);

        var activeColorId = $(this).parent().attr('data-color-id');

        $('#cp11').attr('data-active-color-id',activeColorId);
        $('#colorPickerModal').modal({show:true});


    });



    $('#colorPickerModal').on ('hide.bs.modal',function(){
        color = $("#cp11").data('colorpicker').color.toHex();

        activeColorId = $("#cp11").attr('data-active-color-id');

        $('[data-color-id = "'+activeColorId+'"]').children('.colorTd').css('backgroundColor',color);

        $.ajax({
            url: '/catalog/catItem/setColor',
            data:{
                colorId:activeColorId,
                colorCode:color
            },

            success: function(data) {
             alert("Цвет успешно изменен!");
            }
        });

    })

    $('#cp2,#cp11').colorpicker({
        sliders: {
            saturation: {
                maxLeft: 200,
                maxTop: 200
            },
            hue: {
                maxTop: 200
            },
            alpha: {
                maxTop: 200
            }
        }
    });
});
