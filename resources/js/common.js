'use strict';
//jquery
const jQuery = require('jquery');
// other packages here
window.$ = window.jQuery = jQuery;

//bootstarp-grid
import "../libs/bootstrap/css/bootstrap4-grid.min.css";


//fancybox
import "../libs/fancybox/jquery.fancybox.min.css";
import "../libs/fancybox/jquery.fancybox.min.js";

//iziModal
import "../libs/izimodal/css/iziModal.min.css";
import "../libs/izimodal/js/iziModal.js";

//scrollbar
/* docks/api - https://gromo.github.io/jquery.scrollbar */
import "../libs/scrollbar/jquery.scrollbar.js";
import "../libs/scrollbar/jquery.scrollbar.css";

//imputmask
import "../libs/inputmask/jquery.inputmask.bundle.js";

//swiper slider
import "../libs/swiper/swiper.css";

import Swiper from 'swiper/dist/js/swiper.js';


global.seed = {
    /* init script */
    init: function init() {
        this.initPopup();
        this.closeAllPopup();
        this.initSlider();
        this.initSelect();
        this.initCustomScroll();
        this.initMaskPhone();
        this.initNav();
        this.initQuality();
        this.initCustomTab();
        this.initPassView();
        this.initAccordion();
        this.initButtonSlideTop();
    },
    /* init Popup */
    initPopup: function () {
        if ($('[data-popup="basket"]').length)
            $('[data-popup="basket"]').iziModal({
                width: 410,
                focusInput: false,
                fullscreen: true,
                openFullscreen: true,
                transitionIn: 'fadeInRight',
                transitionOut: 'fadeOutRight'
            });

        if ($('[data-popup="filter"]').length)
            $('[data-popup="filter"]').iziModal({
                width: 410,
                focusInput: false,
                fullscreen: true,
                openFullscreen: true,
                transitionIn: 'fadeInLeft',
                transitionOut: 'fadeOutLeft'
            });

        $('[data-popup="filter"] .wrapper__filter-title h4').on('click', function () {
            $(this).parents('.warpper__filter-item').toggleClass('active');
        });

        if ($('[data-popup="navigation"]').length)
            $('[data-popup="navigation"]').iziModal({
                width: 410,
                focusInput: false,
                fullscreen: true,
                openFullscreen: true,
                transitionIn: 'fadeInLeft',
                transitionOut: 'fadeOutLeft',
                onClosing: function () {
                    $('.btn-nav').removeClass('btn-nav-active');
                }
            });

        $('.box__string-arrow').on('click', function () {
            $(this).parents('li').toggleClass('active');
        });

        $('li[data-popupfilter]').on('click', function () {
            $('[data-popupfilter]').removeClass('active');

            $('[data-popupfilter="' + $(this).attr('data-popupfilter') + '"]').addClass('active');

        });

        if ($('[data-popup="typical"], [data-popup="authorization"], [data-popup="registration"], [data-popup="resetpass"], [data-popup="repeatorder"], [data-popup="manager"], [data-popup="сancellations"]').length)
            $('[data-popup="typical"], [data-popup="authorization"], [data-popup="registration"], [data-popup="resetpass"], [data-popup="repeatorder"], [data-popup="manager"], [data-popup="сancellations"]').iziModal({
                width: 370,
                focusInput: false
            });

        $('body').on('click', '[data-btn-popup]', function () {
            global.seed.closeAllPopup();
            $('[data-popup="' + $(this).data('btn-popup') + '"]').iziModal('open');
        });
    },
    /* close Popup */
    closeAllPopup: function () {

        $('[data-btn-closepopup]').on('click', function () {
            $('[data-popup]').each(function () {
                $(this).iziModal('close');
            });
        });

    },
    /* function open popup on window by name data attibute */
    openPopup: function (elPopup) {
        $('[data-popup="' + elPopup + '"]').iziModal('open');
    },
    /* function close popup on window by name data attibute */
    closePopup: function (elPopup) {
        $('[data-popup="' + elPopup + '"]').iziModal('close');
    },
    initSlider: function () {

        /**
         init slider in home page
         **/
        if ($('.box__slider-mini .swiper-container').length > 0) {

            var galleryTop = new Swiper('.box__slider-mini .swiper-container', {
                autoplay: {
                    delay: 5000,
                },
                effect: 'fade',
                speed: 1000,
                loopedSlides: 1
            });
        }

        /**
         init slider in home page
         **/
        if ($('.box__slider-big .swiper-container').length > 0) {

            var galleryTop = new Swiper('.box__slider-big .swiper-container', {
                loopedSlides: 1,
                loop: true,
                speed: 1000,
                navigation: {
                    nextEl: '.slider-big-next',
                    prevEl: '.slider-big-prev',
                },
                pagination: {
                    el: '.slider-big-pagination',
                    type: 'bullets',
                    clickable: true
                },
            });

        }

        /**
         init slider in about page and other page
         **/
        if ($('.box__slider-productsviewed .swiper-container').length > 0) {

            var galleryTop = new Swiper('.box__slider-productsviewed .swiper-container', {
                spaceBetween: 30,
                slidesPerView: 5,
                loopedSlides: 20,
                loop: true,
                speed: 500,
                navigation: {
                    nextEl: '.slider-productsviewed-next',
                    prevEl: '.slider-productsviewed-prev',
                },
                breakpoints: {
                    767: {
                        slidesPerView: 2,
                    },
                    1199: {
                        slidesPerView: 3,
                    }
                }
            });

        }

        /**
         init slider in about page and other page
         **/
        if ($('.box__slider-oneslides .swiper-container').length > 0) {

            var galleryTop = new Swiper('.box__slider-oneslides .swiper-container', {
                spaceBetween: 30,
                slidesPerView: 1,
                loopedSlides: 20,
                loop: true,
                speed: 500,
                pagination: {
                    el: '.slider-oneslides-pagination',
                    type: 'bullets',
                    clickable: true
                }
            });

        }

        /**
         init slider in about page and other page
         **/
        if ($('.box__brands-slider .swiper-container').length > 0) {

            var galleryTop = new Swiper('.box__brands-slider .swiper-container', {
                spaceBetween: 30,
                slidesPerView: 5,
                loopedSlides: 20,
                loop: true,
                speed: 500,
                navigation: {
                    nextEl: '.slider-brands-next',
                    prevEl: '.slider-brands-prev',
                },
                breakpoints: {
                    767: {
                        slidesPerView: 2,
                    },
                    1199: {
                        slidesPerView: 3,
                    }
                }
            });

        }

    },
    /* init plugin MaskInput for input phone  */
    initMaskPhone: function () {
        $('[data-phone]').each(function () {
            $(this).inputmask("+7 (999) 999-99-99");
        });
    },
    /* init active/deactive click by button navigation mobile  */
    initNav: function () {

        $('.btn-nav').on('click', function () {
            $(this).toggleClass('btn-nav-active');
        });

    },
    /* init function by quality input[type="number"]  */
    initQuality: function initQuality() {
        $('body').on('click', '.box__quality [data-prev-quality]', function () {
            var el = $(this).parents('.box__quality').find('input');
            let amount = (parseInt(el.val()) - parseInt(el.attr('step'))),
            calc = amount - (amount % parseInt(el.attr('step')));

            if (calc >= el.attr('min'))
                el.val(calc);
        }).on('click', '.box__quality [data-next-quality]', function () {
            console.log('update +');
            var el = $(this).parents('.box__quality').find('input');
            let amount = (parseInt(el.val()) + parseInt(el.attr('step'))),
                calc = amount - (amount % parseInt(el.attr('step')));

            el.val(calc);
        }).on('change', '.box__quality input', function () {
            let amount = ($(this).val());

            $(this).val(amount - (amount % parseInt($(this).attr('step'))));
        });
    },
    /* init function by button active/deactive password input[type="password"]  */
    initPassView: function () {

        $('.password-control').each(function () {

            $(this).on('click', function () {

                let el = $(this).parent('label').siblings('input');

                var pwdType = el.attr("type");
                var newType = (pwdType === "password") ? "text" : "password";

                el.attr("type", newType);

            });


        });

    },
    /* init plugin selectric.js for select and option  */
    initSelect: function () {

        $('.box__select select').each(function () {
            $(this).selectric({
                disableOnMobile: false,
                nativeOnMobile: false
            });
        });

    },
    /* init plugin scrollbar.js for div where do you need a scroll bar */
    initCustomScroll: function () {
        $('[data-customscrollbar]').scrollbar({
            duration: 1000
        });
    },
    /* init function by several types of tabs  */
    initCustomTab: function () {

        $('.box__tabs [data-tab]').on('click', function () {
            let el = $(this).attr('data-tab');

            $('[data-tab]').removeClass('active');

            $('.box__tab-content[data-tab="' + el + '"]').addClass('active');
            $('.box__tabs [data-tab="' + el + '"]').addClass('active');
        });

        $('.box__promotionalproducts-tab [data-tab]').on('click', function () {
            let el = $(this).attr('data-tab');

            $('[data-tab]').removeClass('active');

            $('.box__promotionalproducts-content[data-tab="' + el + '"]').addClass('active');
            $('.box__promotionalproducts-tab [data-tab="' + el + '"]').addClass('active');

            $('.wrapper__promotionalproducts-active').removeClass('active-items');

            $('.wrapper__promotionalproducts-active').text($(this).find('button').text());
        });

        $('.btn__currentorder-toggle button').on('click', function () {
            $(this).parents('.box__item').toggleClass('active');
        });

        $('.box__currentorder-warning').on('click', function () {
            $(this).parents('.box__item').toggleClass('active-warning');
        });

        $('.box__characteristics .box__characteristics-status').on('click', function () {
            $(this).parents('.box__characteristics').toggleClass('active');
        });

        $('.box__tab-active, .wrapper__cataloglimit-active, .wrapper__promotionalproducts-active').on('click', function () {
            $(this).toggleClass('active-items');
        })

    },
    /* init function by custom accordion(look example page discount.html) */
    initAccordion: function () {

        $('.box__accodion .box__accodion-item').on('click', function () {
            if (!$(this).hasClass('active')) {
                $('.box__accodion .box__accodion-item').removeClass('active');
                $('.box__accodion .box__content').slideUp(150);
                $(this).addClass('active');
                $(this).find('.box__content').slideDown(300);
            }
        });

    },
    /* init function by custom button animation in header/up */
    initButtonSlideTop: function () {

        $(window).on('scroll', function () {
            if ($(window).scrollTop() > 500) {
                $('.btn-up').fadeIn(400);
            } else {
                $('.btn-up').fadeOut(400);
            }

        });


        $('.btn-up').on('click', function () {
            $('html, body').animate({
                scrollTop: $('header').offset().top
            }, 500);
        });

    },
    removePopup: function () {
        console.log('remove popup');
    }
};

