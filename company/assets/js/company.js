$(document).ready(function () {

    $('.empToDepartInput').click(function () {
        console.log($(this).attr('empId'));
        console.log($(this).attr('depId'));
        $.ajax({
                url: '/company/companyEmployee/ajaxEmpToDep',
                data:{
                    empId:$(this).attr('empId'),
                    depId:$(this).attr('depId')
                }
            }
        );
    });

});