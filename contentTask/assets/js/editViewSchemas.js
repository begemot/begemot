angular.module('performApp').constant('viewSchema', {
    //основной массив флагов видимости, его сливаем с остальныи
    base:{
        btnSave: false,

        panelBtnReview: false,

        btnSendForReview: false,
        pageBase: false,
        pageCurrent: false,
        pageReview: false,
        pageImages:false,
        infoReview: false,
        renderDiv:false
    },


    editor:{
        new:{
            pageBase: true,
            pageCurrent: true,
            btnSave: true,
            pageImages:true,
            renderDiv:true,

        },
        edit:{
            pageBase: true,
            pageCurrent: true,
            btnSave: true,
            pageImages:true,
            btnSendForReview: true,
            renderDiv:true
        },
        review:{
            renderDiv:false,
            dataDiv:true,
            pageImages:false,
        },
        mistake:{
            pageBase: true,
            pageCurrent: true,
            btnSave: true,
            pageImages:true,
            btnSendForReview: true,
            renderDiv:true,
            dataDiv:false,
            pageReview:true,
        },
        done:{}
    },
    admin:{
        new:{},
        edit:{},
        review:{
            dataDiv:false,
            pageBase: true,
            pageCurrent: true,
            panelBtnReview: true,
            renderDiv:true,
            pageReview:true,
            reviewEdit:true,
            pageImages:true,
        },
        mistake:{
            panelBtnReview: false,
            // btnSave: false,
            // btnSendForReview: false,
        },
        done:{
            dataDiv:false,
            pageBase: true,
            pageCurrent: true,
            panelBtnReview: true,
            renderDiv:true,
            pageReview:true,
            reviewEdit:true,
            pageImages:true,
            btnSave: true,
        }
    }

})