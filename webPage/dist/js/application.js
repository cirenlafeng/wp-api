;(function(root, factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory(require('jquery'));
  } else {
    root.jquery_dotdotdot_js = factory(root.jQuery);
  }
}(this, function(jQuery) {
/*
 *	jQuery dotdotdot 3.1.0
 *	@requires jQuery 1.7.0 or later
 *
 *	dotdotdot.frebsite.nl
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 *
 *	License: CC-BY-NC-4.0
 *	http://creativecommons.org/licenses/by-nc/4.0/
 */
!function(t){"use strict";function e(){h=t(window),s={},r={},o={},t.each([s,r,o],function(t,e){e.add=function(t){t=t.split(" ");for(var n=0,i=t.length;n<i;n++)e[t[n]]=e.ddd(t[n])}}),s.ddd=function(t){return"ddd-"+t},s.add("truncated keep text"),r.ddd=function(t){return"ddd-"+t},r.add("text"),o.ddd=function(t){return t+".ddd"},o.add("resize"),e=function(){}}var n="dotdotdot",i="3.1.0";if(!(t[n]&&t[n].version>i)){t[n]=function(t,e){this.$dot=t,this.api=["getInstance","truncate","restore","destroy","watch","unwatch"],this.opts=e;var i=this.$dot.data(n);return i&&i.destroy(),this.init(),this.truncate(),this.opts.watch&&this.watch(),this},t[n].version=i,t[n].uniqueId=0,t[n].defaults={ellipsis:"… ",callback:function(t){},truncate:"word",tolerance:0,keep:null,watch:"window",height:null},t[n].prototype={init:function(){this.watchTimeout=null,this.watchInterval=null,this.uniqueId=t[n].uniqueId++,this.originalContent=this.$dot.contents(),this.originalStyle=this.$dot.attr("style")||"","break-word"!==this.$dot.css("word-wrap")&&this.$dot.css("word-wrap","break-word"),"nowrap"===this.$dot.css("white-space")&&this.$dot.css("white-space","normal"),null===this.opts.height&&(this.opts.height=this._getMaxHeight())},getInstance:function(){return this},truncate:function(){var e=this;this.$inner=this.$dot.wrapInner("<div />").children().css({display:"block",height:"auto",width:"auto",border:"none",padding:0,margin:0}),this.$inner.contents().detach().end().append(this.originalContent.clone(!0)),this.$inner.find("script, style").addClass(s.keep),this.opts.keep&&this.$inner.find(this.opts.keep).addClass(s.keep),this.$inner.find("*").not("."+s.keep).add(this.$inner).contents().each(function(){var n=this,i=t(this);if(3==n.nodeType){if(i.parent().is("table, thead, tfoot, tr, dl, ul, ol, video"))return void i.remove();if(i.parent().contents().length>1){var r=t('<span class="'+s.text+'">'+e.__getTextContent(n)+"</span>").css({display:"inline",height:"auto",width:"auto",border:"none",padding:0,margin:0});i.replaceWith(r)}}else 8==n.nodeType&&i.remove()}),this.maxHeight=this._getMaxHeight();var n=this._truncateNode(this.$dot);return this.$dot[n?"addClass":"removeClass"](s.truncated),this.$inner.find("."+s.text).each(function(){t(this).replaceWith(t(this).contents())}),this.$inner.find("."+s.keep).removeClass(s.keep),this.$inner.replaceWith(this.$inner.contents()),this.$inner=null,this.opts.callback.call(this.$dot[0],n),n},restore:function(){this.unwatch(),this.$dot.contents().detach().end().append(this.originalContent).attr("style",this.originalStyle).removeClass(s.truncated)},destroy:function(){this.restore(),this.$dot.data(n,null)},watch:function(){var t=this;this.unwatch();var e={};"window"==this.opts.watch?h.on(o.resize+t.uniqueId,function(n){t.watchTimeout&&clearTimeout(t.watchTimeout),t.watchTimeout=setTimeout(function(){e=t._watchSizes(e,h,"width","height")},100)}):this.watchInterval=setInterval(function(){e=t._watchSizes(e,t.$dot,"innerWidth","innerHeight")},500)},unwatch:function(){h.off(o.resize+this.uniqueId),this.watchInterval&&clearInterval(this.watchInterval),this.watchTimeout&&clearTimeout(this.watchTimeout)},_api:function(){var e=this,n={};return t.each(this.api,function(t){var i=this;n[i]=function(){var t=e[i].apply(e,arguments);return"undefined"==typeof t?n:t}}),n},_truncateNode:function(e){var n=this,i=!1,r=!1;return t(e.children().get().reverse()).not("."+s.keep).each(function(){var e=(t(this).contents()[0],t(this));if(!i&&!e.hasClass(s.keep)){if(e.children().length)i=n._truncateNode(e);else if(!n._fits()||r){var o=t("<span>").css("display","none");if(e.replaceWith(o),e.detach(),n._fits()){if("node"==n.opts.truncate)return!0;o.replaceWith(e),i=n._truncateWord(e),i||(r=!0,e.detach())}else o.remove()}e.contents().length||e.remove()}}),i},_truncateWord:function(t){var e=t.contents()[0];if(!e)return!1;for(var n=this,i=this.__getTextContent(e),s=i.indexOf(" ")!==-1?" ":"　",r=i.split(s),o="",h=r.length;h>=0;h--){if(o=r.slice(0,h).join(s),0==h)return"letter"==n.opts.truncate&&(n.__setTextContent(e,r.slice(0,h+1).join(s)),n._truncateLetter(e));if(o.length&&(n.__setTextContent(e,n._addEllipsis(o)),n._fits()))return"letter"!=n.opts.truncate||(n.__setTextContent(e,r.slice(0,h+1).join(s)),n._truncateLetter(e))}return!1},_truncateLetter:function(t){for(var e=this,n=this.__getTextContent(t),i=n.split(""),s="",r=i.length;r>=0;r--)if(s=i.slice(0,r).join(""),s.length&&(e.__setTextContent(t,e._addEllipsis(s)),e._fits()))return!0;return!1},_fits:function(){return this.$inner.innerHeight()<=this.maxHeight+this.opts.tolerance},_addEllipsis:function(e){for(var n=[" ","　",",",";",".","!","?"];t.inArray(e.slice(-1),n)>-1;)e=e.slice(0,-1);return e+=this.opts.ellipsis},_getMaxHeight:function(){if("number"==typeof this.opts.height)return this.opts.height;for(var t=["maxHeight","height"],e=0,n=0;n<t.length;n++)if(e=window.getComputedStyle(this.$dot[0])[t[n]],"px"==e.slice(-2)){e=parseFloat(e);break}var t=[];switch(this.$dot.css("boxSizing")){case"border-box":t.push("borderTopWidth"),t.push("borderBottomWidth");case"padding-box":t.push("paddingTop"),t.push("paddingBottom")}for(var n=0;n<t.length;n++){var i=window.getComputedStyle(this.$dot[0])[t[n]];"px"==i.slice(-2)&&(e-=parseFloat(i))}return Math.max(e,0)},_watchSizes:function(t,e,n,i){if(this.$dot.is(":visible")){var s={width:e[n](),height:e[i]()};return t.width==s.width&&t.height==s.height||this.truncate(),s}return t},__getTextContent:function(t){for(var e=["nodeValue","textContent","innerText"],n=0;n<e.length;n++)if("string"==typeof t[e[n]])return t[e[n]];return""},__setTextContent:function(t,e){for(var n=["nodeValue","textContent","innerText"],i=0;i<n.length;i++)t[n[i]]=e}},t.fn[n]=function(i){return e(),i=t.extend(!0,{},t[n].defaults,i),this.each(function(){t(this).data(n,new t[n](t(this),i)._api())})};var s,r,o,h}}(jQuery);
return true;
}));

