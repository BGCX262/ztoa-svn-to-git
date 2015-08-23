/*
 *当前用到的CSS在 ../Styles/Page.css文件里

 *IsContinueSelect：是否启用连续选中功能
 *IsSelectRowEnable:选中行时，是否使用选中效果
 */
(function ($) {
    $.fn.tableUI = function (options) {
        var defaults = {
            activeRowClass: "GridRowHover",
            selectRowClass: "GridRowSelect",
            IsSelectRowEnable: "on",
            IsContinueSelect: "off"
        }
        var options = $.extend(defaults, options);

        function SetRowBg(tableObj) {
            $(tableObj).find("tr").each(function () {
                $(this).removeClass(options.selectRowClass);
            });
        }

        this.each(function () {
            var thisTable = $(this);

            //添加活动行颜色

            $(thisTable).find("tr").bind("mouseover", function () {
                $(this).addClass(options.activeRowClass);
            });

            $(thisTable).find("tr").bind("mouseout", function () {
                $(this).removeClass(options.activeRowClass);
            });

            //点击行的时候

            $(thisTable).find("tr").bind("click", function () {
                var trClass = $(this).attr("class");

                if (options.IsContinueSelect == "off") {
                    $(thisTable).find("tr").each(function () {
                        $(this).removeClass(options.selectRowClass);
                    });
                }
                //alert("IsContinueSelect=" + options.IsContinueSelect + "  trClass=" + trClass + "  trClass.indexOf=" + trClass.indexOf(options.selectRowClass));
                if (options.IsSelectRowEnable == "on") {
                    if (trClass.indexOf(options.selectRowClass) <= -1)
                        $(this).addClass(options.selectRowClass);
                    else
                        $(this).removeClass(options.selectRowClass);
                }
            });
        });
    };
})(jQuery);


/*
*当前用到的CSS在 ../Styles/Page.css文件里
*/
(function ($) {
    $.fn.btnUI = function (options) {
        var defaults = {
             btnCss: "Btn",
            btnHover: "BtnHover",
            btnClick: "BtnClick"
        }
        var options = $.extend(defaults, options);


        this.each(function () {
            $(this).attr("class", options.btnCss);
            $(this).hover(
                function () {
                $(this).addClass(options.btnHover);
                },
                function () {
                $(this).removeClass(options.btnHover);
                }
            );

            $(this).bind("mousedown", function(){
                    $(this).addClass(options.btnClick);
                });
                $(this).bind("mouseup", function(){
                    $(this).removeClass(options.btnClick);
                });


       });  
};
   
})(jQuery);

(function ($) {
    $.fn.buttonUIEnter = function (options) {
        var defaults = {
            keyCode: 13
        }
        var options = $.extend(defaults, options);

        this.each(function () {
            var thisBtn = $(this);
            $("input").keydown(function(){
                if(event.keyCode==13){
                    //alert($(thisBtn).attr("id"));
                    $(thisBtn).click();
                }
            }); 
        });
    };
})(jQuery);

(function ($) {
    $.fn.TextAreaAutoSize = function (options) {
        var defaults = {
            Height: "19"
        }
        var options = $.extend(defaults, options);

        this.each(function () {
            var txtHeight = $(this).get(0).scrollHeight;
           // $(this).attr("class", "formTextarea");
           if (parseFloat(options.Height) < parseFloat(txtHeight))
               $(this).height(txtHeight);
            else
               $(this).height(options.Height);

            //alert('height:'+$(this).height()+'  scrollHeight:'+scrollHeights);
            $(this).bind("keydown keyup", function(event){
                var scrollHeight = $(this).get(0).scrollHeight;
                //var scrollHeight = $(this).height();
                   console.log($(this).get(0));
                    console.log(scrollHeight);
                   console.log(options.Height);
                if (parseFloat(options.Height) < parseFloat(scrollHeight))
                    $(this).height(scrollHeight); 
                else
                    $(this).height(options.Height); 
                });
        });
    };
})(jQuery);

(function ($) {
    $.fn.minusColor = function (options) {
        var defaults = {
            Color: "red",
            Target: "text"
        }
        var options = $.extend(defaults, options);

        this.each(function () {
            $(this).find("td").each(function () {
                var tdValue = "";
                if (options.Target == "text") tdValue=$(this).text();
                if (options.Target == "input") tdValue=$(this).find("input[id*='txt']").val();
                //alert(options.Target+","+tdValue);
                if (tdValue != ""){
                    if (!isNaN(tdValue)) {
                        if (parseFloat(tdValue)<0){
                            if (options.Target == "text") $(this).css("color", options.Color);
                            if (options.Target == "input") $(this).find("input[id*='txt']").css("color", options.Color);
                        }
                    }
                }
            });
        });
    };
})(jQuery);

(function ($) {
    $.fn.textareaAutoHeight = function (options) {
        this._options = {
            minHeight: 0,
            maxHeight: 1000
        };

        this.init = function () {
            for (var p in options) {
                this._options[p] = options[p];
            }
            if (this._options.minHeight == 0) {
                this._options.minHeight=parseFloat($(this).height());
            }
            for (var p in this._options) {
                if ($(this).attr(p) == null) {
                    $(this).attr(p, this._options[p]);
                }
            }
            $(this).keyup(this.resetHeight).change(this.resetHeight)
                .focus(this.resetHeight);
        };
        this.resetHeight = function () {
            var _minHeight = parseFloat($(this).attr("minHeight"));
            var _maxHeight = parseFloat($(this).attr("maxHeight"));

            if (!$.browser.msie) {
                $(this).height(60);
            }else{
                $(this).height(60);
            }

            var h = parseFloat(this.scrollHeight);
            h = h < _minHeight ? _minHeight :
                h > _maxHeight ? _maxHeight : h;
            $(this).height(h).scrollTop(h);
            if (h >= _maxHeight) {
                $(this).css("overflow-y", "scroll");
            }
            else {
                $(this).css("overflow-y", "hidden");
            }
        };
        this.init();
    };
})(jQuery);