$(document).ready(function () {
    global.seed.init();
});

$(window).on('scroll', function () {

});

$(window).on('load', function () {

});

$(window).resize(function () {

});

$(function () {

    $('body').on('click', 'div[data-popup=authorization] form button[type=submit]', function (e) {
        e.preventDefault();

        let _self = $(this),
            form = _self.closest('form'),
            fd = new FormData($(this).closest('form')[0]);

        $.ajax({
            url: _self.closest('form').attr('action'),
            method: 'POST',
            processData: false,
            contentType: false,
            data: fd,
            success: (result) => {
            },
            beforeSend : function (){
                form.find('.invalid-message').remove()
            },
            statusCode: {
                422: (info) => {
                    form.find('input:not([type=hidden])').each(function () {
                        let input = $(this);
                        if (info.responseJSON.errors[$(this).attr('name')]) {
                            input.addClass('is-invalid')
                        } else {
                            input.addClass('valid')
                        }
                    });

                    form.append($('<span/>', {
                        text: 'Введите корректные данные',
                        class: 'invalid-message'
                    }))
                },
                200: () => {
                    window.location.reload();
                }
            }
        });
    }).on('click', 'div[data-popup=registration] form button', function (e) {
        e.preventDefault();

        let _self = $(this),
            form = _self.closest('form'),
            fd = new FormData($(this).closest('form')[0]);

        $.ajax({
            url: _self.closest('form').attr('action'),
            method: 'POST',
            processData: false,
            contentType: false,
            data: fd,
            success: (result) => {
            },
            beforeSend : function (){
                form.find('.invalid-message').remove()
            },
            statusCode: {
                422: (info) => {
                    form.find('input:not([type=hidden])').each(function () {
                        let input = $(this);
                        if (info.responseJSON.errors[$(this).attr('name')]) {
                            input.addClass('is-invalid')
                        } else {
                            input.addClass('valid')
                        }
                    });

                    form.append($('<span/>', {
                        text: 'Введите корректные данные',
                        class: 'invalid-message'
                    }))
                },
                200: () => {
                    window.location.reload();
                }
            }
        });
    })
})
