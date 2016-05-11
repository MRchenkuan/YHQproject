//---------其他属性的渐变方法----------//
(function($){
    $.fn.proGrad = function(pro,from,to,speed) {
        var $this = $(this);
        if (pro == 'grayscale') {

            return $this
        }
    }
})(jQuery);

//---------实现自定义slider插件-------//
(function (w, $) {
    var defaults = {
        'auto': false,
        'speed': 500,
        'duration': 1000,
        'layers': 2,
        'decreaseX': 0.4,
        'decreaseY': 0.4,
        'width': 0.6,
        'height': 0.85,
        'zindex': 9999,
        'grascale': 1
    };

    var frameWidth, frameHeight, $contentDivs, opt, divCount, currentIndex = 0, currentDiv, timer;

    var methods = {

        display: function (order) {
            var currentDiv = $contentDivs.eq(order);
            currentDiv.css({
                'z-index': opt.zindex,
                'opacity': 1,
                // 当前div的位置
                'width': frameWidth * opt.width,
                'height': frameHeight * opt.height,
                'left': (frameWidth * (1 - opt.width)) / 2,
                'top': (frameHeight * (1 - opt.height)) / 2,
                // 当前div的灰度
                '-webkit-filter': 'grayscale(0)',
                '-moz-filter': 'grayscale(0)',
                '-ms-filter': 'grayscale(0)',
                '-o-filter': 'grayscale(0)',
                'filter': 'grayscale(0)'
            });

            for (var i = 1; i < (divCount + 1) / 2; i++) {
                //第二层开始容器的样式
                var leftIndex = (currentIndex - i) >= 0 ? (currentIndex - i) : (divCount - Math.abs(currentIndex - i));
                var rightIndex = ((currentIndex + i + 1) < (divCount - 1)) ? (currentIndex + i) : (currentIndex + i - divCount);
                var leftDiv = $contentDivs.eq(leftIndex);
                var rightDiv = $contentDivs.eq(rightIndex);
                $([leftDiv,rightDiv]).each(function(){
                    this.css({
                        'z-index': opt.zindex - i*1000,// i*1000 是为了解决层差太小导致的闪烁现象
                        'opacity':i<opt.layers?1:0, // 如果当前层级小于最大层级,则隐藏
                        // 当前div的大小和高度
                        'width': opt.width * frameWidth * (Math.pow(1 - opt.decreaseX, i)),
                        'height': opt.height * frameHeight * (Math.pow(1 - opt.decreaseY, i)),
                        'top': (1 - opt.height * (Math.pow(1 - opt.decreaseY, i))) * frameHeight / 2,
                        // 当前div的灰度
                        '-webkit-filter': 'grayscale(100%)',
                        '-moz-filter': 'grayscale(100%)',
                        '-ms-filter': 'grayscale(100%)',
                        '-o-filter': 'grayscale(100%)',
                        'filter': 'grayscale(100%)'
                    });
                });
                // 当前div的位置
                // 按照0.2 的比率递减
                leftDiv.css({'left': (opt.width/2 - opt.decreaseX * (1 - Math.pow(opt.decreaseX, i))) * frameWidth});
                rightDiv.css({'left': frameWidth - (opt.width/2 - opt.decreaseX * (1 - Math.pow(opt.decreaseX, i))) * frameWidth - opt.width * frameWidth * (Math.pow(1 - opt.decreaseX, i))});
            }
        }

    };

    $.fn.buildSlider = function () {
        frameWidth = $(this).width();
        frameHeight = $(this).height();
        opt = $.extend(defaults, arguments[0] || '');
        var frame = this;
        var $frame = $(frame);
        $contentDivs = $frame.find('div');
        divCount = $contentDivs.length;
        // 框架设置为相对位置,容器为绝对位置
        $frame.css({
            'position': 'relative'
        });
        $contentDivs.css({
            'position': 'absolute'
        });

        var currentIndex = 0;
        // 首次打开页面直接展示
        methods.display(currentIndex);
        return $(this)

    };

    $.fn.slidernext = function () {
        currentIndex++;
        if (currentIndex > divCount - 1)currentIndex = 0;
        // 顶部图像容器的样式
        methods.display(currentIndex);

    };

    $.fn.sliderprev = function () {

    };

    $.fn.sliderstop = function () {

    };

    $.fn.sliderauto = function () {

    }
})(window, jQuery);