(function ($) {

    var defaults = {
        loadingImgPath: "/admin/images/loading_big.gif",
        imgHeight: "228",
        imgWidth: "228",
        overlayOpacity: 0.6,
        fadeInSpeed: 0
    };

    var methods = {
        init: function (options) {

            defaults = $.extend(defaults, options);
        },
        show: function (options) {
       
            var settings ={};
            //Deep Copy
            $.extend(true,settings,defaults);
            $.extend(settings, options);

            $("<div id='simple_loading_overlay'></div>").appendTo("body").css(
            {
                "background": "#B1C3D6",
                "position": "fixed",
                "z-index": 10000,
                "left": 0,
                "top": 0,
                "height": "100%",
                "width": "100%",
                "filter": "alpha(opacity=60)",
                "opacity": 0.6,
                "display": "none"
            }).fadeTo(settings.fadeInSpeed, settings.overlayOpacity);

            $("<img id='simple_loading_img' src='" + settings.loadingImgPath + "' />").appendTo("body").css(
            {
                "width": settings.imgWidth,
                "height": settings.imgHeight,
                "margin-left": (settings.imgWidth / 2 * -1) + "px",
                "margin-top": (settings.imgHeight / 2 * -1) + "px",
                "position": "fixed",
                "left": "50%",
                "top": "50%",
                "z-index": 10001
            }).show();

        },
        hide: function () {
            $("#simple_loading_img").hide().remove();
            $("#simple_loading_overlay").remove();
        }
    };

    $.fn.simpleLoading = function (method) {

        /* 定義済みのメソッドが指定された場合 */
        if (methods[method]) {

            //メソッド名の他に引数が渡された場合は1つだけ取り出してメソッドに渡す
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));

        }
        /* ①引数にオブジェクトが指定された場合は初期設定のためのオプションが渡されたとみなす 
        * ②メソッド名が空の場合は初期化処理を実行
        */
        else if (typeof method === 'object' || !method) {

            return methods.init.apply(this, arguments);

        }
        /* 定義されていないメソッドが指定された場合はエラー */
        else {

            $.error('Method ' + method + ' does not exist');
        }
    };

})(jQuery);