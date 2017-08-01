/**
 *
 * @param bobberSelector Селектор эелемента, который будет перемещаться в определенной области. Будем называть его поплавком
 *
 * @param waterSelector Селектор элемента, который является верхним и нижним ограничением для элемента поплавка, как толща воды для
 * реального поплавка. Будем называь его водой. Верхний предел соответственно поверхность водной глади. Нижний - дно.
 */
function makeBobber(params) {

    var settings = {
        bobberSelector:'',
        waterSelector:'',
        bobberMarginTop:0
    };

    $.extend(settings,params);

    console.log('Создать!');

    var bobberElem = $(settings.bobberSelector);
    console.log('Зашли в makeBobber, селектор ' + settings.bobberSelector);


    var waterTop = $(settings.waterSelector).position().top;
    var waterLeft = $(settings.waterSelector).position().left;
    var waterWidth = $(settings.waterSelector).width();
    var waterHeight = $(settings.waterSelector).height();

    var bobberPositionLeft = waterLeft+waterWidth;
    var bobberPositionTop = waterTop;
    var bobberHeight = $(bobberElem).height();



    //Выставляем поплавок на уровень воды

    $(bobberElem).css('position', 'absolute');
    $(bobberElem).css('top',bobberPositionTop);
    $(bobberElem).css('left',bobberPositionLeft);
    if (waterHeight<=bobberHeight) return;
    console.log(top);

    //bobberElem.css('width','20px');
    $(window).scroll(function(){
        var scroll = $(window).scrollTop();

        var windowHeight = $(window).height();

        var scrollBottom = scroll+windowHeight;


        var bobberHeight = $(bobberElem).height();

        var fixedPositionFlag = false;



        console.log(' scroll - '+scroll+'\r\n'+
            ' windowHeight - '+windowHeight+'\r\n'+
            ' bobberPositionTop+bobberHeight - '+(bobberPositionTop+bobberHeight)+'\r\n'+
            ' bobberPositionTop - '+(bobberPositionTop)+'\r\n'+
            ' scroll+ bobberHeight - '+(scroll+ bobberHeight)+'\r\n'+
            ' waterTop+waterHeight - '+(waterTop+waterHeight)+'\r\n'
        );
        if (scroll+settings.bobberMarginTop<waterTop){
            moveBobberToWaterTop();
        }
        else if (scroll+ windowHeight > waterTop+waterHeight){
            if(bobberHeight>windowHeight){
            moveBobberToWaterBottom();
            } else {
                if (scroll+settings.bobberMarginTop+bobberHeight<waterTop+waterHeight) {
                    moveBobberToWindowTop();
                } else {
                    moveBobberToWaterBottom();
                }
            };
        } else {
            console.log('Таскаем');

            if (scroll+windowHeight > bobberPositionTop+bobberHeight){
                console.log('тащим вниз');
                moveBobberToWindowBottom();
            }

            if (scroll+settings.bobberMarginTop<bobberPositionTop){
                console.log('тащим вверх');
                moveBobberToWindowTop();
            }


        }
        //console.log((windowHeight)+ ' '+ waterTop);

        function moveBobberToWindowTop(){
            bobberPositionTop = scroll+settings.bobberMarginTop;
            $(bobberElem).css('top',bobberPositionTop);

        }

        function moveBobberToWindowBottom(){
            bobberPositionTop = scroll+windowHeight - bobberHeight
            $(bobberElem).css('top',bobberPositionTop);
        }
        function moveBobberToWaterTop(){$(bobberElem).css('top',waterTop);}
        function moveBobberToWaterBottom(){$(bobberElem).css('top',waterTop+waterHeight-bobberHeight);}


    });

}



