/**
 * Created by admin on 2016/9/21.
 */
$(function () {
    // cell
    $('#showTooltips').click( function (){
        $('.js_tooltips').show();
        setTimeout(function (){
            $('.js_tooltips').hide();
        }, 3000);
    });

    // toast
    $('#showToast').click( function () {
                $('#toast').show();
                setTimeout(function () {
                    $('#toast').hide();
                }, 2000);
            });
    $('#showLoadingToast').click( function () {
                $('#loadingToast').show();
                setTimeout(function () {
                    $('#loadingToast').hide();
                }, 2000);
            });

    // dialog
    $('#showDialog1').click( function () {
                $('#dialog1').show().on('click', '.weui_btn_dialog', function () {
                    $('#dialog1').off('click').hide();
                });
            });
    $('#showDialog2').click( function () {
                $('#dialog2').show().on('click', '.weui_btn_dialog', function () {
                    $('#dialog2').off('click').hide();
                });
            });


    // progress

    $('#btnStartProgress').click( function () {
                if ($(this).hasClass('weui_btn_disabled')) {
                    return;
                }

                $(this).addClass('weui_btn_disabled');

                var progress = 0;
                var $progress = $('.js_progress');

                function next() {
                    $progress.css({width: progress + '%'});
                    progress = ++progress % 100;
                    setTimeout(next, 30);
                }

                next();
            });


    // actionsheet
    $('#showActionSheet').click( function () {
        var mask = $('#mask');
        var weuiActionsheet = $('#weui_actionsheet');
        weuiActionsheet.addClass('weui_actionsheet_toggle');
        mask.show()
            .focus()//加focus是为了触发一次页面的重排(reflow or layout thrashing),使mask的transition动画得以正常触发
            .addClass('weui_fade_toggle').one('click', function () {
                hideActionSheet(weuiActionsheet, mask);
            });
        $('#actionsheet_cancel').one('click', function () {
            hideActionSheet(weuiActionsheet, mask);
        });
        mask.unbind('transitionend').unbind('webkitTransitionEnd');

        function hideActionSheet(weuiActionsheet, mask) {
            weuiActionsheet.removeClass('weui_actionsheet_toggle');
            mask.removeClass('weui_fade_toggle');
            mask.on('transitionend', function () {
                mask.hide();
            }).on('webkitTransitionEnd', function () {
                mask.hide();
            })
        }
    });


    // navbar
        $('.weui_navbar_item').click( function () {
            $(this).addClass('weui_bar_item_on').siblings('.weui_bar_item_on').removeClass('weui_bar_item_on');
        });

    // tabbar
    $('.weui_tabbar_item').click( function () {
        $(this).addClass('weui_bar_item_on').siblings('.weui_bar_item_on').removeClass('weui_bar_item_on');
    });

    // searchbar
            $('#search_input').focus( function () {
                var $weuiSearchBar = $('#search_bar');
                $weuiSearchBar.addClass('weui_search_focusing');
            });
            $('#search_input').blur( function () {
                var $weuiSearchBar = $('#search_bar');
                $weuiSearchBar.removeClass('weui_search_focusing');
                if ($(this).val()) {
                    $('#search_text').hide();
                } else {
                    $('#search_text').show();
                }
            });
    $('#container').on('input', '#search_input', function () {
                var $searchShow = $("#search_show");
                if ($(this).val()) {
                    $searchShow.show();
                } else {
                    $searchShow.hide();
                }
            }).on('touchend', '#search_cancel', function () {
                $("#search_show").hide();
                $('#search_input').val('');
            }).on('touchend', '#search_clear', function () {
                $("#search_show").hide();
                $('#search_input').val('');
            });


    //router.push(home)
    //    .push(button)
    //    .push(cell)
    //    .push(toast)
    //    .push(dialog)
    //    .push(progress)
    //    .push(msg)
    //    .push(article)
    //    .push(actionsheet)
    //    .push(icons)
    //    .push(panel)
    //    .push(tab)
    //    .push(navbar)
    //    .push(tabbar)
    //    .push(searchbar)
    //    .setDefault('/')
    //    .init();


    // .container 设置了 overflow 属性, 导致 Android 手机下输入框获取焦点时, 输入法挡住输入框的 bug
    // 相关 issue: https://github.com/weui/weui/issues/15
    // 解决方法:
    // 0. .container 去掉 overflow 属性, 但此 demo 下会引发别的问题
    // 1. 参考 http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
    //    Android 手机下, input 或 textarea 元素聚焦时, 主动滚一把
    if (/Android/gi.test(navigator.userAgent)) {
        window.addEventListener('resize', function () {
            if (document.activeElement.tagName == 'INPUT' || document.activeElement.tagName == 'TEXTAREA') {
                window.setTimeout(function () {
                    document.activeElement.scrollIntoViewIfNeeded();
                }, 0);
            }
        })
    }
});
