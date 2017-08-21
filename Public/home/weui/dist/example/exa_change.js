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
            .focus()//��focus��Ϊ�˴���һ��ҳ�������(reflow or layout thrashing),ʹmask��transition����������������
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


    // .container ������ overflow ����, ���� Android �ֻ���������ȡ����ʱ, ���뷨��ס������ bug
    // ��� issue: https://github.com/weui/weui/issues/15
    // �������:
    // 0. .container ȥ�� overflow ����, ���� demo �»������������
    // 1. �ο� http://stackoverflow.com/questions/23757345/android-does-not-correctly-scroll-on-input-focus-if-not-body-element
    //    Android �ֻ���, input �� textarea Ԫ�ؾ۽�ʱ, ������һ��
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
