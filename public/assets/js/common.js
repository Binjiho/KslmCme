$(function (e) {
    $width = $(window).innerWidth(),
        wWidth = windowWidth();

    $(document).ready(function (e) {
        btnTop();
        fileUpload();
        datepicker();
        popup();

        noticeRolling();
        boardRolling();
        tabMenu();

        resEvt();

    });

    // resize
    function resEvt() {
        conHeight();

        if (wWidth < 1025) {
            mGnb();
            if($('.js-gnb').hasClass('mobile')){
                $('html, body').addClass('ovh');
            }
        } else {
            // if($('.js-gnb').hasClass('mobile')){
            //     $('html, body').removeClass('ovh');
            // }
            gnb();
            tabMenu();
            if($('.js-dim').hasClass('mobile')){
                $('.js-dim').hide();
                $('html, body').removeClass('ovh');
            }
            $('.js-gnb > li > ul, .js-sub-menu-list ul').removeAttr('style');
            // $('.js-gnb > li').removeClass('on');
            $('.js-tab-menu, .js-tabcon-menu').removeAttr('style');
            $('.js-btn-tab-menu, .js-btn-tabcon-menu').removeClass('on');
        }

        if(wWidth < 769){
            touchHelp();
        }else{
        }
    }

    $(window).resize(function (e) {
        $width = $(window).innerWidth(),
            wWidth = windowWidth();
        resEvt();
    });

    $(window).scroll(function(e){
        if($(this).scrollTop() > 200){
            $('.js-btn-top').addClass('on');
        }else{
            $('.js-btn-top').removeClass('on');
        }
    });
});

function Mobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

function windowWidth() {
    if ($(document).innerHeight() > $(window).innerHeight()) {
        if (Mobile()) {
            return $(window).innerWidth();
        } else {
            return $(window).innerWidth() + 17;
        }
    } else {
        return $(window).innerWidth();
    }
}

function conHeight(){
    $(document).ready(function(e){
        var conHeight = $(window).outerHeight() - $('.js-header').outerHeight() - $('#footer').outerHeight();
        setTimeout(function(e){
            $('#container').css('min-height',conHeight);
        },100);
    });
}

function subConHeight(){
    $(document).ready(function(e){
        var subConHeight = $(window).outerHeight() - $('.js-header').outerHeight() - $('#footer').outerHeight();
        setTimeout(function(e){
            $('.sub-contents').css('min-height',subConHeight);
        },100);
    });
}


function gnb() {
    var max_h = 0;
    $('.js-gnb > li').each(function(e){
        var h = parseInt($(this).children('ul').outerHeight());
        if(max_h < h){
            max_h = h;
        }
    });
    $('.js-gnb > li > ul').height(max_h);
    $('.js-gnb > li > ul').css('height','100px');
    $('.js-gnb > li > a').off('click');
    $('.js-gnb > li').on('mouseenter',function(e){
        $('.js-gnb-bg, .js-gnb > li > ul').css('height',max_h);
        $('.js-gnb > li > ul').show();
    });
    $('.js-gnb').on('mouseleave', function(e){
        $('.js-gnb > li > ul').hide();
        $('.js-gnb-bg').css('height','');
    });
}

function mGnb() {
    // $('.js-btn-menu-open').on('click',function(e){
    //     $('html, body').addClass('ovh');
    //     $('.js-gnb').addClass('mobile').stop().animate({'right':0},400);
    // });
    // $('.js-btn-menu-close').on('click',function(e){
    //     $('html, body').removeClass('ovh');
    //     $('.js-gnb').removeClass('mobile').stop().animate({'right':'-100%'},400);
    // });
    // $('.js-gnb > li').off('mouseenter');
    // $('.js-gnb').off('mouseleave');

    $('.js-gnb > li').off('mouseenter');
    $('.js-gnb').off('mouseleave');
    $('.js-gnb > li > a').off().on('click',function(e){
        if($(this).next('ul').length){
            $(this).parent('li').toggleClass('on');
            $('.js-gnb > li > a').not(this).parent('li').removeClass('on');
            $(this).next('ul').stop().slideToggle();
            $('.js-gnb > li > a').not(this).next('ul').stop().slideUp();
            return false;
        }
    });

    $('.js-btn-menu-open').on('click',function(e){
        $('html, body').addClass('ovh');
        $('.js-dim').addClass('mobile').stop().fadeIn(100);
        $('#gnb').stop().animate({'right':0},400);
    });
    $('.js-btn-menu-close, .js-dim').on('click',function(e){
        $('html, body').removeClass('ovh');
        $('.js-dim').removeClass('mobile').stop().fadeOut(100);
        $('#gnb').stop().animate({'right':'-100%'},400);
    });
}