"use strict";

function rem(num) {
    if (window.document.documentElement.getBoundingClientRect().width / num * 100 > 133.3) {
        window.document.documentElement.style.fontSize = '133px';
        window.document.documentElement.className = '';
    } else {
        window.document.documentElement.style.fontSize = window.document.documentElement.getBoundingClientRect().width / num * 100 +
            'px';
        window.document.documentElement.className = 'mobile';
    }
}

rem(375);
window.onresize = function () {
    rem(375)
};

//截取url
(function ($) {
    $.extend({
        getQueryString: function (name) {
            function parseParams() {
                var params = {},
                    e,
                    a = /\+/g,  // Regex for replacing addition symbol with a space
                    r = /([^&=]+)=?([^&]*)/g,
                    d = function (s) {
                        return decodeURIComponent(s.replace(a, ''));
                    },
                    q = window.location.search.substring(1);

                while (e = r.exec(q))
                    params[d(e[1])] = d(e[2]);

                return params;
            }

            if (!this.queryStringParams)
                this.queryStringParams = parseParams();

            return this.queryStringParams[name];
        }
    });
    //请求地址
    window.android = $.getQueryString('platform') === undefined ? '' : $.getQueryString('platform');
})(jQuery);

/*
 * Lazy Load - jQuery plugin for lazy loading images
 *
 * Copyright (c) 2007-2013 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/lazyload
 *
 * Version:  1.9.3
 *
 */

