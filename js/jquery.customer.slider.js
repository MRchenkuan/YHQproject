//---------其他属性的渐变方法----------//
(function($){
    $.fn.proGrad = function(pro,from,to,speed) {
        var $this = $(this);
        if (pro == 'grayscale') {

            return $this
        }
    }
})(jQuery);

//---------实现首页slider插件-------//
(function (w, $) {
    var defaults = {
        'auto': false,
        'speed': 500,
        'duration': 1000,
        'layers': 2,
        'decreaseX': 0.15,
        'decreaseY': 0.15,
        'offset':.5,
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
                var width = opt.width * frameWidth * (Math.pow(1 - opt.decreaseX, i));
                var height = opt.height * frameHeight * (Math.pow(1 - opt.decreaseY, i));
                var top = (1 - opt.height * (Math.pow(1 - opt.decreaseY, i))) * frameHeight / 2;
                $([leftDiv,rightDiv]).each(function(){
                    this.css({
                        'z-index': opt.zindex - i*1000,// i*1000 是为了解决层差太小导致的闪烁现象
                        'opacity':i<opt.layers?1:0, // 如果当前层级小于最大层级,则隐藏
                        // 当前div的大小和高度
                        'width': width,
                        'height': height,
                        'top': top,
                        // 当前div的灰度
                        '-webkit-filter': 'grayscale(100%)',
                        '-moz-filter': 'grayscale(100%)',
                        '-ms-filter': 'grayscale(100%)',
                        '-o-filter': 'grayscale(100%)',
                        'filter': 'grayscale(100%)'
                    });
                });
                // 当前div的位置
                // 按照固定比率递减
                // leftDiv.css({'left': (opt.width/2 - opt.decreaseX * (1 - Math.pow(opt.decreaseX, i))) * frameWidth});
                // rightDiv.css({'left': frameWidth - (opt.width/2 - opt.decreaseX * (1 - Math.pow(opt.decreaseX, i))) * frameWidth - opt.width * frameWidth * (Math.pow(1 - opt.decreaseX, i))});

                // 按照左右对齐
                leftDiv.css({
                    'left': 0
                });
                rightDiv.css({
                    'left': frameWidth-width
                });
            }
        }

    };

    $.fn.buildSlider = function () {
        var $this =$(this);
        frameWidth = $this.width();
        frameHeight = $this.height();
        opt = $.extend(defaults, arguments[0] || '');
        $contentDivs = $this.find('div');
        divCount = $contentDivs.length;
        // 框架设置为相对位置,容器为绝对位置
        $this.css({
            //'position': 'abso'
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

//---------实现介绍页小slider插件-------//
(function (w,$) {
    var defaults = {
        autoplay:true
    };
    var timer_1;

    $.fn.buildSmallSlider = function () {
        var $this = $(this);
        var frameHeight = $this.height();
        var frameWidth = $this.width();
        var pices = $this.find("div");
        var nowPice = 0;

        // 框架样式
        $this.css({
            overflow:"hidden"
        });

        // 轮播图样式
        pices.each(function(index,ele){
            $(ele).css({
                width:frameWidth,
                height:frameHeight,
                position:"absolute",
                left:index * frameWidth,
                top:0
            });
        });

        // 自动播放
        timer_1 = setInterval(function () {
            if(!defaults.autoplay){
                clearInterval(timer_1);
                return;
            }
            nowPice++;
            if(nowPice>=pices.length){
                nowPice=0;
            }
            $this.animate({
                scrollLeft:nowPice*frameWidth
            },500);
        },2000)
    }
})(window, jQuery);