function noticeRolling(){
    $('.js-notice-rolling').not('.slick-initialized').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 1000,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        vertical: true,
        responsive: [
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
}

function boardRolling(){
    $('.js-lib-rolling').not('.slick-initialized').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 1000,
        infinite: true,
        slidesToShow: 2,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
    $('.js-board-rolling').not('.slick-initialized').slick({
        dots: false,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 3000,
        speed: 1000,
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
}

function tabMenu(){
    $('.js-tab-menu > li').on('click',function(e){
        var cnt = $(this).index();
        $(this).addClass('on');
        $('.js-tab-con').hide().eq(cnt).stop().fadeIn();
        $(this).siblings().removeClass('on');
        $('.js-lib-rolling').slick('setPosition');
    });
}

function btnTop(){
    $('.js-btn-top').on('click',function(e){
        $('html, body').stop().animate({'scrollTop':0},400);
        return false;
    });
}

function touchHelp(){
    $('.scroll-x').each(function(e){
        if($(this).height() < 180){
            $(this).addClass('small');
        }
        $(this).scroll(function(e){
            $(this).removeClass('touch-help');
        });
    });
}

function fileUpload(option=null){
    $('.file-upload').each(function(e){
        $(this).parent().find('.upload-name').attr('readonly','readonly');
        $(this).on('change',function(){
            var fileName = $(this).val();
            $(this).parent().find('.upload-name').val(fileName);
        });
    });
}

function datepicker(){
    if($('.datepicker').length){
        $('.datepicker').datepicker({
            dateFormat : "yy-mm-dd",
            dayNamesMin : ["월", "화", "수", "목", "금", "토", "일"],
            monthNamesShort : ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"],
            showMonthAfterYear: true,
            changeMonth : true,
            changeYear : true
        });
    }
}

function imgMap(){
    $('img[usemap]').each(function(e){
        $('img[usemap]').rwdImageMaps();
    });
}

function scrollFixed(){
    $('.js-scroll-fixed').each(function(e){
        var fixedPosition = $('.js-scroll-fixed').offset().top;
        $(window).scroll(function(e){
            if($(window).scrollTop() + fixedPosition > $(document).height()) {
                $('.js-scroll-fixed').addClass('fixed bottom');
            }else if(fixedPosition < $(window).scrollTop()){
                $('.js-scroll-fixed').removeClass('bottom');
                $('.js-scroll-fixed').addClass('fixed');
            }else{
                $('.js-scroll-fixed').removeClass('fixed bottom');
            }
        });
    });
}

function popup(){
    $('.js-pop-open').on('click',function(e){
        var popCnt = $(this).attr('href');
        $('html, body').addClass('ovh');
        $('.popup-wrap'+popCnt+'').stop().fadeIn(200);
        $('.popup-wrap').scrollTop(0);
        return false;
    });
    $('.js-pop-close').on('click',function(e){
        $('html, body').removeClass('ovh');
        $(this).parents('.popup-wrap').stop().fadeOut(100);
        return false;
    });
    $('.popup-wrap#pop-privacy, .popup-wrap#pop-email').off().on('click', function (e){
        if ($('.popup-contents').has(e.target).length == 0){
            $('html, body').removeClass('ovh');
            $('.popup-wrap').stop().fadeOut(100);
        }
    });
}