(function ($, window, document, undefined) {
    var $window = $(window);

    $.fn.lazyload = function (options) {
        var elements = this;
        var $container;
        var settings = {
            threshold: 0,
            failure_limit: 0,
            event: "scroll",
            effect: "show",
            container: window,
            data_attribute: "original",
            skip_invisible: true,
            appear: null,
            load: null,
            placeholder: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
        };

        function update() {
            var counter = 0;

            elements.each(function () {
                var $this = $(this);
                if (settings.skip_invisible && !$this.is(":visible")) {
                    return;
                }
                if ($.abovethetop(this, settings) ||
                    $.leftofbegin(this, settings)) {
                    /* Nothing. */
                } else if (!$.belowthefold(this, settings) &&
                    !$.rightoffold(this, settings)) {
                    $this.trigger("appear");
                    /* if we found an image we'll load, reset the counter */
                    counter = 0;
                } else {
                    if (++counter > settings.failure_limit) {
                        return false;
                    }
                }
            });

        }

        if (options) {
            /* Maintain BC for a couple of versions. */
            if (undefined !== options.failurelimit) {
                options.failure_limit = options.failurelimit;
                delete options.failurelimit;
            }
            if (undefined !== options.effectspeed) {
                options.effect_speed = options.effectspeed;
                delete options.effectspeed;
            }

            $.extend(settings, options);
        }

        /* Cache container as jQuery as object. */
        $container = (settings.container === undefined ||
            settings.container === window) ? $window : $(settings.container);

        /* Fire one scroll event per scroll. Not one scroll event per image. */
        if (0 === settings.event.indexOf("scroll")) {
            $container.bind(settings.event, function () {
                return update();
            });
        }

        this.each(function () {
            var self = this;
            var $self = $(self);

            self.loaded = false;

            /* If no src attribute given use data:uri. */
            if ($self.attr("src") === undefined || $self.attr("src") === false) {
                if ($self.is("img")) {
                    $self.attr("src", settings.placeholder);
                }
            }

            /* When appear is triggered load original image. */
            $self.one("appear", function () {
                if (!this.loaded) {
                    if (settings.appear) {
                        var elements_left = elements.length;
                        settings.appear.call(self, elements_left, settings);
                    }
                    $("<img />")
                        .bind("load", function () {

                            var original = $self.attr("data-" + settings.data_attribute);
                            $self.hide();
                            if ($self.is("img")) {
                                $self.attr("src", original);
                            } else {
                                $self.css("background-image", "url('" + original + "')");
                            }
                            $self[settings.effect](settings.effect_speed);

                            self.loaded = true;

                            /* Remove image from array so it is not looped next time. */
                            var temp = $.grep(elements, function (element) {
                                return !element.loaded;
                            });
                            elements = $(temp);

                            if (settings.load) {
                                var elements_left = elements.length;
                                settings.load.call(self, elements_left, settings);
                            }
                        })
                        .attr("src", $self.attr("data-" + settings.data_attribute));
                }
            });

            /* When wanted event is triggered load original image */
            /* by triggering appear.                              */
            if (0 !== settings.event.indexOf("scroll")) {
                $self.bind(settings.event, function () {
                    if (!self.loaded) {
                        $self.trigger("appear");
                    }
                });
            }
        });

        /* Check if something appears when window is resized. */
        $window.bind("resize", function () {
            update();
        });

        /* With IOS5 force loading images when navigating with back button. */
        /* Non optimal workaround. */
        if ((/(?:iphone|ipod|ipad).*os 5/gi).test(navigator.appVersion)) {
            $window.bind("pageshow", function (event) {
                if (event.originalEvent && event.originalEvent.persisted) {
                    elements.each(function () {
                        $(this).trigger("appear");
                    });
                }
            });
        }

        /* Force initial check if images should appear. */
        $(document).ready(function () {
            update();
        });

        return this;
    };

    /* Convenience methods in jQuery namespace.           */
    /* Use as  $.belowthefold(element, {threshold : 100, container : window}) */

    $.belowthefold = function (element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = (window.innerHeight ? window.innerHeight : $window.height()) + $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top + $(settings.container).height();
        }

        return fold <= $(element).offset().top - settings.threshold;
    };

    $.rightoffold = function (element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.width() + $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left + $(settings.container).width();
        }

        return fold <= $(element).offset().left - settings.threshold;
    };

    $.abovethetop = function (element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollTop();
        } else {
            fold = $(settings.container).offset().top;
        }

        return fold >= $(element).offset().top + settings.threshold + $(element).height();
    };

    $.leftofbegin = function (element, settings) {
        var fold;

        if (settings.container === undefined || settings.container === window) {
            fold = $window.scrollLeft();
        } else {
            fold = $(settings.container).offset().left;
        }

        return fold >= $(element).offset().left + settings.threshold + $(element).width();
    };

    $.inviewport = function (element, settings) {
        return !$.rightoffold(element, settings) && !$.leftofbegin(element, settings) &&
            !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
    };

    /* Custom selectors for your convenience.   */
    /* Use as $("img:below-the-fold").something() or */
    /* $("img").filter(":below-the-fold").something() which is faster */

    $.extend($.expr[":"], {
        "below-the-fold": function (a) {
            return $.belowthefold(a, {threshold: 0});
        },
        "above-the-top": function (a) {
            return !$.belowthefold(a, {threshold: 0});
        },
        "right-of-screen": function (a) {
            return $.rightoffold(a, {threshold: 0});
        },
        "left-of-screen": function (a) {
            return !$.rightoffold(a, {threshold: 0});
        },
        "in-viewport": function (a) {
            return $.inviewport(a, {threshold: 0});
        },
        /* Maintain BC for couple of versions. */
        "above-the-fold": function (a) {
            return !$.belowthefold(a, {threshold: 0});
        },
        "right-of-fold": function (a) {
            return $.rightoffold(a, {threshold: 0});
        },
        "left-of-fold": function (a) {
            return !$.rightoffold(a, {threshold: 0});
        }
    });

})(jQuery, window, document);


$(function () {
    if(window.android === 'android'){
        window.AnswerApp = {};
        $('.app').show();
    }

    $('.good-wrap').on('click', function () {
        $(this).addClass('yellow')
    });
    $('.more-wrap p').dotdotdot({
        ellipsis: "\u2026 ",
        watch: "window",
        truncate: "word" //letter
    });
    $('.share').on('click', function () {
        var index = $(this).index();
        switch (index) {
            case 0:
                window.AnswerApp.doShareLink('facebook');
                break;
            case 1:
                window.AnswerApp.doShareLink('twitter');
                break;
            case 2:
                window.AnswerApp.doShareLink('whatsapp');
                break;
            case 3:
                window.AnswerApp.doShareLink('telegram');
                break;
            case 4:
                window.AnswerApp.doShareLink('reddit');
                break;
            case 5:
                window.AnswerApp.doShareLink('share');
                break;
        }
    })
    //跳转
    $('.down-load').on('click', function () {
        window.branch.deepviewCta();
    });
});

