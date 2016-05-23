//<!--板块滑动插件-->
;(function ($, w) {
    $.fn.moveDownOut = function (speed) {
        var target = $(this);
        target.css('position', 'absolute');
        target.animate({top: w.innerHeight - target.height() / 2}, speed, 'swing').dequeue().fadeOut(speed/2);
        return target
    };
    $.fn.moveDownIn = function (speed) {
        var target = $(this);
        // 先定位到起始位置
        target.css({'position': 'absolute', 'top': '-' + target.height() / 2 + 'px'});
        // 动画到目标位置
        target.animate({top: 0}, speed, 'easeOutExpo').dequeue().fadeIn(speed/2);
        return target
    };
})(jQuery, window);