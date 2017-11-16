function checkAuth() {
    $("#add_adv").length < 1 || $("#add_adv").on("click", function(t) {
        t.preventDefault();
        var e = "";
        $.ajax({
            type: "GET",
            url: $(this).attr("href"),
            success: function(t, i, n) {
                n.getResponseHeader("content-type") || "";
                try {
                    var a = JSON.parse(t);
                    if (a.need_register && 1 == a.need_register) {
                        // var r = $(".layout__header-info-bar_type_sticky-show").length > 0 ? 0 : 1;
                        // $(".enter-button").eq(r).click();
                        $("#myModal_bill_item").show();
                    }
                } catch (s) {
                    e = t
                }
            }
        }).then(function() {
            e.length > 0 && $.ajax({
                type: "GET",
                url: "/json/ads/checkstatus.html",
                success: function(t) {
                    var i = JSON.parse(t);
                    need_pay_anyway = i.need_pay_anyway, 1 == i.need_pay_anyway && alert("Это не первое Ваше объявление. Оно будет платным. Стоимость размещения 10 грн. Бесплатное размещение доступно один раз в сутки. Также вы можете воспользоваться услугой добавления видео и фото более 5 шт. в Ваше объявление. Такая услуга стоит 10 грн."), $('<div class="modal">' + e + "</div>").appendTo("body").modal(), e = null
                }
            })
        })
    })
}

function detectHref(){
    if(location.pathname == '/basket/'){
    var to = location.href;
    console.info(to);
    setTimeout(pageReplace,1000);
    }
};


function pageReplace(page){
    location.reload();
    // location.replace(page);
}


$(document).ready(function() {
    setInterval(isEmptyBasket,500);
});

function isEmptyBasket(){
    if(location.pathname == '/basket/'){
        if($('basket-bar__notifier').text() == '0'){
            pageReplace('/')
        }
    }
}


function addtoCart() {
    var t = {
        method: "add",
        id: 170172,
        count: 1,
        color: 0,
        size: 0,
        adverts_id: +advertId
    };
    $.ajax({
        type: "POST",
        url: "/json/basket/change/",
        data: t,
        success: function(t) {},
        error: function(t) {
            alert("Ошибка регистрации платной услуги: " + t)
        }
    })
}

function addtoCartandRedirect() {
    var t = {
        method: "add",
        id: 170172,
        count: 1,
        color: 0,
        size: 0,
        adverts_id: +advertId
    };
    $.ajax({
        type: "POST",
        url: "/json/basket/change/",
        data: t,
        success: function(t) {
            console.info("Succesfull service add to cart"), window.location = window.location.origin + "/basket"
        },
        error: function(t) {
            alert("Ошибка регистрации платной услуги: " + t)
        }
    })
}

function createPushToGA() {
    if (0 == funcStatus) {
        funcStatus = 1;
        var t = document.getElementsByTagName("title")[0].textContent.trim(),
            e = window.location.href.split("com")[1];
        console.log(t, e), _gaq.push(["_set", "title", t]), _gaq.push(["_trackPageview", e]), setTimeout(function() {
            funcStatus = 0
        }, 500)
    }
}

function createPushToGA() {
    var t = document.getElementsByTagName("title")[0].textContent.trim(),
        e = window.location.href.split("ua")[1];
    _gaq.push(["_set", "title", t]), _gaq.push(["_trackPageview", e])
}

function hideDelivering() {
    var t = $(".payments-deliveries_align_right .payments-deliveries__radio");
    t.each(function() {
        if (0 == $(this).attr("data-usd-cost")) return $(this).prop("checked", !0), !1
    }), $(".payments-deliveries_align_right").hide(), $(".basket-user-info").hide(), $("#form-page-2").show(), $(".payments-deliveries__delivery-cost").hide(), $("#payment-4, #payment-5, #payment-8").closest("li").remove(), $(".advertStatus").val(1)
}
var requirejs, require, define;
! function(t) {
    function e(t, e) {
        return v.call(t, e)
    }

    function i(t, e) {
        var i, n, a, r, s, o, l, u, c, d, p = e && e.split("/"),
            f = m.map,
            h = f && f["*"] || {};
        if (t && "." === t.charAt(0))
            if (e) {
                for (p = p.slice(0, p.length - 1), t = p.concat(t.split("/")), u = 0; u < t.length; u += 1)
                    if (d = t[u], "." === d) t.splice(u, 1), u -= 1;
                    else if (".." === d) {
                        if (1 === u && (".." === t[2] || ".." === t[0])) break;
                        u > 0 && (t.splice(u - 1, 2), u -= 2)
                    }
                t = t.join("/")
            } else 0 === t.indexOf("./") && (t = t.substring(2));
        if ((p || h) && f) {
            for (i = t.split("/"), u = i.length; u > 0; u -= 1) {
                if (n = i.slice(0, u).join("/"), p)
                    for (c = p.length; c > 0; c -= 1)
                        if (a = f[p.slice(0, c).join("/")], a && (a = a[n])) {
                            r = a, s = u;
                            break
                        }
                if (r) break;
                !o && h && h[n] && (o = h[n], l = u)
            }!r && o && (r = o, s = l), r && (i.splice(0, s, r), t = i.join("/"))
        }
        return t
    }

    function n(e, i) {
        return function() {
            return c.apply(t, _.call(arguments, 0).concat([e, i]))
        }
    }

    function a(t) {
        return function(e) {
            return i(e, t)
        }
    }

    function r(t) {
        return function(e) {
            f[t] = e
        }
    }

    function s(i) {
        if (e(h, i)) {
            var n = h[i];
            delete h[i], g[i] = !0, u.apply(t, n)
        }
        if (!e(f, i) && !e(g, i)) throw new Error("No " + i);
        return f[i]
    }

    function o(t) {
        var e, i = t ? t.indexOf("!") : -1;
        return i > -1 && (e = t.substring(0, i), t = t.substring(i + 1, t.length)), [e, t]
    }

    function l(t) {
        return function() {
            return m && m.config && m.config[t] || {}
        }
    }
    var u, c, d, p, f = {},
        h = {},
        m = {},
        g = {},
        v = Object.prototype.hasOwnProperty,
        _ = [].slice;
    d = function(t, e) {
        var n, r = o(t),
            l = r[0];
        return t = r[1], l && (l = i(l, e), n = s(l)), l ? t = n && n.normalize ? n.normalize(t, a(e)) : i(t, e) : (t = i(t, e), r = o(t), l = r[0], t = r[1], l && (n = s(l))), {
            f: l ? l + "!" + t : t,
            n: t,
            pr: l,
            p: n
        }
    }, p = {
        require: function(t) {
            return n(t)
        },
        exports: function(t) {
            var e = f[t];
            return "undefined" != typeof e ? e : f[t] = {}
        },
        module: function(t) {
            return {
                id: t,
                uri: "",
                exports: f[t],
                config: l(t)
            }
        }
    }, u = function(i, a, o, l) {
        var u, c, m, v, _, b, y = [];
        if (l = l || i, "function" == typeof o) {
            for (a = !a.length && o.length ? ["require", "exports", "module"] : a, _ = 0; _ < a.length; _ += 1)
                if (v = d(a[_], l), c = v.f, "require" === c) y[_] = p.require(i);
                else if ("exports" === c) y[_] = p.exports(i), b = !0;
                else if ("module" === c) u = y[_] = p.module(i);
                else if (e(f, c) || e(h, c) || e(g, c)) y[_] = s(c);
                else {
                    if (!v.p) throw new Error(i + " missing " + c);
                    v.p.load(v.n, n(l, !0), r(c), {}), y[_] = f[c]
                }
            m = o.apply(f[i], y), i && (u && u.exports !== t && u.exports !== f[i] ? f[i] = u.exports : m === t && b || (f[i] = m))
        } else i && (f[i] = o)
    }, requirejs = require = c = function(e, i, n, a, r) {
        return "string" == typeof e ? p[e] ? p[e](i) : s(d(e, i).f) : (e.splice || (m = e, i.splice ? (e = i, i = n, n = null) : e = t), i = i || function() {}, "function" == typeof n && (n = a, a = r), a ? u(t, e, i, n) : setTimeout(function() {
            u(t, e, i, n)
        }, 4), c)
    }, c.config = function(t) {
        return m = t, m.deps && c(m.deps, m.callback), c
    }, requirejs._defined = f, define = function(t, i, n) {
        i.splice || (n = i, i = []), e(f, t) || e(h, t) || (h[t] = [t, i, n])
    }, define.amd = {
        jQuery: !0
    }
}(), define("../components/external/almond/almond", function() {}),
    function() {
        define("jquery", [], function() {
            return jQuery
        })
    }.call(this),
    function(t, e) {
        "object" == typeof exports && module ? module.exports = e() : "function" == typeof define && define.amd ? define("pubsub", e) : t.PubSub = e()
    }("object" == typeof window && window || this, function() {
        function t(t) {
            return function() {
                throw t
            }
        }

        function e(e, i, n) {
            try {
                e(i, n)
            } catch (a) {
                setTimeout(t(a), 0)
            }
        }

        function i(t, e, i) {
            t(e, i)
        }

        function n(t, n, a, r) {
            var s, o = l[n],
                u = r ? i : e;
            if (l.hasOwnProperty(n))
                for (s = 0; s < o.length; s++) u(o[s].func, t, a)
        }

        function a(t, e, i) {
            return function() {
                var a = String(t),
                    r = a.lastIndexOf(".");
                for (n(t, t, e, i); r !== -1;) a = a.substr(0, r), r = a.lastIndexOf("."), n(t, a, e)
            }
        }

        function r(t) {
            for (var e = String(t), i = l.hasOwnProperty(e), n = e.lastIndexOf("."); !i && n !== -1;) e = e.substr(0, n), n = e.lastIndexOf("."), i = l.hasOwnProperty(e);
            return i && l[e].length > 0
        }

        function s(t, e, i, n) {
            var s = a(t, e, n),
                o = r(t);
            return !!o && (i === !0 ? s() : setTimeout(s, 0), !0)
        }
        var o = {},
            l = {},
            u = -1;
        return o.publish = function(t, e) {
            return s(t, e, !1, o.immediateExceptions)
        }, o.publishSync = function(t, e) {
            return s(t, e, !0, o.immediateExceptions)
        }, o.subscribe = function(t, e) {
            if ("function" != typeof e) return !1;
            l.hasOwnProperty(t) || (l[t] = []);
            var i = String(++u);
            return l[t].push({
                token: i,
                func: e
            }), i
        }, o.unsubscribe = function(t) {
            var e, i, n = "string" == typeof t,
                a = n ? "token" : "func",
                r = !n || t,
                s = !1;
            for (e in l)
                if (l.hasOwnProperty(e))
                    for (i = l[e].length - 1; i >= 0; i--)
                        if (l[e][i][a] === t && (l[e].splice(i, 1), s = r, n)) return s;
            return s
        }, o
    }),
    function() {
        define("sparky", ["jquery"], function() {
            var t;
            return t = {
                settings: {},
                init: function(t) {
                    var e;
                    return $('meta[name^="app-"]').each(function() {
                        return t.meta[this.name.replace("app-", "")] = this.content
                    }), "undefined" == typeof t.meta.route && (e = window.location.pathname, "/" === e[0] && (e = e.substring(1)), "/" === e[e.length - 1] && (e = e.substring(0, e.length - 1)), "" === e && (e = "index"), t.meta.route = e), this.settings = t
                },
                log: function(t) {
                    if (this.settings.debug) return console.log(t)
                },
                parseRoute: function(t) {
                    var e, i, n, a, r;
                    return i = t.delimiter || "/", r = t.path.split(i), e = t.target[r.shift()], n = "undefined" != typeof e, a = 0 === r.length, t.inits = t.inits || [], n ? ("function" == typeof e.init && t.inits.push(e.init), a ? t.parsed.call(void 0, {
                        exists: !0,
                        type: typeof e,
                        obj: e,
                        inits: t.inits
                    }) : this.parseRoute({
                        path: r.join(i),
                        target: e,
                        delimiter: i,
                        parsed: t.parsed,
                        inits: t.inits
                    })) : t.parsed.call(void 0, {
                        exists: !1
                    })
                },
                route: function(t) {
                    return this.parseRoute({
                        path: this.settings.meta.route,
                        target: t,
                        delimiter: "/",
                        parsed: function(t) {
                            var e, i, n, a;
                            if (t.exists && "function" === t.type) {
                                if (0 !== t.inits.length)
                                    for (a = t.inits, i = 0, n = a.length; i < n; i++) e = a[i], t.inits[e].call();
                                return t.obj.call()
                            }
                        }
                    })
                },
                bindEvents: function(e) {
                    return $("[data-event]").each(function() {
                        var i, n, a, r, s;
                        if (i = this, a = $(this).data(), r = a.method || "click", s = a.event, n = a.bound, !n) return t.parseRoute({
                            path: s,
                            target: e.endpoints,
                            delimiter: ".",
                            parsed: function(t) {
                                if (t.exists) return a.bound = !0, $(i).on(r, function(e) {
                                    return t.obj.call(i, e)
                                })
                            }
                        })
                    })
                }
            }
        })
    }.call(this),
    function(t) {
        t.flexslider = function(e, i) {
            var n = t(e);
            n.vars = t.extend({}, t.flexslider.defaults, i);
            var a, r = n.vars.namespace,
                s = window.navigator && window.navigator.msPointerEnabled && window.MSGesture,
                o = ("ontouchstart" in window || s || window.DocumentTouch && document instanceof DocumentTouch) && n.vars.touch,
                l = "click touchend MSPointerUp keyup",
                u = "",
                c = "vertical" === n.vars.direction,
                d = n.vars.reverse,
                p = n.vars.itemWidth > 0,
                f = "fade" === n.vars.animation,
                h = "" !== n.vars.asNavFor,
                m = {},
                g = !0;
            t.data(e, "flexslider", n), m = {
                init: function() {
                    n.animating = !1, n.currentSlide = parseInt(n.vars.startAt ? n.vars.startAt : 0, 10), isNaN(n.currentSlide) && (n.currentSlide = 0), n.animatingTo = n.currentSlide, n.atEnd = 0 === n.currentSlide || n.currentSlide === n.last, n.containerSelector = n.vars.selector.substr(0, n.vars.selector.search(" ")), n.slides = t(n.vars.selector, n), n.container = t(n.containerSelector, n), n.count = n.slides.length, n.syncExists = t(n.vars.sync).length > 0, "slide" === n.vars.animation && (n.vars.animation = "swing"), n.prop = c ? "top" : "marginLeft", n.args = {}, n.manualPause = !1, n.stopped = !1, n.started = !1, n.startTimeout = null, n.transitions = !n.vars.video && !f && n.vars.useCSS && function() {
                        var t = document.createElement("div"),
                            e = ["perspectiveProperty", "WebkitPerspective", "MozPerspective", "OPerspective", "msPerspective"];
                        for (var i in e)
                            if (void 0 !== t.style[e[i]]) return n.pfx = e[i].replace("Perspective", "").toLowerCase(), n.prop = "-" + n.pfx + "-transform", !0;
                        return !1
                    }(), n.ensureAnimationEnd = "", "" !== n.vars.controlsContainer && (n.controlsContainer = t(n.vars.controlsContainer).length > 0 && t(n.vars.controlsContainer)), "" !== n.vars.manualControls && (n.manualControls = t(n.vars.manualControls).length > 0 && t(n.vars.manualControls)), n.vars.randomize && (n.slides.sort(function() {
                        return Math.round(Math.random()) - .5
                    }), n.container.empty().append(n.slides)), n.doMath(), n.setup("init"), n.vars.controlNav && m.controlNav.setup(), n.vars.directionNav && m.directionNav.setup(), n.vars.keyboard && (1 === t(n.containerSelector).length || n.vars.multipleKeyboard) && t(document).bind("keyup", function(t) {
                        var e = t.keyCode;
                        if (!n.animating && (39 === e || 37 === e)) {
                            var i = 39 === e ? n.getTarget("next") : 37 === e && n.getTarget("prev");
                            n.flexAnimate(i, n.vars.pauseOnAction)
                        }
                    }), n.vars.mousewheel && n.bind("mousewheel", function(t, e, i, a) {
                        t.preventDefault();
                        var r = e < 0 ? n.getTarget("next") : n.getTarget("prev");
                        n.flexAnimate(r, n.vars.pauseOnAction)
                    }), n.vars.pausePlay && m.pausePlay.setup(), n.vars.slideshow && n.vars.pauseInvisible && m.pauseInvisible.init(), n.vars.slideshow && (n.vars.pauseOnHover && n.hover(function() {
                        n.manualPlay || n.manualPause || n.pause()
                    }, function() {
                        n.manualPause || n.manualPlay || n.stopped || n.play()
                    }), n.vars.pauseInvisible && m.pauseInvisible.isHidden() || (n.vars.initDelay > 0 ? n.startTimeout = setTimeout(n.play, n.vars.initDelay) : n.play())), h && m.asNav.setup(), o && n.vars.touch && m.touch(), (!f || f && n.vars.smoothHeight) && t(window).bind("resize orientationchange focus", m.resize), n.find("img").attr("draggable", "false"), setTimeout(function() {
                        n.vars.start(n)
                    }, 200)
                },
                asNav: {
                    setup: function() {
                        n.asNav = !0, n.animatingTo = Math.floor(n.currentSlide / n.move), n.currentItem = n.currentSlide, n.slides.removeClass(r + "active-slide").eq(n.currentItem).addClass(r + "active-slide"), s ? (e._slider = n, n.slides.each(function() {
                            var e = this;
                            e._gesture = new MSGesture, e._gesture.target = e, e.addEventListener("MSPointerDown", function(t) {
                                t.preventDefault(), t.currentTarget._gesture && t.currentTarget._gesture.addPointer(t.pointerId)
                            }, !1), e.addEventListener("MSGestureTap", function(e) {
                                e.preventDefault();
                                var i = t(this),
                                    a = i.index();
                                t(n.vars.asNavFor).data("flexslider").animating || i.hasClass("active") || (n.direction = n.currentItem < a ? "next" : "prev", n.flexAnimate(a, n.vars.pauseOnAction, !1, !0, !0))
                            })
                        })) : n.slides.on(l, function(e) {
                            e.preventDefault();
                            var i = t(this),
                                a = i.index(),
                                s = i.offset().left - t(n).scrollLeft();
                            s <= 0 && i.hasClass(r + "active-slide") ? n.flexAnimate(n.getTarget("prev"), !0) : t(n.vars.asNavFor).data("flexslider").animating || i.hasClass(r + "active-slide") || (n.direction = n.currentItem < a ? "next" : "prev", n.flexAnimate(a, n.vars.pauseOnAction, !1, !0, !0))
                        })
                    }
                },
                controlNav: {
                    setup: function() {
                        n.manualControls ? m.controlNav.setupManual() : m.controlNav.setupPaging()
                    },
                    setupPaging: function() {
                        var e, i, a = "thumbnails" === n.vars.controlNav ? "control-thumbs" : "control-paging",
                            s = 1;
                        if (n.controlNavScaffold = t('<ol class="' + r + "control-nav " + r + a + '"></ol>'), n.pagingCount > 1)
                            for (var o = 0; o < n.pagingCount; o++) {
                                if (i = n.slides.eq(o), e = "thumbnails" === n.vars.controlNav ? '<img src="' + i.attr("data-thumb") + '"/>' : "<a>" + s + "</a>", "thumbnails" === n.vars.controlNav && !0 === n.vars.thumbCaptions) {
                                    var c = i.attr("data-thumbcaption");
                                    "" != c && void 0 != c && (e += '<span class="' + r + 'caption">' + c + "</span>")
                                }
                                n.controlNavScaffold.append("<li>" + e + "</li>"), s++
                            }
                        n.controlsContainer ? t(n.controlsContainer).append(n.controlNavScaffold) : n.append(n.controlNavScaffold), m.controlNav.set(), m.controlNav.active(), n.controlNavScaffold.delegate("a, img", l, function(e) {
                            if (e.preventDefault(), "" === u || u === e.type) {
                                var i = t(this),
                                    a = n.controlNav.index(i);
                                i.hasClass(r + "active") || (n.direction = a > n.currentSlide ? "next" : "prev", n.flexAnimate(a, n.vars.pauseOnAction))
                            }
                            "" === u && (u = e.type), m.setToClearWatchedEvent()
                        })
                    },
                    setupManual: function() {
                        n.controlNav = n.manualControls, m.controlNav.active(), n.controlNav.bind(l, function(e) {
                            if (e.preventDefault(), "" === u || u === e.type) {
                                var i = t(this),
                                    a = n.controlNav.index(i);
                                i.hasClass(r + "active") || (a > n.currentSlide ? n.direction = "next" : n.direction = "prev", n.flexAnimate(a, n.vars.pauseOnAction))
                            }
                            "" === u && (u = e.type), m.setToClearWatchedEvent()
                        })
                    },
                    set: function() {
                        var e = "thumbnails" === n.vars.controlNav ? "img" : "a";
                        n.controlNav = t("." + r + "control-nav li " + e, n.controlsContainer ? n.controlsContainer : n)
                    },
                    active: function() {
                        n.controlNav.removeClass(r + "active").eq(n.animatingTo).addClass(r + "active")
                    },
                    update: function(e, i) {
                        n.pagingCount > 1 && "add" === e ? n.controlNavScaffold.append(t("<li><a>" + n.count + "</a></li>")) : 1 === n.pagingCount ? n.controlNavScaffold.find("li").remove() : n.controlNav.eq(i).closest("li").remove(), m.controlNav.set(), n.pagingCount > 1 && n.pagingCount !== n.controlNav.length ? n.update(i, e) : m.controlNav.active()
                    }
                },
                directionNav: {
                    setup: function() {
                        var e = t('<ul class="' + r + 'direction-nav"><li><a class="' + r + 'prev" href="#">' + n.vars.prevText + '</a></li><li><a class="' + r + 'next" href="#">' + n.vars.nextText + "</a></li></ul>");
                        n.controlsContainer ? (t(n.controlsContainer).append(e), n.directionNav = t("." + r + "direction-nav li a", n.controlsContainer)) : (n.append(e), n.directionNav = t("." + r + "direction-nav li a", n)), m.directionNav.update(), n.directionNav.bind(l, function(e) {
                            e.preventDefault();
                            var i;
                            "" !== u && u !== e.type || (i = t(this).hasClass(r + "next") ? n.getTarget("next") : n.getTarget("prev"), n.flexAnimate(i, n.vars.pauseOnAction)), "" === u && (u = e.type), m.setToClearWatchedEvent()
                        })
                    },
                    update: function() {
                        var t = r + "disabled";
                        1 === n.pagingCount ? n.directionNav.addClass(t).attr("tabindex", "-1") : n.vars.animationLoop ? n.directionNav.removeClass(t).removeAttr("tabindex") : 0 === n.animatingTo ? n.directionNav.removeClass(t).filter("." + r + "prev").addClass(t).attr("tabindex", "-1") : n.animatingTo === n.last ? n.directionNav.removeClass(t).filter("." + r + "next").addClass(t).attr("tabindex", "-1") : n.directionNav.removeClass(t).removeAttr("tabindex")
                    }
                },
                pausePlay: {
                    setup: function() {
                        var e = t('<div class="' + r + 'pauseplay"><a></a></div>');
                        n.controlsContainer ? (n.controlsContainer.append(e), n.pausePlay = t("." + r + "pauseplay a", n.controlsContainer)) : (n.append(e), n.pausePlay = t("." + r + "pauseplay a", n)), m.pausePlay.update(n.vars.slideshow ? r + "pause" : r + "play"), n.pausePlay.bind(l, function(e) {
                            e.preventDefault(), "" !== u && u !== e.type || (t(this).hasClass(r + "pause") ? (n.manualPause = !0, n.manualPlay = !1, n.pause()) : (n.manualPause = !1, n.manualPlay = !0, n.play())), "" === u && (u = e.type), m.setToClearWatchedEvent()
                        })
                    },
                    update: function(t) {
                        "play" === t ? n.pausePlay.removeClass(r + "pause").addClass(r + "play").html(n.vars.playText) : n.pausePlay.removeClass(r + "play").addClass(r + "pause").html(n.vars.pauseText)
                    }
                },
                touch: function() {
                    function t(t) {
                        n.animating ? t.preventDefault() : (window.navigator.msPointerEnabled || 1 === t.touches.length) && (n.pause(), g = c ? n.h : n.w, _ = Number(new Date), y = t.touches[0].pageX, w = t.touches[0].pageY, m = p && d && n.animatingTo === n.last ? 0 : p && d ? n.limit - (n.itemW + n.vars.itemMargin) * n.move * n.animatingTo : p && n.currentSlide === n.last ? n.limit : p ? (n.itemW + n.vars.itemMargin) * n.move * n.currentSlide : d ? (n.last - n.currentSlide + n.cloneOffset) * g : (n.currentSlide + n.cloneOffset) * g, u = c ? w : y, h = c ? y : w, e.addEventListener("touchmove", i, !1), e.addEventListener("touchend", a, !1))
                    }

                    function i(t) {
                        y = t.touches[0].pageX, w = t.touches[0].pageY, v = c ? u - w : u - y, b = c ? Math.abs(v) < Math.abs(y - h) : Math.abs(v) < Math.abs(w - h);
                        var e = 500;
                        (!b || Number(new Date) - _ > e) && (t.preventDefault(), !f && n.transitions && (n.vars.animationLoop || (v /= 0 === n.currentSlide && v < 0 || n.currentSlide === n.last && v > 0 ? Math.abs(v) / g + 2 : 1), n.setProps(m + v, "setTouch")))
                    }

                    function a(t) {
                        if (e.removeEventListener("touchmove", i, !1), n.animatingTo === n.currentSlide && !b && null !== v) {
                            var r = d ? -v : v,
                                s = r > 0 ? n.getTarget("next") : n.getTarget("prev");
                            n.canAdvance(s) && (Number(new Date) - _ < 550 && Math.abs(r) > 50 || Math.abs(r) > g / 2) ? n.flexAnimate(s, n.vars.pauseOnAction) : f || n.flexAnimate(n.currentSlide, n.vars.pauseOnAction, !0)
                        }
                        e.removeEventListener("touchend", a, !1), u = null, h = null, v = null, m = null
                    }

                    function r(t) {
                        t.stopPropagation(), n.animating ? t.preventDefault() : (n.pause(), e._gesture.addPointer(t.pointerId), x = 0, g = c ? n.h : n.w, _ = Number(new Date), m = p && d && n.animatingTo === n.last ? 0 : p && d ? n.limit - (n.itemW + n.vars.itemMargin) * n.move * n.animatingTo : p && n.currentSlide === n.last ? n.limit : p ? (n.itemW + n.vars.itemMargin) * n.move * n.currentSlide : d ? (n.last - n.currentSlide + n.cloneOffset) * g : (n.currentSlide + n.cloneOffset) * g)
                    }

                    function o(t) {
                        t.stopPropagation();
                        var i = t.target._slider;
                        if (i) {
                            var n = -t.translationX,
                                a = -t.translationY;
                            return x += c ? a : n, v = x, b = c ? Math.abs(x) < Math.abs(-n) : Math.abs(x) < Math.abs(-a), t.detail === t.MSGESTURE_FLAG_INERTIA ? void setImmediate(function() {
                                e._gesture.stop()
                            }) : void((!b || Number(new Date) - _ > 500) && (t.preventDefault(), !f && i.transitions && (i.vars.animationLoop || (v = x / (0 === i.currentSlide && x < 0 || i.currentSlide === i.last && x > 0 ? Math.abs(x) / g + 2 : 1)), i.setProps(m + v, "setTouch"))))
                        }
                    }

                    function l(t) {
                        t.stopPropagation();
                        var e = t.target._slider;
                        if (e) {
                            if (e.animatingTo === e.currentSlide && !b && null !== v) {
                                var i = d ? -v : v,
                                    n = i > 0 ? e.getTarget("next") : e.getTarget("prev");
                                e.canAdvance(n) && (Number(new Date) - _ < 550 && Math.abs(i) > 50 || Math.abs(i) > g / 2) ? e.flexAnimate(n, e.vars.pauseOnAction) : f || e.flexAnimate(e.currentSlide, e.vars.pauseOnAction, !0)
                            }
                            u = null, h = null, v = null, m = null, x = 0
                        }
                    }
                    var u, h, m, g, v, _, b = !1,
                        y = 0,
                        w = 0,
                        x = 0;
                    s ? (e.style.msTouchAction = "none", e._gesture = new MSGesture, e._gesture.target = e, e.addEventListener("MSPointerDown", r, !1), e._slider = n, e.addEventListener("MSGestureChange", o, !1), e.addEventListener("MSGestureEnd", l, !1)) : e.addEventListener("touchstart", t, !1)
                },
                resize: function() {
                    !n.animating && n.is(":visible") && (p || n.doMath(), f ? m.smoothHeight() : p ? (n.slides.width(n.computedW), n.update(n.pagingCount), n.setProps()) : c ? (n.viewport.height(n.h), n.setProps(n.h, "setTotal")) : (n.vars.smoothHeight && m.smoothHeight(), n.newSlides.width(n.computedW), n.setProps(n.computedW, "setTotal")))
                },
                smoothHeight: function(t) {
                    if (!c || f) {
                        var e = f ? n : n.viewport;
                        t ? e.animate({
                            height: n.slides.eq(n.animatingTo).height()
                        }, t) : e.height(n.slides.eq(n.animatingTo).height())
                    }
                },
                sync: function(e) {
                    var i = t(n.vars.sync).data("flexslider"),
                        a = n.animatingTo;
                    switch (e) {
                        case "animate":
                            i.flexAnimate(a, n.vars.pauseOnAction, !1, !0);
                            break;
                        case "play":
                            i.playing || i.asNav || i.play();
                            break;
                        case "pause":
                            i.pause()
                    }
                },
                uniqueID: function(e) {
                    return e.filter("[id]").add(e.find("[id]")).each(function() {
                        var e = t(this);
                        e.attr("id", e.attr("id") + "_clone")
                    }), e
                },
                pauseInvisible: {
                    visProp: null,
                    init: function() {
                        var t = m.pauseInvisible.getHiddenProp();
                        if (t) {
                            var e = t.replace(/[H|h]idden/, "") + "visibilitychange";
                            document.addEventListener(e, function() {
                                m.pauseInvisible.isHidden() ? n.startTimeout ? clearTimeout(n.startTimeout) : n.pause() : n.started ? n.play() : n.vars.initDelay > 0 ? setTimeout(n.play, n.vars.initDelay) : n.play()
                            })
                        }
                    },
                    isHidden: function() {
                        var t = m.pauseInvisible.getHiddenProp();
                        return !!t && document[t]
                    },
                    getHiddenProp: function() {
                        var t = ["webkit", "moz", "ms", "o"];
                        if ("hidden" in document) return "hidden";
                        for (var e = 0; e < t.length; e++)
                            if (t[e] + "Hidden" in document) return t[e] + "Hidden";
                        return null
                    }
                },
                setToClearWatchedEvent: function() {
                    clearTimeout(a), a = setTimeout(function() {
                        u = ""
                    }, 3e3)
                }
            }, n.flexAnimate = function(e, i, a, s, l) {
                if (n.vars.animationLoop || e === n.currentSlide || (n.direction = e > n.currentSlide ? "next" : "prev"), h && 1 === n.pagingCount && (n.direction = n.currentItem < e ? "next" : "prev"), !n.animating && (n.canAdvance(e, l) || a) && n.is(":visible")) {
                    if (h && s) {
                        var u = t(n.vars.asNavFor).data("flexslider");
                        if (n.atEnd = 0 === e || e === n.count - 1, u.flexAnimate(e, !0, !1, !0, l), n.direction = n.currentItem < e ? "next" : "prev", u.direction = n.direction, Math.ceil((e + 1) / n.visible) - 1 === n.currentSlide || 0 === e) return n.currentItem = e, n.slides.removeClass(r + "active-slide").eq(e).addClass(r + "active-slide"), !1;
                        n.currentItem = e, n.slides.removeClass(r + "active-slide").eq(e).addClass(r + "active-slide"), e = Math.floor(e / n.visible)
                    }
                    if (n.animating = !0, n.animatingTo = e, i && n.pause(), n.vars.before(n), n.syncExists && !l && m.sync("animate"), n.vars.controlNav && m.controlNav.active(), p || n.slides.removeClass(r + "active-slide").eq(e).addClass(r + "active-slide"), n.atEnd = 0 === e || e === n.last, n.vars.directionNav && m.directionNav.update(), e === n.last && (n.vars.end(n), n.vars.animationLoop || n.pause()), f) o ? (n.slides.eq(n.currentSlide).css({
                        opacity: 0,
                        zIndex: 1
                    }), n.slides.eq(e).css({
                        opacity: 1,
                        zIndex: 2
                    }), n.wrapup(b)) : (n.slides.eq(n.currentSlide).css({
                        zIndex: 1
                    }).animate({
                        opacity: 0
                    }, n.vars.animationSpeed, n.vars.easing), n.slides.eq(e).css({
                        zIndex: 2
                    }).animate({
                        opacity: 1
                    }, n.vars.animationSpeed, n.vars.easing, n.wrapup));
                    else {
                        var g, v, _, b = c ? n.slides.filter(":first").height() : n.computedW;
                        p ? (g = n.vars.itemMargin, _ = (n.itemW + g) * n.move * n.animatingTo, v = _ > n.limit && 1 !== n.visible ? n.limit : _) : v = 0 === n.currentSlide && e === n.count - 1 && n.vars.animationLoop && "next" !== n.direction ? d ? (n.count + n.cloneOffset) * b : 0 : n.currentSlide === n.last && 0 === e && n.vars.animationLoop && "prev" !== n.direction ? d ? 0 : (n.count + 1) * b : d ? (n.count - 1 - e + n.cloneOffset) * b : (e + n.cloneOffset) * b, n.setProps(v, "", n.vars.animationSpeed), n.transitions ? (n.vars.animationLoop && n.atEnd || (n.animating = !1, n.currentSlide = n.animatingTo), n.container.unbind("webkitTransitionEnd transitionend"), n.container.bind("webkitTransitionEnd transitionend", function() {
                            clearTimeout(n.ensureAnimationEnd), n.wrapup(b)
                        }), clearTimeout(n.ensureAnimationEnd), n.ensureAnimationEnd = setTimeout(function() {
                            n.wrapup(b)
                        }, n.vars.animationSpeed + 100)) : n.container.animate(n.args, n.vars.animationSpeed, n.vars.easing, function() {
                            n.wrapup(b)
                        })
                    }
                    n.vars.smoothHeight && m.smoothHeight(n.vars.animationSpeed)
                }
            }, n.wrapup = function(t) {
                f || p || (0 === n.currentSlide && n.animatingTo === n.last && n.vars.animationLoop ? n.setProps(t, "jumpEnd") : n.currentSlide === n.last && 0 === n.animatingTo && n.vars.animationLoop && n.setProps(t, "jumpStart")), n.animating = !1, n.currentSlide = n.animatingTo, n.vars.after(n)
            }, n.animateSlides = function() {
                !n.animating && g && n.flexAnimate(n.getTarget("next"))
            }, n.pause = function() {
                clearInterval(n.animatedSlides), n.animatedSlides = null, n.playing = !1, n.vars.pausePlay && m.pausePlay.update("play"), n.syncExists && m.sync("pause")
            }, n.play = function() {
                n.playing && clearInterval(n.animatedSlides), n.animatedSlides = n.animatedSlides || setInterval(n.animateSlides, n.vars.slideshowSpeed), n.started = n.playing = !0, n.vars.pausePlay && m.pausePlay.update("pause"), n.syncExists && m.sync("play")
            }, n.stop = function() {
                n.pause(), n.stopped = !0
            }, n.canAdvance = function(t, e) {
                var i = h ? n.pagingCount - 1 : n.last;
                return !!e || (!(!h || n.currentItem !== n.count - 1 || 0 !== t || "prev" !== n.direction) || (!h || 0 !== n.currentItem || t !== n.pagingCount - 1 || "next" === n.direction) && (!(t === n.currentSlide && !h) && (!!n.vars.animationLoop || (!n.atEnd || 0 !== n.currentSlide || t !== i || "next" === n.direction) && (!n.atEnd || n.currentSlide !== i || 0 !== t || "next" !== n.direction))))
            }, n.getTarget = function(t) {
                return n.direction = t, "next" === t ? n.currentSlide === n.last ? 0 : n.currentSlide + 1 : 0 === n.currentSlide ? n.last : n.currentSlide - 1
            }, n.setProps = function(t, e, i) {
                var a = function() {
                    var i = t ? t : (n.itemW + n.vars.itemMargin) * n.move * n.animatingTo,
                        a = function() {
                            if (p) return "setTouch" === e ? t : d && n.animatingTo === n.last ? 0 : d ? n.limit - (n.itemW + n.vars.itemMargin) * n.move * n.animatingTo : n.animatingTo === n.last ? n.limit : i;
                            switch (e) {
                                case "setTotal":
                                    return d ? (n.count - 1 - n.currentSlide + n.cloneOffset) * t : (n.currentSlide + n.cloneOffset) * t;
                                case "setTouch":
                                    return d ? t : t;
                                case "jumpEnd":
                                    return d ? t : n.count * t;
                                case "jumpStart":
                                    return d ? n.count * t : t;
                                default:
                                    return t
                            }
                        }();
                    return a * -1 + "px"
                }();
                n.transitions && (a = c ? "translate3d(0," + a + ",0)" : "translate3d(" + a + ",0,0)", i = void 0 !== i ? i / 1e3 + "s" : "0s", n.container.css("-" + n.pfx + "-transition-duration", i), n.container.css("transition-duration", i)), n.args[n.prop] = a, (n.transitions || void 0 === i) && n.container.css(n.args), n.container.css("transform", a)
            }, n.setup = function(e) {
                if (f) n.slides.css({
                    width: "100%",
                    "float": "left",
                    marginRight: "-100%",
                    position: "relative"
                }), "init" === e && (o ? n.slides.css({
                    opacity: 0,
                    display: "block",
                    webkitTransition: "opacity " + n.vars.animationSpeed / 1e3 + "s ease",
                    zIndex: 1
                }).eq(n.currentSlide).css({
                    opacity: 1,
                    zIndex: 2
                }) : 0 == n.vars.fadeFirstSlide ? n.slides.css({
                    opacity: 0,
                    display: "block",
                    zIndex: 1
                }).eq(n.currentSlide).css({
                    zIndex: 2
                }).css({
                    opacity: 1
                }) : n.slides.css({
                    opacity: 0,
                    display: "block",
                    zIndex: 1
                }).eq(n.currentSlide).css({
                    zIndex: 2
                }).animate({
                    opacity: 1
                }, n.vars.animationSpeed, n.vars.easing)), n.vars.smoothHeight && m.smoothHeight();
                else {
                    var i, a;
                    "init" === e && (n.viewport = t('<div class="' + r + 'viewport"></div>').css({
                        overflow: "hidden",
                        position: "relative"
                    }).appendTo(n).append(n.container), n.cloneCount = 0, n.cloneOffset = 0, d && (a = t.makeArray(n.slides).reverse(), n.slides = t(a), n.container.empty().append(n.slides))), n.vars.animationLoop && !p && (n.cloneCount = 2, n.cloneOffset = 1, "init" !== e && n.container.find(".clone").remove(), n.container.append(m.uniqueID(n.slides.first().clone().addClass("clone")).attr("aria-hidden", "true")).prepend(m.uniqueID(n.slides.last().clone().addClass("clone")).attr("aria-hidden", "true"))), n.newSlides = t(n.vars.selector, n), i = d ? n.count - 1 - n.currentSlide + n.cloneOffset : n.currentSlide + n.cloneOffset, c && !p ? (n.container.height(200 * (n.count + n.cloneCount) + "%").css("position", "absolute").width("100%"), setTimeout(function() {
                        n.newSlides.css({
                            display: "block"
                        }), n.doMath(), n.viewport.height(n.h), n.setProps(i * n.h, "init")
                    }, "init" === e ? 100 : 0)) : (n.container.width(200 * (n.count + n.cloneCount) + "%"), n.setProps(i * n.computedW, "init"), setTimeout(function() {
                        n.doMath(), n.newSlides.css({
                            width: n.computedW,
                            "float": "left",
                            display: "block"
                        }), n.vars.smoothHeight && m.smoothHeight()
                    }, "init" === e ? 100 : 0))
                }
                p || n.slides.removeClass(r + "active-slide").eq(n.currentSlide).addClass(r + "active-slide"), n.vars.init(n)
            }, n.doMath = function() {
                var t = n.slides.first(),
                    e = n.vars.itemMargin,
                    i = n.vars.minItems,
                    a = n.vars.maxItems;
                n.w = void 0 === n.viewport ? n.width() : n.viewport.width(), n.h = t.height(), n.boxPadding = t.outerWidth() - t.width(), p ? (n.itemT = n.vars.itemWidth + e, n.minW = i ? i * n.itemT : n.w, n.maxW = a ? a * n.itemT - e : n.w, n.itemW = n.minW > n.w ? (n.w - e * (i - 1)) / i : n.maxW < n.w ? (n.w - e * (a - 1)) / a : n.vars.itemWidth > n.w ? n.w : n.vars.itemWidth, n.visible = Math.floor(n.w / n.itemW), n.move = n.vars.move > 0 && n.vars.move < n.visible ? n.vars.move : n.visible, n.pagingCount = Math.ceil((n.count - n.visible) / n.move + 1), n.last = n.pagingCount - 1, n.limit = 1 === n.pagingCount ? 0 : n.vars.itemWidth > n.w ? n.itemW * (n.count - 1) + e * (n.count - 1) : (n.itemW + e) * n.count - n.w - e) : (n.itemW = n.w, n.pagingCount = n.count, n.last = n.count - 1), n.computedW = n.itemW - n.boxPadding
            }, n.update = function(t, e) {
                n.doMath(), p || (t < n.currentSlide ? n.currentSlide += 1 : t <= n.currentSlide && 0 !== t && (n.currentSlide -= 1), n.animatingTo = n.currentSlide), n.vars.controlNav && !n.manualControls && ("add" === e && !p || n.pagingCount > n.controlNav.length ? m.controlNav.update("add") : ("remove" === e && !p || n.pagingCount < n.controlNav.length) && (p && n.currentSlide > n.last && (n.currentSlide -= 1, n.animatingTo -= 1), m.controlNav.update("remove", n.last))), n.vars.directionNav && m.directionNav.update()
            }, n.addSlide = function(e, i) {
                var a = t(e);
                n.count += 1, n.last = n.count - 1, c && d ? void 0 !== i ? n.slides.eq(n.count - i).after(a) : n.container.prepend(a) : void 0 !== i ? n.slides.eq(i).before(a) : n.container.append(a), n.update(i, "add"), n.slides = t(n.vars.selector + ":not(.clone)", n), n.setup(), n.vars.added(n)
            }, n.removeSlide = function(e) {
                var i = isNaN(e) ? n.slides.index(t(e)) : e;
                n.count -= 1, n.last = n.count - 1, isNaN(e) ? t(e, n.slides).remove() : c && d ? n.slides.eq(n.last).remove() : n.slides.eq(e).remove(), n.doMath(), n.update(i, "remove"), n.slides = t(n.vars.selector + ":not(.clone)", n), n.setup(), n.vars.removed(n)
            }, m.init()
        }, t(window).blur(function(t) {
            focused = !1
        }).focus(function(t) {
            focused = !0
        }), t.flexslider.defaults = {
            namespace: "flex-",
            selector: ".slides > li",
            animation: "fade",
            easing: "swing",
            direction: "horizontal",
            reverse: !1,
            animationLoop: !0,
            smoothHeight: !1,
            startAt: 0,
            slideshow: !0,
            slideshowSpeed: 7e3,
            animationSpeed: 600,
            initDelay: 0,
            randomize: !1,
            fadeFirstSlide: !0,
            thumbCaptions: !1,
            pauseOnAction: !0,
            pauseOnHover: !1,
            pauseInvisible: !0,
            useCSS: !0,
            touch: !0,
            video: !1,
            controlNav: !0,
            directionNav: !0,
            prevText: "Previous",
            nextText: "Next",
            keyboard: !0,
            multipleKeyboard: !1,
            mousewheel: !1,
            pausePlay: !1,
            pauseText: "Pause",
            playText: "Play",
            controlsContainer: "",
            manualControls: "",
            sync: "",
            asNavFor: "",
            itemWidth: 0,
            itemMargin: 0,
            minItems: 1,
            maxItems: 0,
            move: 0,
            allowOneSlide: !0,
            start: function() {},
            before: function() {},
            after: function() {},
            end: function() {},
            added: function() {},
            removed: function() {},
            init: function() {}
        }, t.fn.flexslider = function(e) {
            if (void 0 === e && (e = {}), "object" == typeof e) return this.each(function() {
                var i = t(this),
                    n = e.selector ? e.selector : ".slides > li",
                    a = i.find(n);
                1 === a.length && e.allowOneSlide === !0 || 0 === a.length ? (a.fadeIn(400), e.start && e.start(i)) : void 0 === i.data("flexslider") && new t.flexslider(this, e)
            });
            var i = t(this).data("flexslider");
            switch (e) {
                case "play":
                    i.play();
                    break;
                case "pause":
                    i.pause();
                    break;
                case "stop":
                    i.stop();
                    break;
                case "next":
                    i.flexAnimate(i.getTarget("next"), !0);
                    break;
                case "prev":
                case "previous":
                    i.flexAnimate(i.getTarget("prev"), !0);
                    break;
                default:
                    "number" == typeof e && i.flexAnimate(e, !0)
            }
        }
    }(jQuery), define("flexslider", function() {}),
    function() {
        define("Deals", ["jquery", "flexslider"], function(t) {
            var e;
            return e = {
                init: function() {
                    var t;
                    return t = this
                },
                initSlider: function(e) {
                    var i;
                    return i = t(e), i.flexslider({
                        animation: "slide",
                        directionNav: !0,
                        controlNav: !1,
                        animationLoop: !1,
                        itemWidth: 227,
                        itemMargin: 9,
                        slideshow: !0,
                        controlsContainer: "#hot-products__nav"
                    })
                }
            }
        })
    }.call(this),
    function(t, e, i) {
        function n(t) {
            return !t || "loaded" == t || "complete" == t || "uninitialized" == t
        }

        function a(t, i, a, r, o, l) {
            var u, c, p = e.createElement("script");
            r = r || d.errorTimeout, p.src = t;
            for (c in a) p.setAttribute(c, a[c]);
            i = l ? s : i || _, p.onreadystatechange = p.onload = function() {
                !u && n(p.readyState) && (u = 1, i(), p.onload = p.onreadystatechange = null)
            }, f(function() {
                u || (u = 1, i(1))
            }, r), o ? p.onload() : h.parentNode.insertBefore(p, h)
        }

        function r(t, i, n, a, r, o) {
            var l, u = e.createElement("link");
            a = a || d.errorTimeout, i = o ? s : i || _, u.href = t, u.rel = "stylesheet", u.type = "text/css";
            for (l in n) u.setAttribute(l, n[l]);
            r || (h.parentNode.insertBefore(u, h), f(i, 0))
        }

        function s() {
            var t = g.shift();
            v = 1, t ? t.t ? f(function() {
                ("c" == t.t ? d.injectCss : d.injectJs)(t.s, 0, t.a, t.x, t.e, 1)
            }, 0) : (t(), s()) : v = 0
        }

        function o(t, i, a, r, o, l, u) {
            function c(e) {
                if (!m && n(p.readyState) && (b.r = m = 1, !v && s(), p.onload = p.onreadystatechange = null, e)) {
                    "img" != t && f(function() {
                        w.removeChild(p)
                    }, 50);
                    for (var a in N[i]) N[i].hasOwnProperty(a) && N[i][a].onload()
                }
            }
            u = u || d.errorTimeout;
            var p = e.createElement(t),
                m = 0,
                _ = 0,
                b = {
                    t: a,
                    s: i,
                    e: o,
                    a: l,
                    x: u
                };
            1 === N[i] && (_ = 1, N[i] = []), "object" == t ? p.data = i : (p.src = i, p.type = t), p.width = p.height = "0", p.onerror = p.onload = p.onreadystatechange = function() {
                c.call(this, _)
            }, g.splice(r, 0, b), "img" != t && (_ || 2 === N[i] ? (w.insertBefore(p, y ? null : h), f(c, u)) : N[i].push(p))
        }

        function l(t, e, i, n, a) {
            return v = 0, e = e || "j", A(t) ? o("c" == e ? T : C, t, e, this.i++, i, n, a) : (g.splice(this.i++, 0, t), 1 == g.length && s()), this
        }

        function u() {
            var t = d;
            return t.loader = {
                load: l,
                i: 0
            }, t
        }
        var c, d, p = e.documentElement,
            f = t.setTimeout,
            h = e.getElementsByTagName("script")[0],
            m = {}.toString,
            g = [],
            v = 0,
            _ = function() {},
            b = "MozAppearance" in p.style,
            y = b && !!e.createRange().compareNode,
            w = y ? p : h.parentNode,
            x = t.opera && "[object Opera]" == m.call(t.opera),
            k = !!e.attachEvent && !x,
            C = b ? "object" : k ? "script" : "img",
            T = k ? "script" : C,
            S = Array.isArray || function(t) {
                return "[object Array]" == m.call(t)
            },
            E = function(t) {
                return Object(t) === t
            },
            A = function(t) {
                return "string" == typeof t
            },
            P = function(t) {
                return "[object Function]" == m.call(t)
            },
            O = [],
            N = {},
            D = {
                timeout: function(t, e) {
                    return e.length && (t.timeout = e[0]), t
                }
            };
        d = function(t) {
            function e(t) {
                var e, i, n, a = t.split("!"),
                    r = O.length,
                    s = a.pop(),
                    o = a.length,
                    l = {
                        url: s,
                        origUrl: s,
                        prefixes: a
                    };
                for (i = 0; i < o; i++) n = a[i].split("="), e = D[n.shift()], e && (l = e(l, n));
                for (i = 0; i < r; i++) l = O[i](l);
                return l
            }

            function n(t) {
                return t.split(".").pop().split("?").shift()
            }

            function a(t, a, r, s, o) {
                var l = e(t),
                    c = l.autoCallback;
                n(l.url);
                if (!l.bypass) return a && (a = P(a) ? a : a[t] || a[s] || a[t.split("/").pop().split("?")[0]]), l.instead ? l.instead(t, a, r, s, o) : (N[l.url] ? l.noexec = !0 : N[l.url] = 1, r.load(l.url, l.forceCSS || !l.forceJS && "css" == n(l.url) ? "c" : i, l.noexec, l.attrs, l.timeout), (P(a) || P(c)) && r.load(function() {
                    u(), a && a(l.origUrl, o, s), c && c(l.origUrl, o, s), N[l.url] = 2
                }), void 0)
            }

            function r(t, e) {
                function i(t, i) {
                    if (t) {
                        if (A(t)) i || (u = function() {
                            var t = [].slice.call(arguments);
                            c.apply(this, t), d()
                        }), a(t, u, e, 0, s);
                        else if (E(t)) {
                            n = function() {
                                var e, i = 0;
                                for (e in t) t.hasOwnProperty(e) && i++;
                                return i
                            }();
                            for (r in t) t.hasOwnProperty(r) && (i || --n || (P(u) ? u = function() {
                                var t = [].slice.call(arguments);
                                c.apply(this, t), d()
                            } : u[r] = function(t) {
                                return function() {
                                    var e = [].slice.call(arguments);
                                    t && t.apply(this, e), d()
                                }
                            }(c[r])), a(t[r], u, e, r, s))
                        }
                    } else !i && d()
                }
                var n, r, s = !!t.test,
                    o = s ? t.yep : t.nope,
                    l = t.load || t.both,
                    u = t.callback || _,
                    c = u,
                    d = t.complete || _;
                i(o, !!l), l && i(l)
            }
            var s, o, l = this.yepnope.loader;
            if (A(t)) a(t, 0, l, 0);
            else if (S(t))
                for (s = 0; s < t.length; s++) o = t[s], A(o) ? a(o, 0, l, 0) : S(o) ? d(o) : E(o) && r(o, l);
            else E(t) && r(t, l)
        }, d.addPrefix = function(t, e) {
            D[t] = e
        }, d.addFilter = function(t) {
            O.push(t)
        }, d.errorTimeout = 1e4, null == e.readyState && e.addEventListener && (e.readyState = "loading", e.addEventListener("DOMContentLoaded", c = function() {
            e.removeEventListener("DOMContentLoaded", c, 0), e.readyState = "complete"
        }, 0)), t.yepnope = u(), t.yepnope.executeStack = s, t.yepnope.injectJs = a, t.yepnope.injectCss = r
    }(this, document), define("yepnope", function() {}),
    function(t) {
        "function" == typeof define && define.amd ? define("iframe_transport", ["jquery"], t) : t("object" == typeof exports ? require("jquery") : window.jQuery)
    }(function(t) {
        var e = 0;
        t.ajaxTransport("iframe", function(i) {
            if (i.async) {
                var n, a, r, s = i.initialIframeSrc || "javascript:false;";
                return {
                    send: function(o, l) {
                        n = t('<form style="display:none;"></form>'), n.attr("accept-charset", i.formAcceptCharset), r = /\?/.test(i.url) ? "&" : "?", "DELETE" === i.type ? (i.url = i.url + r + "_method=DELETE", i.type = "POST") : "PUT" === i.type ? (i.url = i.url + r + "_method=PUT", i.type = "POST") : "PATCH" === i.type && (i.url = i.url + r + "_method=PATCH", i.type = "POST"), e += 1, a = t('<iframe src="' + s + '" name="iframe-transport-' + e + '"></iframe>').bind("load", function() {
                            var e, r = t.isArray(i.paramName) ? i.paramName : [i.paramName];
                            a.unbind("load").bind("load", function() {
                                var e;
                                try {
                                    if (e = a.contents(), !e.length || !e[0].firstChild) throw new Error
                                } catch (i) {
                                    e = void 0
                                }
                                l(200, "success", {
                                    iframe: e
                                }), t('<iframe src="' + s + '"></iframe>').appendTo(n), window.setTimeout(function() {
                                    n.remove()
                                }, 0)
                            }), n.prop("target", a.prop("name")).prop("action", i.url).prop("method", i.type), i.formData && t.each(i.formData, function(e, i) {
                                t('<input type="hidden"/>').prop("name", i.name).val(i.value).appendTo(n)
                            }), i.fileInput && i.fileInput.length && "POST" === i.type && (e = i.fileInput.clone(), i.fileInput.after(function(t) {
                                return e[t]
                            }), i.paramName && i.fileInput.each(function(e) {
                                t(this).prop("name", r[e] || i.paramName)
                            }), n.append(i.fileInput).prop("enctype", "multipart/form-data").prop("encoding", "multipart/form-data"), i.fileInput.removeAttr("form")), n.submit(), e && e.length && i.fileInput.each(function(i, n) {
                                var a = t(e[i]);
                                t(n).prop("name", a.prop("name")).attr("form", a.attr("form")), a.replaceWith(n)
                            })
                        }), n.append(a).appendTo(document.body)
                    },
                    abort: function() {
                        a && a.unbind("load").prop("src", s), n && n.remove()
                    }
                }
            }
        }), t.ajaxSetup({
            converters: {
                "iframe text": function(e) {
                    return e && t(e[0].body).text()
                },
                "iframe json": function(e) {
                    return e && t.parseJSON(t(e[0].body).text())
                },
                "iframe html": function(e) {
                    return e && t(e[0].body).html()
                },
                "iframe xml": function(e) {
                    var i = e && e[0];
                    return i && t.isXMLDoc(i) ? i : t.parseXML(i.XMLDocument && i.XMLDocument.xml || t(i.body).html())
                },
                "iframe script": function(e) {
                    return e && t.globalEval(t(e[0].body).text())
                }
            }
        })
    }),
    function(t) {
        "function" == typeof define && define.amd ? define("jquery.ui.widget", ["jquery"], t) : t("object" == typeof exports ? require("jquery") : jQuery)
    }(function(t) {
        var e = 0,
            i = Array.prototype.slice;
        t.cleanData = function(e) {
            return function(i) {
                var n, a, r;
                for (r = 0; null != (a = i[r]); r++) try {
                    n = t._data(a, "events"), n && n.remove && t(a).triggerHandler("remove")
                } catch (s) {}
                e(i)
            }
        }(t.cleanData), t.widget = function(e, i, n) {
            var a, r, s, o, l = {},
                u = e.split(".")[0];
            return e = e.split(".")[1], a = u + "-" + e, n || (n = i, i = t.Widget), t.expr[":"][a.toLowerCase()] = function(e) {
                return !!t.data(e, a)
            }, t[u] = t[u] || {}, r = t[u][e], s = t[u][e] = function(t, e) {
                return this._createWidget ? void(arguments.length && this._createWidget(t, e)) : new s(t, e)
            }, t.extend(s, r, {
                version: n.version,
                _proto: t.extend({}, n),
                _childConstructors: []
            }), o = new i, o.options = t.widget.extend({}, o.options), t.each(n, function(e, n) {
                return t.isFunction(n) ? void(l[e] = function() {
                    var t = function() {
                            return i.prototype[e].apply(this, arguments)
                        },
                        a = function(t) {
                            return i.prototype[e].apply(this, t)
                        };
                    return function() {
                        var e, i = this._super,
                            r = this._superApply;
                        return this._super = t, this._superApply = a, e = n.apply(this, arguments), this._super = i, this._superApply = r, e
                    }
                }()) : void(l[e] = n)
            }), s.prototype = t.widget.extend(o, {
                widgetEventPrefix: r ? o.widgetEventPrefix || e : e
            }, l, {
                constructor: s,
                namespace: u,
                widgetName: e,
                widgetFullName: a
            }), r ? (t.each(r._childConstructors, function(e, i) {
                var n = i.prototype;
                t.widget(n.namespace + "." + n.widgetName, s, i._proto)
            }), delete r._childConstructors) : i._childConstructors.push(s), t.widget.bridge(e, s), s
        }, t.widget.extend = function(e) {
            for (var n, a, r = i.call(arguments, 1), s = 0, o = r.length; s < o; s++)
                for (n in r[s]) a = r[s][n], r[s].hasOwnProperty(n) && void 0 !== a && (t.isPlainObject(a) ? e[n] = t.isPlainObject(e[n]) ? t.widget.extend({}, e[n], a) : t.widget.extend({}, a) : e[n] = a);
            return e
        }, t.widget.bridge = function(e, n) {
            var a = n.prototype.widgetFullName || e;
            t.fn[e] = function(r) {
                var s = "string" == typeof r,
                    o = i.call(arguments, 1),
                    l = this;
                return r = !s && o.length ? t.widget.extend.apply(null, [r].concat(o)) : r, s ? this.each(function() {
                    var i, n = t.data(this, a);
                    return "instance" === r ? (l = n, !1) : n ? t.isFunction(n[r]) && "_" !== r.charAt(0) ? (i = n[r].apply(n, o), i !== n && void 0 !== i ? (l = i && i.jquery ? l.pushStack(i.get()) : i, !1) : void 0) : t.error("no such method '" + r + "' for " + e + " widget instance") : t.error("cannot call methods on " + e + " prior to initialization; attempted to call method '" + r + "'")
                }) : this.each(function() {
                    var e = t.data(this, a);
                    e ? (e.option(r || {}), e._init && e._init()) : t.data(this, a, new n(r, this))
                }), l
            }
        }, t.Widget = function() {}, t.Widget._childConstructors = [], t.Widget.prototype = {
            widgetName: "widget",
            widgetEventPrefix: "",
            defaultElement: "<div>",
            options: {
                disabled: !1,
                create: null
            },
            _createWidget: function(i, n) {
                n = t(n || this.defaultElement || this)[0], this.element = t(n), this.uuid = e++, this.eventNamespace = "." + this.widgetName + this.uuid, this.options = t.widget.extend({}, this.options, this._getCreateOptions(), i), this.bindings = t(), this.hoverable = t(), this.focusable = t(), n !== this && (t.data(n, this.widgetFullName, this), this._on(!0, this.element, {
                    remove: function(t) {
                        t.target === n && this.destroy()
                    }
                }), this.document = t(n.style ? n.ownerDocument : n.document || n), this.window = t(this.document[0].defaultView || this.document[0].parentWindow)), this._create(), this._trigger("create", null, this._getCreateEventData()), this._init()
            },
            _getCreateOptions: t.noop,
            _getCreateEventData: t.noop,
            _create: t.noop,
            _init: t.noop,
            destroy: function() {
                this._destroy(), this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(t.camelCase(this.widgetFullName)), this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName + "-disabled ui-state-disabled"), this.bindings.unbind(this.eventNamespace), this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus")
            },
            _destroy: t.noop,
            widget: function() {
                return this.element
            },
            option: function(e, i) {
                var n, a, r, s = e;
                if (0 === arguments.length) return t.widget.extend({}, this.options);
                if ("string" == typeof e)
                    if (s = {}, n = e.split("."), e = n.shift(), n.length) {
                        for (a = s[e] = t.widget.extend({}, this.options[e]), r = 0; r < n.length - 1; r++) a[n[r]] = a[n[r]] || {}, a = a[n[r]];
                        if (e = n.pop(), 1 === arguments.length) return void 0 === a[e] ? null : a[e];
                        a[e] = i
                    } else {
                        if (1 === arguments.length) return void 0 === this.options[e] ? null : this.options[e];
                        s[e] = i
                    }
                return this._setOptions(s), this
            },
            _setOptions: function(t) {
                var e;
                for (e in t) this._setOption(e, t[e]);
                return this
            },
            _setOption: function(t, e) {
                return this.options[t] = e, "disabled" === t && (this.widget().toggleClass(this.widgetFullName + "-disabled", !!e), e && (this.hoverable.removeClass("ui-state-hover"), this.focusable.removeClass("ui-state-focus"))), this
            },
            enable: function() {
                return this._setOptions({
                    disabled: !1
                })
            },
            disable: function() {
                return this._setOptions({
                    disabled: !0
                })
            },
            _on: function(e, i, n) {
                var a, r = this;
                "boolean" != typeof e && (n = i, i = e, e = !1), n ? (i = a = t(i), this.bindings = this.bindings.add(i)) : (n = i, i = this.element, a = this.widget()), t.each(n, function(n, s) {
                    function o() {
                        if (e || r.options.disabled !== !0 && !t(this).hasClass("ui-state-disabled")) return ("string" == typeof s ? r[s] : s).apply(r, arguments)
                    }
                    "string" != typeof s && (o.guid = s.guid = s.guid || o.guid || t.guid++);
                    var l = n.match(/^([\w:-]*)\s*(.*)$/),
                        u = l[1] + r.eventNamespace,
                        c = l[2];
                    c ? a.delegate(c, u, o) : i.bind(u, o)
                })
            },
            _off: function(t, e) {
                e = (e || "").split(" ").join(this.eventNamespace + " ") + this.eventNamespace, t.unbind(e).undelegate(e)
            },
            _delay: function(t, e) {
                function i() {
                    return ("string" == typeof t ? n[t] : t).apply(n, arguments)
                }
                var n = this;
                return setTimeout(i, e || 0)
            },
            _hoverable: function(e) {
                this.hoverable = this.hoverable.add(e), this._on(e, {
                    mouseenter: function(e) {
                        t(e.currentTarget).addClass("ui-state-hover")
                    },
                    mouseleave: function(e) {
                        t(e.currentTarget).removeClass("ui-state-hover")
                    }
                })
            },
            _focusable: function(e) {
                this.focusable = this.focusable.add(e), this._on(e, {
                    focusin: function(e) {
                        t(e.currentTarget).addClass("ui-state-focus")
                    },
                    focusout: function(e) {
                        t(e.currentTarget).removeClass("ui-state-focus")
                    }
                })
            },
            _trigger: function(e, i, n) {
                var a, r, s = this.options[e];
                if (n = n || {}, i = t.Event(i), i.type = (e === this.widgetEventPrefix ? e : this.widgetEventPrefix + e).toLowerCase(), i.target = this.element[0], r = i.originalEvent)
                    for (a in r) a in i || (i[a] = r[a]);
                return this.element.trigger(i, n), !(t.isFunction(s) && s.apply(this.element[0], [i].concat(n)) === !1 || i.isDefaultPrevented())
            }
        }, t.each({
            show: "fadeIn",
            hide: "fadeOut"
        }, function(e, i) {
            t.Widget.prototype["_" + e] = function(n, a, r) {
                "string" == typeof a && (a = {
                    effect: a
                });
                var s, o = a ? a === !0 || "number" == typeof a ? i : a.effect || i : e;
                a = a || {}, "number" == typeof a && (a = {
                    duration: a
                }), s = !t.isEmptyObject(a), a.complete = r, a.delay && n.delay(a.delay), s && t.effects && t.effects.effect[o] ? n[e](a) : o !== e && n[o] ? n[o](a.duration, a.easing, r) : n.queue(function(i) {
                    t(this)[e](), r && r.call(n[0]), i()
                })
            }
        });
        t.widget
    }),
    function(t) {
        "function" == typeof define && define.amd ? define("fileupload", ["jquery", "jquery.ui.widget"], t) : "object" == typeof exports ? t(require("jquery"), require("./vendor/jquery.ui.widget")) : t(window.jQuery)
    }(function(t) {
        function e(e) {
            var i = "dragover" === e;
            return function(n) {
                n.dataTransfer = n.originalEvent && n.originalEvent.dataTransfer;
                var a = n.dataTransfer;
                a && t.inArray("Files", a.types) !== -1 && this._trigger(e, t.Event(e, {
                    delegatedEvent: n
                })) !== !1 && (n.preventDefault(), i && (a.dropEffect = "copy"))
            }
        }
        t.support.fileInput = !(new RegExp("(Android (1\\.[0156]|2\\.[01]))|(Windows Phone (OS 7|8\\.0))|(XBLWP)|(ZuneWP)|(WPDesktop)|(w(eb)?OSBrowser)|(webOS)|(Kindle/(1\\.0|2\\.[05]|3\\.0))").test(window.navigator.userAgent) || t('<input type="file">').prop("disabled")), t.support.xhrFileUpload = !(!window.ProgressEvent || !window.FileReader), t.support.xhrFormDataFileUpload = !!window.FormData, t.support.blobSlice = window.Blob && (Blob.prototype.slice || Blob.prototype.webkitSlice || Blob.prototype.mozSlice), t.widget("blueimp.fileupload", {
            options: {
                dropZone: t(document),
                pasteZone: void 0,
                fileInput: void 0,
                replaceFileInput: !0,
                paramName: void 0,
                singleFileUploads: !0,
                limitMultiFileUploads: void 0,
                limitMultiFileUploadSize: void 0,
                limitMultiFileUploadSizeOverhead: 512,
                sequentialUploads: !1,
                limitConcurrentUploads: void 0,
                forceIframeTransport: !1,
                redirect: void 0,
                redirectParamName: void 0,
                postMessage: void 0,
                multipart: !0,
                maxChunkSize: void 0,
                uploadedBytes: void 0,
                recalculateProgress: !0,
                progressInterval: 100,
                bitrateInterval: 500,
                autoUpload: !0,
                messages: {
                    uploadedBytes: "Uploaded bytes exceed file size"
                },
                i18n: function(e, i) {
                    return e = this.messages[e] || e.toString(), i && t.each(i, function(t, i) {
                        e = e.replace("{" + t + "}", i)
                    }), e
                },
                formData: function(t) {
                    return t.serializeArray()
                },
                add: function(e, i) {
                    return !e.isDefaultPrevented() && void((i.autoUpload || i.autoUpload !== !1 && t(this).fileupload("option", "autoUpload")) && i.process().done(function() {
                        i.submit()
                    }))
                },
                processData: !1,
                contentType: !1,
                cache: !1
            },
            _specialOptions: ["fileInput", "dropZone", "pasteZone", "multipart", "forceIframeTransport"],
            _blobSlice: t.support.blobSlice && function() {
                var t = this.slice || this.webkitSlice || this.mozSlice;
                return t.apply(this, arguments)
            },
            _BitrateTimer: function() {
                this.timestamp = Date.now ? Date.now() : (new Date).getTime(), this.loaded = 0, this.bitrate = 0, this.getBitrate = function(t, e, i) {
                    var n = t - this.timestamp;
                    return (!this.bitrate || !i || n > i) && (this.bitrate = (e - this.loaded) * (1e3 / n) * 8, this.loaded = e, this.timestamp = t), this.bitrate
                }
            },
            _isXHRUpload: function(e) {
                return !e.forceIframeTransport && (!e.multipart && t.support.xhrFileUpload || t.support.xhrFormDataFileUpload)
            },
            _getFormData: function(e) {
                var i;
                return "function" === t.type(e.formData) ? e.formData(e.form) : t.isArray(e.formData) ? e.formData : "object" === t.type(e.formData) ? (i = [], t.each(e.formData, function(t, e) {
                    i.push({
                        name: t,
                        value: e
                    })
                }), i) : []
            },
            _getTotal: function(e) {
                var i = 0;
                return t.each(e, function(t, e) {
                    i += e.size || 1
                }), i
            },
            _initProgressObject: function(e) {
                var i = {
                    loaded: 0,
                    total: 0,
                    bitrate: 0
                };
                e._progress ? t.extend(e._progress, i) : e._progress = i
            },
            _initResponseObject: function(t) {
                var e;
                if (t._response)
                    for (e in t._response) t._response.hasOwnProperty(e) && delete t._response[e];
                else t._response = {}
            },
            _onProgress: function(e, i) {
                if (e.lengthComputable) {
                    var n, a = Date.now ? Date.now() : (new Date).getTime();
                    if (i._time && i.progressInterval && a - i._time < i.progressInterval && e.loaded !== e.total) return;
                    i._time = a, n = Math.floor(e.loaded / e.total * (i.chunkSize || i._progress.total)) + (i.uploadedBytes || 0), this._progress.loaded += n - i._progress.loaded, this._progress.bitrate = this._bitrateTimer.getBitrate(a, this._progress.loaded, i.bitrateInterval), i._progress.loaded = i.loaded = n, i._progress.bitrate = i.bitrate = i._bitrateTimer.getBitrate(a, n, i.bitrateInterval), this._trigger("progress", t.Event("progress", {
                        delegatedEvent: e
                    }), i), this._trigger("progressall", t.Event("progressall", {
                        delegatedEvent: e
                    }), this._progress)
                }
            },
            _initProgressListener: function(e) {
                var i = this,
                    n = e.xhr ? e.xhr() : t.ajaxSettings.xhr();
                n.upload && (t(n.upload).bind("progress", function(t) {
                    var n = t.originalEvent;
                    t.lengthComputable = n.lengthComputable, t.loaded = n.loaded, t.total = n.total, i._onProgress(t, e)
                }), e.xhr = function() {
                    return n
                })
            },
            _isInstanceOf: function(t, e) {
                return Object.prototype.toString.call(e) === "[object " + t + "]"
            },
            _initXHRData: function(e) {
                var i, n = this,
                    a = e.files[0],
                    r = e.multipart || !t.support.xhrFileUpload,
                    s = "array" === t.type(e.paramName) ? e.paramName[0] : e.paramName;
                e.headers = t.extend({}, e.headers), e.contentRange && (e.headers["Content-Range"] = e.contentRange), r && !e.blob && this._isInstanceOf("File", a) || (e.headers["Content-Disposition"] = 'attachment; filename="' + encodeURI(a.name) + '"'), r ? t.support.xhrFormDataFileUpload && (e.postMessage ? (i = this._getFormData(e), e.blob ? i.push({
                    name: s,
                    value: e.blob
                }) : t.each(e.files, function(n, a) {
                    i.push({
                        name: "array" === t.type(e.paramName) && e.paramName[n] || s,
                        value: a
                    })
                })) : (n._isInstanceOf("FormData", e.formData) ? i = e.formData : (i = new FormData, t.each(this._getFormData(e), function(t, e) {
                    i.append(e.name, e.value)
                })), e.blob ? i.append(s, e.blob, a.name) : t.each(e.files, function(a, r) {
                    (n._isInstanceOf("File", r) || n._isInstanceOf("Blob", r)) && i.append("array" === t.type(e.paramName) && e.paramName[a] || s, r, r.uploadName || r.name)
                })), e.data = i) : (e.contentType = a.type || "application/octet-stream", e.data = e.blob || a), e.blob = null
            },
            _initIframeSettings: function(e) {
                var i = t("<a></a>").prop("href", e.url).prop("host");
                e.dataType = "iframe " + (e.dataType || ""), e.formData = this._getFormData(e), e.redirect && i && i !== location.host && e.formData.push({
                    name: e.redirectParamName || "redirect",
                    value: e.redirect
                })
            },
            _initDataSettings: function(t) {
                this._isXHRUpload(t) ? (this._chunkedUpload(t, !0) || (t.data || this._initXHRData(t), this._initProgressListener(t)), t.postMessage && (t.dataType = "postmessage " + (t.dataType || ""))) : this._initIframeSettings(t)
            },
            _getParamName: function(e) {
                var i = t(e.fileInput),
                    n = e.paramName;
                return n ? t.isArray(n) || (n = [n]) : (n = [], i.each(function() {
                    for (var e = t(this), i = e.prop("name") || "files[]", a = (e.prop("files") || [1]).length; a;) n.push(i), a -= 1
                }), n.length || (n = [i.prop("name") || "files[]"])), n
            },
            _initFormSettings: function(e) {
                e.form && e.form.length || (e.form = t(e.fileInput.prop("form")), e.form.length || (e.form = t(this.options.fileInput.prop("form")))), e.paramName = this._getParamName(e), e.url || (e.url = e.form.prop("action") || location.href), e.type = (e.type || "string" === t.type(e.form.prop("method")) && e.form.prop("method") || "").toUpperCase(), "POST" !== e.type && "PUT" !== e.type && "PATCH" !== e.type && (e.type = "POST"), e.formAcceptCharset || (e.formAcceptCharset = e.form.attr("accept-charset"))
            },
            _getAJAXSettings: function(e) {
                var i = t.extend({}, this.options, e);
                return this._initFormSettings(i), this._initDataSettings(i), i
            },
            _getDeferredState: function(t) {
                return t.state ? t.state() : t.isResolved() ? "resolved" : t.isRejected() ? "rejected" : "pending"
            },
            _enhancePromise: function(t) {
                return t.success = t.done, t.error = t.fail, t.complete = t.always, t
            },
            _getXHRPromise: function(e, i, n) {
                var a = t.Deferred(),
                    r = a.promise();
                return i = i || this.options.context || r, e === !0 ? a.resolveWith(i, n) : e === !1 && a.rejectWith(i, n), r.abort = a.promise, this._enhancePromise(r)
            },
            _addConvenienceMethods: function(e, i) {
                var n = this,
                    a = function(e) {
                        return t.Deferred().resolveWith(n, e).promise()
                    };
                i.process = function(e, r) {
                    return (e || r) && (i._processQueue = this._processQueue = (this._processQueue || a([this])).pipe(function() {
                        return i.errorThrown ? t.Deferred().rejectWith(n, [i]).promise() : a(arguments)
                    }).pipe(e, r)), this._processQueue || a([this])
                }, i.submit = function() {
                    return "pending" !== this.state() && (i.jqXHR = this.jqXHR = n._trigger("submit", t.Event("submit", {
                        delegatedEvent: e
                    }), this) !== !1 && n._onSend(e, this)), this.jqXHR || n._getXHRPromise()
                }, i.abort = function() {
                    return this.jqXHR ? this.jqXHR.abort() : (this.errorThrown = "abort", n._trigger("fail", null, this), n._getXHRPromise(!1))
                }, i.state = function() {
                    return this.jqXHR ? n._getDeferredState(this.jqXHR) : this._processQueue ? n._getDeferredState(this._processQueue) : void 0
                }, i.processing = function() {
                    return !this.jqXHR && this._processQueue && "pending" === n._getDeferredState(this._processQueue)
                }, i.progress = function() {
                    return this._progress
                }, i.response = function() {
                    return this._response
                }
            },
            _getUploadedBytes: function(t) {
                var e = t.getResponseHeader("Range"),
                    i = e && e.split("-"),
                    n = i && i.length > 1 && parseInt(i[1], 10);
                return n && n + 1
            },
            _chunkedUpload: function(e, i) {
                e.uploadedBytes = e.uploadedBytes || 0;
                var n, a, r = this,
                    s = e.files[0],
                    o = s.size,
                    l = e.uploadedBytes,
                    u = e.maxChunkSize || o,
                    c = this._blobSlice,
                    d = t.Deferred(),
                    p = d.promise();
                return !(!(this._isXHRUpload(e) && c && (l || u < o)) || e.data) && (!!i || (l >= o ? (s.error = e.i18n("uploadedBytes"), this._getXHRPromise(!1, e.context, [null, "error", s.error])) : (a = function() {
                    var i = t.extend({}, e),
                        p = i._progress.loaded;
                    i.blob = c.call(s, l, l + u, s.type), i.chunkSize = i.blob.size, i.contentRange = "bytes " + l + "-" + (l + i.chunkSize - 1) + "/" + o, r._initXHRData(i), r._initProgressListener(i), n = (r._trigger("chunksend", null, i) !== !1 && t.ajax(i) || r._getXHRPromise(!1, i.context)).done(function(n, s, u) {
                        l = r._getUploadedBytes(u) || l + i.chunkSize, p + i.chunkSize - i._progress.loaded && r._onProgress(t.Event("progress", {
                            lengthComputable: !0,
                            loaded: l - i.uploadedBytes,
                            total: l - i.uploadedBytes
                        }), i), e.uploadedBytes = i.uploadedBytes = l, i.result = n, i.textStatus = s, i.jqXHR = u, r._trigger("chunkdone", null, i), r._trigger("chunkalways", null, i), l < o ? a() : d.resolveWith(i.context, [n, s, u])
                    }).fail(function(t, e, n) {
                        i.jqXHR = t, i.textStatus = e, i.errorThrown = n, r._trigger("chunkfail", null, i), r._trigger("chunkalways", null, i), d.rejectWith(i.context, [t, e, n])
                    })
                }, this._enhancePromise(p), p.abort = function() {
                    return n.abort()
                }, a(), p)))
            },
            _beforeSend: function(t, e) {
                0 === this._active && (this._trigger("start"), this._bitrateTimer = new this._BitrateTimer, this._progress.loaded = this._progress.total = 0, this._progress.bitrate = 0), this._initResponseObject(e), this._initProgressObject(e), e._progress.loaded = e.loaded = e.uploadedBytes || 0, e._progress.total = e.total = this._getTotal(e.files) || 1, e._progress.bitrate = e.bitrate = 0, this._active += 1, this._progress.loaded += e.loaded, this._progress.total += e.total
            },
            _onDone: function(e, i, n, a) {
                var r = a._progress.total,
                    s = a._response;
                a._progress.loaded < r && this._onProgress(t.Event("progress", {
                    lengthComputable: !0,
                    loaded: r,
                    total: r
                }), a), s.result = a.result = e, s.textStatus = a.textStatus = i, s.jqXHR = a.jqXHR = n, this._trigger("done", null, a)
            },
            _onFail: function(t, e, i, n) {
                var a = n._response;
                n.recalculateProgress && (this._progress.loaded -= n._progress.loaded, this._progress.total -= n._progress.total), a.jqXHR = n.jqXHR = t, a.textStatus = n.textStatus = e, a.errorThrown = n.errorThrown = i, this._trigger("fail", null, n)
            },
            _onAlways: function(t, e, i, n) {
                this._trigger("always", null, n)
            },
            _onSend: function(e, i) {
                i.submit || this._addConvenienceMethods(e, i);
                var n, a, r, s, o = this,
                    l = o._getAJAXSettings(i),
                    u = function() {
                        return o._sending += 1, l._bitrateTimer = new o._BitrateTimer, n = n || ((a || o._trigger("send", t.Event("send", {
                            delegatedEvent: e
                        }), l) === !1) && o._getXHRPromise(!1, l.context, a) || o._chunkedUpload(l) || t.ajax(l)).done(function(t, e, i) {
                            o._onDone(t, e, i, l)
                        }).fail(function(t, e, i) {
                            o._onFail(t, e, i, l)
                        }).always(function(t, e, i) {
                            if (o._onAlways(t, e, i, l), o._sending -= 1, o._active -= 1, l.limitConcurrentUploads && l.limitConcurrentUploads > o._sending)
                                for (var n = o._slots.shift(); n;) {
                                    if ("pending" === o._getDeferredState(n)) {
                                        n.resolve();
                                        break
                                    }
                                    n = o._slots.shift()
                                }
                            0 === o._active && o._trigger("stop")
                        })
                    };
                return this._beforeSend(e, l), this.options.sequentialUploads || this.options.limitConcurrentUploads && this.options.limitConcurrentUploads <= this._sending ? (this.options.limitConcurrentUploads > 1 ? (r = t.Deferred(), this._slots.push(r), s = r.pipe(u)) : (this._sequence = this._sequence.pipe(u, u), s = this._sequence), s.abort = function() {
                    return a = [void 0, "abort", "abort"], n ? n.abort() : (r && r.rejectWith(l.context, a), u())
                }, this._enhancePromise(s)) : u()
            },
            _onAdd: function(e, i) {
                var n, a, r, s, o = this,
                    l = !0,
                    u = t.extend({}, this.options, i),
                    c = i.files,
                    d = c.length,
                    p = u.limitMultiFileUploads,
                    f = u.limitMultiFileUploadSize,
                    h = u.limitMultiFileUploadSizeOverhead,
                    m = 0,
                    g = this._getParamName(u),
                    v = 0;
                if (!f || d && void 0 !== c[0].size || (f = void 0), (u.singleFileUploads || p || f) && this._isXHRUpload(u))
                    if (u.singleFileUploads || f || !p)
                        if (!u.singleFileUploads && f)
                            for (r = [], n = [], s = 0; s < d; s += 1) m += c[s].size + h, (s + 1 === d || m + c[s + 1].size + h > f || p && s + 1 - v >= p) && (r.push(c.slice(v, s + 1)), a = g.slice(v, s + 1), a.length || (a = g), n.push(a), v = s + 1, m = 0);
                        else n = g;
                    else
                        for (r = [], n = [], s = 0; s < d; s += p) r.push(c.slice(s, s + p)), a = g.slice(s, s + p), a.length || (a = g), n.push(a);
                else r = [c], n = [g];
                return i.originalFiles = c, t.each(r || c, function(a, s) {
                    var u = t.extend({}, i);
                    return u.files = r ? s : [s], u.paramName = n[a], o._initResponseObject(u), o._initProgressObject(u), o._addConvenienceMethods(e, u), l = o._trigger("add", t.Event("add", {
                        delegatedEvent: e
                    }), u)
                }), l
            },
            _replaceFileInput: function(e) {
                var i = e.fileInput,
                    n = i.clone(!0);
                e.fileInputClone = n, t("<form></form>").append(n)[0].reset(), i.after(n).detach(), t.cleanData(i.unbind("remove")), this.options.fileInput = this.options.fileInput.map(function(t, e) {
                    return e === i[0] ? n[0] : e
                }), i[0] === this.element[0] && (this.element = n)
            },
            _handleFileTreeEntry: function(e, i) {
                var n, a = this,
                    r = t.Deferred(),
                    s = function(t) {
                        t && !t.entry && (t.entry = e), r.resolve([t])
                    },
                    o = function(t) {
                        a._handleFileTreeEntries(t, i + e.name + "/").done(function(t) {
                            r.resolve(t)
                        }).fail(s)
                    },
                    l = function() {
                        n.readEntries(function(t) {
                            t.length ? (u = u.concat(t), l()) : o(u)
                        }, s)
                    },
                    u = [];
                return i = i || "", e.isFile ? e._file ? (e._file.relativePath = i, r.resolve(e._file)) : e.file(function(t) {
                    t.relativePath = i, r.resolve(t)
                }, s) : e.isDirectory ? (n = e.createReader(), l()) : r.resolve([]), r.promise()
            },
            _handleFileTreeEntries: function(e, i) {
                var n = this;
                return t.when.apply(t, t.map(e, function(t) {
                    return n._handleFileTreeEntry(t, i)
                })).pipe(function() {
                    return Array.prototype.concat.apply([], arguments)
                })
            },
            _getDroppedFiles: function(e) {
                e = e || {};
                var i = e.items;
                return i && i.length && (i[0].webkitGetAsEntry || i[0].getAsEntry) ? this._handleFileTreeEntries(t.map(i, function(t) {
                    var e;
                    return t.webkitGetAsEntry ? (e = t.webkitGetAsEntry(), e && (e._file = t.getAsFile()), e) : t.getAsEntry()
                })) : t.Deferred().resolve(t.makeArray(e.files)).promise()
            },
            _getSingleFileInputFiles: function(e) {
                e = t(e);
                var i, n, a = e.prop("webkitEntries") || e.prop("entries");
                if (a && a.length) return this._handleFileTreeEntries(a);
                if (i = t.makeArray(e.prop("files")), i.length) void 0 === i[0].name && i[0].fileName && t.each(i, function(t, e) {
                    e.name = e.fileName, e.size = e.fileSize
                });
                else {
                    if (n = e.prop("value"), !n) return t.Deferred().resolve([]).promise();
                    i = [{
                        name: n.replace(/^.*\\/, "")
                    }]
                }
                return t.Deferred().resolve(i).promise()
            },
            _getFileInputFiles: function(e) {
                return e instanceof t && 1 !== e.length ? t.when.apply(t, t.map(e, this._getSingleFileInputFiles)).pipe(function() {
                    return Array.prototype.concat.apply([], arguments)
                }) : this._getSingleFileInputFiles(e)
            },
            _onChange: function(e) {
                var i = this,
                    n = {
                        fileInput: t(e.target),
                        form: t(e.target.form)
                    };
                this._getFileInputFiles(n.fileInput).always(function(a) {
                    n.files = a, i.options.replaceFileInput && i._replaceFileInput(n), i._trigger("change", t.Event("change", {
                        delegatedEvent: e
                    }), n) !== !1 && i._onAdd(e, n)
                })
            },
            _onPaste: function(e) {
                var i = e.originalEvent && e.originalEvent.clipboardData && e.originalEvent.clipboardData.items,
                    n = {
                        files: []
                    };
                i && i.length && (t.each(i, function(t, e) {
                    var i = e.getAsFile && e.getAsFile();
                    i && n.files.push(i)
                }), this._trigger("paste", t.Event("paste", {
                    delegatedEvent: e
                }), n) !== !1 && this._onAdd(e, n))
            },
            _onDrop: function(e) {
                e.dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
                var i = this,
                    n = e.dataTransfer,
                    a = {};
                n && n.files && n.files.length && (e.preventDefault(), this._getDroppedFiles(n).always(function(n) {
                    a.files = n, i._trigger("drop", t.Event("drop", {
                        delegatedEvent: e
                    }), a) !== !1 && i._onAdd(e, a)
                }))
            },
            _onDragOver: e("dragover"),
            _onDragEnter: e("dragenter"),
            _onDragLeave: e("dragleave"),
            _initEventHandlers: function() {
                this._isXHRUpload(this.options) && (this._on(this.options.dropZone, {
                    dragover: this._onDragOver,
                    drop: this._onDrop,
                    dragenter: this._onDragEnter,
                    dragleave: this._onDragLeave
                }), this._on(this.options.pasteZone, {
                    paste: this._onPaste
                })), t.support.fileInput && this._on(this.options.fileInput, {
                    change: this._onChange
                })
            },
            _destroyEventHandlers: function() {
                this._off(this.options.dropZone, "dragenter dragleave dragover drop"), this._off(this.options.pasteZone, "paste"), this._off(this.options.fileInput, "change")
            },
            _setOption: function(e, i) {
                var n = t.inArray(e, this._specialOptions) !== -1;
                n && this._destroyEventHandlers(), this._super(e, i), n && (this._initSpecialOptions(), this._initEventHandlers())
            },
            _initSpecialOptions: function() {
                var e = this.options;
                void 0 === e.fileInput ? e.fileInput = this.element.is('input[type="file"]') ? this.element : this.element.find('input[type="file"]') : e.fileInput instanceof t || (e.fileInput = t(e.fileInput)), e.dropZone instanceof t || (e.dropZone = t(e.dropZone)), e.pasteZone instanceof t || (e.pasteZone = t(e.pasteZone))
            },
            _getRegExp: function(t) {
                var e = t.split("/"),
                    i = e.pop();
                return e.shift(), new RegExp(e.join("/"), i)
            },
            _isRegExpOption: function(e, i) {
                return "url" !== e && "string" === t.type(i) && /^\/.*\/[igm]{0,3}$/.test(i)
            },
            _initDataAttributes: function() {
                var e = this,
                    i = this.options,
                    n = this.element.data();
                t.each(this.element[0].attributes, function(t, a) {
                    var r, s = a.name.toLowerCase();
                    /^data-/.test(s) && (s = s.slice(5).replace(/-[a-z]/g, function(t) {
                        return t.charAt(1).toUpperCase()
                    }), r = n[s], e._isRegExpOption(s, r) && (r = e._getRegExp(r)), i[s] = r)
                })
            },
            _create: function() {
                this._initDataAttributes(), this._initSpecialOptions(), this._slots = [], this._sequence = this._getXHRPromise(!0), this._sending = this._active = 0, this._initProgressObject(this), this._initEventHandlers()
            },
            active: function() {
                return this._active
            },
            progress: function() {
                return this._progress
            },
            add: function(e) {
                var i = this;
                e && !this.options.disabled && (e.fileInput && !e.files ? this._getFileInputFiles(e.fileInput).always(function(t) {
                    e.files = t, i._onAdd(null, e)
                }) : (e.files = t.makeArray(e.files), this._onAdd(null, e)))
            },
            send: function(e) {
                if (e && !this.options.disabled) {
                    if (e.fileInput && !e.files) {
                        var i, n, a = this,
                            r = t.Deferred(),
                            s = r.promise();
                        return s.abort = function() {
                            return n = !0, i ? i.abort() : (r.reject(null, "abort", "abort"), s)
                        }, this._getFileInputFiles(e.fileInput).always(function(t) {
                            if (!n) {
                                if (!t.length) return void r.reject();
                                e.files = t, i = a._onSend(null, e), i.then(function(t, e, i) {
                                    r.resolve(t, e, i)
                                }, function(t, e, i) {
                                    r.reject(t, e, i)
                                })
                            }
                        }), this._enhancePromise(s)
                    }
                    if (e.files = t.makeArray(e.files), e.files.length) return this._onSend(null, e)
                }
                return this._getXHRPromise(!1, e && e.context)
            }
        })
    }),
    function() {
        var t = [].indexOf || function(t) {
            for (var e = 0, i = this.length; e < i; e++)
                if (e in this && this[e] === t) return e;
            return -1
        };
        define("Ads", ["jquery", "pubsub", "yepnope", "iframe_transport", "fileupload", "flexslider"], function(e, i) {
            var n;
            return n = {
                uploadedFiles: [],
                init: function() {
                    var t;
                    return t = this, e("[data-paging]").uwinPaging({
                        gotoTopPage: function() {
                            return e.scrollTo(".layout__content.news", 800, {
                                offset: -65
                            })
                        },
                        callback: function() {
                            return i.publish("LOAD_PAGE_CONTENT", {})
                        }
                    }), this.initAddForm()
                },
                initAddForm: function() {
                    var i;
                    return i = this, e(document).on(e.modal.OPEN, function(t, i) {
                        var n;
                        return n = e("#add-ads"), n.find("INPUT, SELECT").filter(":first").focus()
                    }), e(document).on(e.modal.BEFORE_OPEN, function(n, a) {
                        function r(t) {
                            var e = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                            return !!t.match(e) && RegExp.$1
                        }

                        function s(t) {
                            var e = /^(\+{0,1}[\(\)\d\s-]+)$/g;
                            return !!t.match(e) && RegExp.$1
                        }
                        var o;
                        e("SELECT").on("change", function(t) {
                            return e(this).parent().nextAll(".error").css("opacity", "0")
                        }), e("INPUT, TEXTAREA").on("keyup", function(i) {
                            var n, a;
                            if (n = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40], a = i.keyCode, !(t.call(n, a) >= 0)) return e(this).nextAll(".error").css("opacity", "0")
                        });
                        var l = 0;
                        e("#add-ads__phone").on("change", function(t) {
                            var i = e(this).val();
                            0 == s(i) && ("" != i && alert("Указан недопустимый номер телефона"), e(this).val(""))
                        }), e("body").on("change", "#add-ads__videourl", function() {
                            var t = e(this).val();
                            console.log(r(t)), r(t) !== !1 ? (0 === l && 0 == need_pay_anyway && alert("Данное поле сделает ваше объявление платным. Стоимость размещения 10 грн."), l = 1) : (alert("Указан неверный адрес видео"), e(this).val(""))
                        }), e("#add-ads").on("submit", function(t) {
                            var n, a, r, s, o, l, u, c;
                            for (n = e(this), n.find(".error").css("opacity", ""), a = n.find("button[type=submit]"), a.attr("disabled", "disabled"), o = "add-ads__", r = new FormData(n[0]), c = i.uploadedFiles, l = 0, u = c.length; l < u; l++) s = c[l], r.append("photo[]=", s);
                            return e.ajax({
                                type: "POST",
                                url: n.attr("action"),
                                data: r,
                                contentType: !1,
                                processData: !1,
                                dataType: "json",
                                success: function(t, n, a) {
                                    i.uploadedFiles = [], e.modal.close();
                                    try {
                                        1 == t.need_pay && e.get(t.pay_form_url, function(t) {
                                            e('<div class="modal">' + t + "</div>").appendTo("body").modal(), addtoCart()
                                        })
                                    } catch (r) {
                                        console.error('Ошибка парсинга JSON в ответе на "отправку объявления" ' + r.error + ":" + r.message + "\n" + r.stack)
                                    }
                                    if (advertId = +t.advert_id, 1 != t.need_pay) return 0 !== e("[data-paging]").length ? e("[data-paging]").uwinPaging().data("plugin_uwinPaging").getPage() : window.location = "/ads/"
                                },
                                error: function(t) {
                                    var i, n, s, l, u;
                                    console.log(t), a.removeAttr("disabled"), r = JSON.parse(t.responseText), l = r.errors, delete r.errors;
                                    for (u in r) n = e("#" + o + r[u].id), s = n.parent(), s.hasClass("selector") && (s = s.parent()), i = s.find(".error"), i.text(r[u].text), i.css("opacity", 1), a.removeAttr("disabled");
                                    if (r) return e("#" + o + r[0].id).focus()
                                }
                            }, t.preventDefault())
                        }), e("#add-ads SELECT").uniform({
                            selectAutoWidth: !1,
                            selectClass: "selector form__selector_size_big"
                        }), o = 0, e(".form__fileinput-files").on("click", ".remove", function(t) {
                            t.preventDefault();
                            var n = e(this).closest(".preview-photo-block").index();
                            [].concat(i.uploadedFiles);
                            i.uploadedFiles.splice(n, 1), e(this).closest(".preview-photo-block").remove()
                        });
                        var u = 0;
                        return e("#fileupload").fileupload({
                            dataType: "json",
                            send: function(t, i) {
                                var n, a;
                                return i.files[0].size > 2097152 ? (n = e(".form__fileinput-error"), n.text(e(t.target).attr("data-error-filesize")), n.css("opacity", "1"), a = setInterval(function() {
                                    return n.css("opacity", "0"), clearInterval(a)
                                }, 5e3), !1) : o >= 10 ? (n = e(".form__fileinput-error"), n.text(e(t.target).attr("data-error-maxfiles")), n.css("opacity", "1"), a = setInterval(function() {
                                    return n.css("opacity", "0"), clearInterval(a)
                                }, 5e3), !1) : o++
                            },
                            progressall: function(t, i) {
                                var n;
                                if (n = parseInt(i.loaded / i.total * 100, 10), e(".form__submit").attr("disabled", "disabled"), e("#upload-progress").css("width", n + "%"), 100 === n) return e(".form__submit").removeAttr("disabled")
                            },
                            error: function() {
                                return e("#upload-progress").css("width", 0)
                            },
                            done: function(t, n) {
                                i.uploadedFiles.push(n.result.file);
                                var a = '<li class="ad__thumbnails-item countUploadedPhotos preview-photo-block">                                          <a target="_blank" class="ad__thumbnail-link" rel="photos" title="" href="">                                            <img class="ad__thumbnail-image" width="50" height="40" src="" alt="">                                            <span class="remove"></span>                                          </a>                                       </li>';
                                e(".modal.current .ad__thumbnails").length ? e(".modal.current .countUploadedPhotos").last().after(a) : e(".modal.current .form__fileinput-files").append('<ul class="ad__thumbnails">' + a + "</ul>");
                                var r;
                                for (r = i.uploadedFiles, j = 0, len = r.length; j < len; j++) {
                                    file = r[j];
                                    var s = e(".ad__thumbnails-item").last().find(".ad__thumbnail-link"),
                                        o = e(".ad__thumbnails-item").last().find(".ad__thumbnail-image");
                                    s.attr("href", "http://s1." + window.location.host + file.split(".com")[2]), o.attr("src", "http://s1." + window.location.host + file.split(".com")[2])
                                }
                                return i.uploadedFiles.length > 5 && 0 === u && 0 == need_pay_anyway && (alert("Размещение больше 5-ти фото сделает ваше объявление платным. Стоимость размещения 10 грн."), u = 1), console.log("here"), e("#upload-progress").css("width", 0)
                            }
                        })
                    })
                },
                initGallery: function(t) {
                    var i;
                    return i = e(t), i.swipebox({
                        hideBarsDelay: 0
                    })
                },
                initBarSlider: function(t) {
                    var i;
                    return i = e(t), i.flexslider({
                        animation: "slide",
                        directionNav: !0,
                        controlNav: !1,
                        animationLoop: !1,
                        itemWidth: 184,
                        slideshow: !1
                    })
                },
                initSlider: function(t) {
                    var i;
                    return i = e(t), i.flexslider({
                        animation: "slide",
                        directionNav: !0,
                        controlNav: !1,
                        animationLoop: !1,
                        itemWidth: 227,
                        itemMargin: 9,
                        slideshow: !0,
                        keyboard: !1,
                        controlsContainer: "#other-adverts__nav"
                    })
                }
            }
        })
    }.call(this), checkAuth();
var advertId;
$(".pay_premium_advert").on("click", function(t) {
    t.preventDefault(), advertId = $(this).attr("id").slice(10), addtoCartandRedirect()
}), $("body, html").on("click", ".jquery-modal.blocker", function() {
    $("#add-ads").length > 0 && $("#add-ads").closest(".modal").remove()
}), $("#adv_edit").on("click", ".remove-preview", function() {
    var t = $(this).closest(".preview-photo-block").index(),
        e = $(".ad__image-wrap .countUploadedPhotos").not(".preview-photo-block").length;
    $(this).closest(".preview-photo-block").remove(), $("#files_urls input").eq(t - e).remove()
}),
    function() {
        define("Slider", ["jquery", "flexslider"], function(t) {
            var e;
            return e = {
                init: function() {
                    var t;
                    return t = this
                },
                initSlider: function(e) {
                    var i, n, a;
                    return i = t(e), n = !1, a = 0, ~~i.attr("data-autoslide") > 0 && (n = !0, a = ~~i.attr("data-autoslide")), i.flexslider({
                        animation: "slide",
                        directionNav: !0,
                        controlNav: !0,
                        animationLoop: !0,
                        slideshow: n,
                        slideshowSpeed: a,
                        itemWidth: 700
                    })
                }
            }
        })
    }.call(this);
var Advcounter = 0,
    initcount = 0,
    linkTextVal = "";
(function() {
    ! function(t, e, i) {
        var n, a, r;
        return r = "uwinPaging", a = {
            url: "page.html",
            pages: 1,
            useFirstLastArrows: !0,
            contentSelector: "#content",
            countLeftRightPages: 6
        }, n = function() {
            function i(e, i) {
                this.element = e, this.$element = t(this.element), this.settings = t.extend({}, a, i), this.$element.attr("data-content") && (this.settings.contentSelector = this.$element.attr("data-content")), this._defaults = a, this._name = r, this._contentEl = t(this.settings.contentSelector), this.$element.attr("data-url") && (this.settings.url = this.$element.attr("data-url")), this.$element.attr("data-pages") && (this.settings.pages = ~~this.$element.attr("data-pages")), this.init()
            }
            return i.prototype.getParamsFromHash = function() {
                var t, e, i, n, a;
                t = location.hash.substr(1), a = [], t && (a = t.split("&")), n = {};
                for (e in a) i = a[e].split("="), n[i[0]] = i[1];
                return n
            }, i.prototype.convertParamsToHash = function(t) {
                var e, i;
                e = "#";
                for (i in t) e += i + "=" + t[i] + "&";
                return e = e.substr(0, e.length - 1)
            }, i.prototype.getPageHash = function(t) {
                var e, i;
                return i = this.getParamsFromHash(), t && (i.page = t), e = this.convertParamsToHash(i)
            }, i.prototype.getCurrentPage = function() {
                return ~~(this.getParamsFromHash().page || 1)
            }, i.prototype.render = function() {
                // createPushToGA();
                var t, e, i, n, a, r, s, o, l, u;
                if (this.settings.pages <= 1) return void this.$element.html("");
                for (e = this.getCurrentPage(), n = e - this.settings.countLeftRightPages / 2, i = 0, n < 1 && (i = n * -1, n = 1, i++), s = ~~e + ~~(this.settings.countLeftRightPages / 2) + ~~i, s > this.settings.pages && (s = this.settings.pages), this.$element.html('<ul class="paging__list"></ul>'), u = this.$element.find("UL"), 1 !== e ? u.append('<li class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(e - 1) + '">Предыдущая</a></li>') : u.append('<li style="visibility: hidden;"class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(e - 1) + '">Предыдущая</a></li>'), a = r = o = n, l = s; o <= l ? r <= l : r >= l; a = o <= l ? ++r : --r) t = ' class="paging__item"', a === e && (t = ' class="paging__item paging__item_type_current"'), u.append("<li" + t + ' data-page="' + a + '"><a class="paging__link" href="' + this.getPageHash(a) + '">' + a + "</a></li>");
                return e !== this.settings.pages ? u.append('<li class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(e + 1) + '">Следующая</a></li>') : u.append('<li style="visibility: hidden;" class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(e + 1) + '">Следующая</a></li>')
            }, i.prototype.getPage = function() {
                var i, n;
                return n = this, i = this.getParamsFromHash(), i.page = this.getCurrentPage(), t.ajax({
                    type: "GET",
                    url: n.settings.url,
                    data: i
                }).done(function(i) {
                    var a, r;
                    if (r = t.parseJSON(i), n._contentEl.html(r.html), n.settings.pages = ~~r.pages, a = n.$element.find(".paging__item_type_current").attr("data-page"), 0 === ~~a && (a = 1), a !== ~~n.getCurrentPage() && (n.settings.gotoTopPage ? t(e).scrollTop() > n._contentEl.offset().top && n.settings.gotoTopPage() : t("html, body").animate({
                            scrollTop: 0
                        }, 300)), n.render(), n.settings.callback instanceof Function) return n.settings.callback.call(this)
                })
            }, i.prototype.init = function() {
                var i;
                return i = this, this.render(), t(e).hashchange(function(t) {
                    return i.getPage()
                }).trigger("hashchange")
            }, i
        }(), t.fn[r] = function(e) {
            return this.each(function() {
                if (!t.data(this, "plugin_" + r)) return t.data(this, "plugin_" + r, new n(this, e))
            })
        }
    }(jQuery, window, document)
}).call(this),
    function() {
        ! function(t, e, i) {
            var n, a, r;
            return r = "adsPaging", a = {
                url: "page.html",
                pages: 1,
                useFirstLastArrows: !0,
                contentSelector: "#content",
                countLeftRightPages: 6
            }, n = function() {
                function i(e, i) {
                    this.element = e, this.$element = t(this.element), this.settings = t.extend({}, a, i), this.$element.attr("data-content") && (this.settings.contentSelector = this.$element.attr("data-content")), this._defaults = a, this._name = r, this._contentEl = t(this.settings.contentSelector), this.$element.attr("data-url") && (this.settings.url = this.$element.attr("data-url")), this.$element.attr("data-pages") && (this.settings.pages = ~~this.$element.attr("data-pages")), this.init()
                }
                return i.prototype.getParamsFromHash = function() {
                    var t, i, n, a, r;
                    if (t = location.hash.substr(1), "" == location.hash.substr(1)) {
                        t = e.location.pathname;
                        var s = t.split("/");
                        t = "page=" + s[2]
                    }
                    r = [], t && (r = t.split("&")), a = {};
                    for (i in r) n = r[i].split("="), a[n[0]] = n[1];
                    return a
                }, i.prototype.convertParamsToHash = function(t) {
                    var e, i;
                    e = "#";
                    for (i in t) e += i + "=" + t[i] + "&";
                    return e = e.substr(0, e.length - 1)
                }, i.prototype.getPageHash = function(t) {
                    var e, i;
                    return i = this.getParamsFromHash(), t && (i.page = t), e = this.convertParamsToHash(i)
                }, i.prototype.getCurrentPage = function() {
                    return ~~(this.getParamsFromHash().page || 1)
                }, i.prototype.render = function() {
                    // createPushToGA();
                    var e, i, n, a, r, s, o, l, u, c;
                    if (0 !== linkTextVal ? t("[data-paging]").find(".paging__link").each(function(e) {
                            t(this).text() == linkTextVal && t(this).click()
                        }) : linkTextVal = this.getCurrentPage(), this.settings.pages <= 1) return void this.$element.html("");
                    for (i = linkTextVal, a = i - this.settings.countLeftRightPages / 2, n = 0, a < 1 && (n = a * -1, a = 1, n++), o = ~~i + ~~(this.settings.countLeftRightPages / 2) + ~~n, o > this.settings.pages && (o = this.settings.pages), this.$element.html('<ul class="paging__list"></ul>'), c = this.$element.find("UL"), 1 !== i ? c.append('<li class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(i - 1) + '">Предыдущая</a></li>') : c.append('<li style="visibility: hidden;"class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(i - 1) + '">Предыдущая</a></li>'), r = s = l = a, u = o; l <= u ? s <= u : s >= u; r = l <= u ? ++s : --s) e = ' class="paging__item"', r == linkTextVal && (e = ' class="paging__item paging__item_type_current"'), r === i && (e = ' class="paging__item paging__item_type_current"'), c.append("<li" + e + ' data-page="' + r + '"><a class="paging__link" href="' + this.getPageHash(r) + '">' + r + "</a></li>");
                    for (i !== this.settings.pages ? c.append('<li class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(i + 1) + '">Следующая</a></li>') : c.append('<li style="visibility: hidden;" class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(i + 1) + '">Следующая</a></li>'), t("#nav-foot[data-paging]").html('<ul class="paging__list"></ul>'), c = t("#nav-foot[data-paging]").find("UL"), 1 !== i ? c.append('<li class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(i - 1) + '">Предыдущая</a></li>') : c.append('<li style="visibility: hidden;"class="paging__item paging__item_type_prev"><a class="paging__link paging__link_type_prev" href="' + this.getPageHash(i - 1) + '">Предыдущая</a></li>'), r = s = l = a, u = o; l <= u ? s <= u : s >= u; r = l <= u ? ++s : --s) e = ' class="paging__item"', r == linkTextVal && (e = ' class="paging__item paging__item_type_current"'), r === i && (e = ' class="paging__item paging__item_type_current"'), c.append("<li" + e + ' data-page="' + r + '"><a class="paging__link" href="' + this.getPageHash(r) + '">' + r + "</a></li>");
                    i !== this.settings.pages ? c.append('<li class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(i + 1) + '">Следующая</a></li>') : c.append('<li style="visibility: hidden;" class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="' + this.getPageHash(i + 1) + '">Следующая</a></li>'), linkTextVal = 0
                }, i.prototype.getPage = function() {
                    var i, n;
                    return n = this, i = this.getParamsFromHash(), 0 !== linkTextVal ? i.page = linkTextVal : i.page = this.getCurrentPage(), t.ajax({
                        type: "GET",
                        url: n.settings.url,
                        data: i
                    }).done(function(i) {
                        var a, r;
                        if (r = t.parseJSON(i), n._contentEl.html(r.html), n.settings.pages = ~~r.pages, a = n.$element.find(".paging__item_type_current").attr("data-page"), 0 === ~~a && (a = 1), a !== ~~n.getCurrentPage() && (n.settings.gotoTopPage ? t(e).scrollTop() > n._contentEl.offset().top && n.settings.gotoTopPage() : t("html, body").animate({
                                scrollTop: 0
                            }, 300)), n.render(), n.settings.callback instanceof Function) return n.settings.callback.call(this)
                    })
                }, i.prototype.init = function() {
                    if (0 == initcount) {
                        initcount = 1;
                        var i;
                        return i = this, this.render(), t(e).hashchange(function(t) {
                            return i.getPage()
                        }).trigger("hashchange")
                    }
                }, i
            }(), t.fn[r] = function(e) {
                return this.each(function() {
                    if (!t.data(this, "plugin_" + r)) return t.data(this, "plugin_" + r, new n(this, e))
                })
            }
        }(jQuery, window, document)
    }.call(this);
var funcStatus = 0;
$(".form__virtual-checkbox").click(function() {
    console.log("go")
    // createPushToGA()
}), define("uwinPaging", function() {}),
    function(t) {
        "function" == typeof define && define.amd ? define("scrollto", ["jquery"], t) : t(jQuery)
    }(function(t) {
        function e(e) {
            return t.isFunction(e) || "object" == typeof e ? e : {
                top: e,
                left: e
            }
        }
        var i = t.scrollTo = function(e, i, n) {
            return t(window).scrollTo(e, i, n)
        };
        return i.defaults = {
            axis: "xy",
            duration: parseFloat(t.fn.jquery) >= 1.3 ? 0 : 1,
            limit: !0
        }, i.window = function(e) {
            return t(window)._scrollable()
        }, t.fn._scrollable = function() {
            return this.map(function() {
                var e = this,
                    i = !e.nodeName || t.inArray(e.nodeName.toLowerCase(), ["iframe", "#document", "html", "body"]) != -1;
                if (!i) return e;
                var n = (e.contentWindow || e).document || e.ownerDocument || e;
                return /webkit/i.test(navigator.userAgent) || "BackCompat" == n.compatMode ? n.body : n.documentElement
            })
        }, t.fn.scrollTo = function(n, a, r) {
            return "object" == typeof a && (r = a, a = 0), "function" == typeof r && (r = {
                onAfter: r
            }), "max" == n && (n = 9e9), r = t.extend({}, i.defaults, r), a = a || r.duration, r.queue = r.queue && r.axis.length > 1, r.queue && (a /= 2), r.offset = e(r.offset), r.over = e(r.over), this._scrollable().each(function() {
                function s(t) {
                    u.animate(d, a, r.easing, t && function() {
                        t.call(this, c, r)
                    })
                }
                if (null != n) {
                    var o, l = this,
                        u = t(l),
                        c = n,
                        d = {},
                        p = u.is("html,body");
                    switch (typeof c) {
                        case "number":
                        case "string":
                            if (/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(c)) {
                                c = e(c);
                                break
                            }
                            if (c = t(c, this), !c.length) return;
                        case "object":
                            (c.is || c.style) && (o = (c = t(c)).offset())
                    }
                    var f = t.isFunction(r.offset) && r.offset(l, c) || r.offset;
                    t.each(r.axis.split(""), function(t, e) {
                        var n = "x" == e ? "Left" : "Top",
                            a = n.toLowerCase(),
                            h = "scroll" + n,
                            m = l[h],
                            g = i.max(l, e);
                        if (o) d[h] = o[a] + (p ? 0 : m - u.offset()[a]), r.margin && (d[h] -= parseInt(c.css("margin" + n)) || 0, d[h] -= parseInt(c.css("border" + n + "Width")) || 0), d[h] += f[a] || 0, r.over[a] && (d[h] += c["x" == e ? "width" : "height"]() * r.over[a]);
                        else {
                            var v = c[a];
                            d[h] = v.slice && "%" == v.slice(-1) ? parseFloat(v) / 100 * g : v
                        }
                        r.limit && /^\d+$/.test(d[h]) && (d[h] = d[h] <= 0 ? 0 : Math.min(d[h], g)), !t && r.queue && (m != d[h] && s(r.onAfterFirst), delete d[h])
                    }), s(r.onAfter)
                }
            }).end()
        }, i.max = function(e, i) {
            var n = "x" == i ? "Width" : "Height",
                a = "scroll" + n;
            if (!t(e).is("html,body")) return e[a] - t(e)[n.toLowerCase()]();
            var r = "client" + n,
                s = e.ownerDocument.documentElement,
                o = e.ownerDocument.body;
            return Math.max(s[a], o[a]) - Math.min(s[r], o[r])
        }, i
    }), ! function(t, e, i) {
    function n(t) {
        var e = Array.prototype.slice.call(arguments, 1);
        return t.prop ? t.prop.apply(t, e) : t.attr.apply(t, e)
    }

    function a(t, e, i) {
        var n, a;
        for (n in i) i.hasOwnProperty(n) && (a = n.replace(/ |$/g, e.eventNamespace), t.bind(a, i[n]))
    }

    function r(t, e, i) {
        a(t, i, {
            focus: function() {
                e.addClass(i.focusClass)
            },
            blur: function() {
                e.removeClass(i.focusClass), e.removeClass(i.activeClass)
            },
            mouseenter: function() {
                e.addClass(i.hoverClass)
            },
            mouseleave: function() {
                e.removeClass(i.hoverClass), e.removeClass(i.activeClass)
            },
            "mousedown touchbegin": function() {
                t.is(":disabled") || e.addClass(i.activeClass)
            },
            "mouseup touchend": function() {
                e.removeClass(i.activeClass)
            }
        })
    }

    function s(t, e) {
        t.removeClass(e.hoverClass + " " + e.focusClass + " " + e.activeClass)
    }

    function o(t, e, i) {
        i ? t.addClass(e) : t.removeClass(e)
    }

    function l(t, e, i) {
        var n = "checked",
            a = e.is(":" + n);
        e.prop ? e.prop(n, a) : a ? e.attr(n, n) : e.removeAttr(n), o(t, i.checkedClass, a)
    }

    function u(t, e, i) {
        o(t, i.disabledClass, e.is(":disabled"))
    }

    function c(t, e, i) {
        switch (i) {
            case "after":
                return t.after(e), t.next();
            case "before":
                return t.before(e), t.prev();
            case "wrap":
                return t.wrap(e), t.parent()
        }
        return null
    }

    function d(t, i, a) {
        var r, s, o;
        return a || (a = {}), a = e.extend({
            bind: {},
            divClass: null,
            divWrap: "wrap",
            spanClass: null,
            spanHtml: null,
            spanWrap: "wrap"
        }, a), r = e("<div />"), s = e("<span />"), i.autoHide && t.is(":hidden") && "none" === t.css("display") && r.hide(), a.divClass && r.addClass(a.divClass), i.wrapperClass && r.addClass(i.wrapperClass), a.spanClass && s.addClass(a.spanClass), o = n(t, "id"), i.useID && o && n(r, "id", i.idPrefix + "-" + o), a.spanHtml && s.html(a.spanHtml), r = c(t, r, a.divWrap), s = c(t, s, a.spanWrap), u(r, t, i), {
            div: r,
            span: s
        }
    }

    function p(t, i) {
        var n;
        return i.wrapperClass ? (n = e("<span />").addClass(i.wrapperClass), n = c(t, n, "wrap")) : null
    }

    function f() {
        var i, n, a, r;
        return r = "rgb(120,2,153)", n = e('<div style="width:0;height:0;color:' + r + '">'), e("body").append(n), a = n.get(0), i = t.getComputedStyle ? t.getComputedStyle(a, "").color : (a.currentStyle || a.style || {}).color, n.remove(), i.replace(/ /g, "") !== r
    }

    function h(t) {
        return t ? e("<span />").text(t).html() : ""
    }

    function m() {
        return navigator.cpuClass && !navigator.product
    }

    function g() {
        return void 0 !== t.XMLHttpRequest
    }

    function v(t) {
        var e;
        return !!t[0].multiple || (e = n(t, "size"), !(!e || 1 >= e))
    }

    function _() {
        return !1
    }

    function b(t, e) {
        var i = "none";
        a(t, e, {
            "selectstart dragstart mousedown": _
        }), t.css({
            MozUserSelect: i,
            msUserSelect: i,
            webkitUserSelect: i,
            userSelect: i
        })
    }

    function y(t, e, i) {
        var n = t.val();
        "" === n ? n = i.fileDefaultHtml : (n = n.split(/[\/\\]+/), n = n[n.length - 1]), e.text(n)
    }

    function w(t, e, i) {
        var n, a;
        for (n = [], t.each(function() {
            var t;
            for (t in e) Object.prototype.hasOwnProperty.call(e, t) && (n.push({
                el: this,
                name: t,
                old: this.style[t]
            }), this.style[t] = e[t])
        }), i(); n.length;) a = n.pop(), a.el.style[a.name] = a.old
    }

    function x(t, e) {
        var i;
        i = t.parents(), i.push(t[0]), i = i.not(":visible"), w(i, {
            visibility: "hidden",
            display: "block",
            position: "absolute"
        }, e)
    }

    function k(t, e) {
        return function() {
            t.unwrap().unwrap().unbind(e.eventNamespace)
        }
    }
    var C = !0,
        T = !1,
        S = [{
            match: function(t) {
                return t.is("a, button, :submit, :reset, input[type='button']")
            },
            apply: function(e, i) {
                var o, l, c, p, f;
                return l = i.submitDefaultHtml, e.is(":reset") && (l = i.resetDefaultHtml), p = e.is("a, button") ? function() {
                    return e.html() || l
                } : function() {
                    return h(n(e, "value")) || l
                }, c = d(e, i, {
                    divClass: i.buttonClass,
                    spanHtml: p()
                }), o = c.div, r(e, o, i), f = !1, a(o, i, {
                    "click touchend": function() {
                        var i, a, r, s;
                        f || e.is(":disabled") || (f = !0, e[0].dispatchEvent ? (i = document.createEvent("MouseEvents"), i.initEvent("click", !0, !0), a = e[0].dispatchEvent(i), e.is("a") && a && (r = n(e, "target"), s = n(e, "href"), r && "_self" !== r ? t.open(s, r) : document.location.href = s)) : e.click(), f = !1)
                    }
                }), b(o, i), {
                    remove: function() {
                        return o.after(e), o.remove(), e.unbind(i.eventNamespace), e
                    },
                    update: function() {
                        s(o, i), u(o, e, i), e.detach(), c.span.html(p()).append(e)
                    }
                }
            }
        }, {
            match: function(t) {
                return t.is(":checkbox")
            },
            apply: function(t, e) {
                var i, n, o;
                return i = d(t, e, {
                    divClass: e.checkboxClass
                }), n = i.div, o = i.span, r(t, n, e), a(t, e, {
                    "click touchend": function() {
                        l(o, t, e)
                    }
                }), l(o, t, e), {
                    remove: k(t, e),
                    update: function() {
                        s(n, e), o.removeClass(e.checkedClass), l(o, t, e), u(n, t, e)
                    }
                }
            }
        }, {
            match: function(t) {
                return t.is(":file")
            },
            apply: function(t, i) {
                function o() {
                    y(t, f, i)
                }
                var l, p, f, h;
                return l = d(t, i, {
                    divClass: i.fileClass,
                    spanClass: i.fileButtonClass,
                    spanHtml: i.fileButtonHtml,
                    spanWrap: "after"
                }), p = l.div, h = l.span, f = e("<span />").html(i.fileDefaultHtml), f.addClass(i.filenameClass), f = c(t, f, "after"), n(t, "size") || n(t, "size", p.width() / 10), r(t, p, i), o(), m() ? a(t, i, {
                    click: function() {
                        t.trigger("change"), setTimeout(o, 0)
                    }
                }) : a(t, i, {
                    change: o
                }), b(f, i), b(h, i), {
                    remove: function() {
                        return f.remove(), h.remove(), t.unwrap().unbind(i.eventNamespace)
                    },
                    update: function() {
                        s(p, i), y(t, f, i), u(p, t, i)
                    }
                }
            }
        }, {
            match: function(t) {
                if (t.is("input")) {
                    var e = (" " + n(t, "type") + " ").toLowerCase(),
                        i = " color date datetime datetime-local email month number password search tel text time url week ";
                    return i.indexOf(e) >= 0
                }
                return !1
            },
            apply: function(t, e) {
                var i, a;
                return i = n(t, "type"), t.addClass(e.inputClass), a = p(t, e), r(t, t, e), e.inputAddTypeAsClass && t.addClass(i), {
                    remove: function() {
                        t.removeClass(e.inputClass), e.inputAddTypeAsClass && t.removeClass(i), a && t.unwrap()
                    },
                    update: _
                }
            }
        }, {
            match: function(t) {
                return t.is(":radio")
            },
            apply: function(t, i) {
                var o, c, p;
                return o = d(t, i, {
                    divClass: i.radioClass
                }), c = o.div, p = o.span, r(t, c, i), a(t, i, {
                    "click touchend": function() {
                        e.uniform.update(e(':radio[name="' + n(t, "name") + '"]'))
                    }
                }), l(p, t, i), {
                    remove: k(t, i),
                    update: function() {
                        s(c, i), l(p, t, i), u(c, t, i)
                    }
                }
            }
        }, {
            match: function(t) {
                return !(!t.is("select") || v(t))
            },
            apply: function(t, i) {
                var n, o, l, c;
                return i.selectAutoWidth && x(t, function() {
                    c = t.width()
                }), n = d(t, i, {
                    divClass: i.selectClass,
                    spanHtml: (t.find(":selected:first") || t.find("option:first")).html(),
                    spanWrap: "before"
                }), o = n.div, l = n.span, i.selectAutoWidth ? x(t, function() {
                    w(e([l[0], o[0]]), {
                        display: "block"
                    }, function() {
                        var t;
                        t = l.outerWidth() - l.width(), o.width(c + t), l.width(c)
                    })
                }) : o.addClass("fixedWidth"), r(t, o, i), a(t, i, {
                    change: function() {
                        l.html(t.find(":selected").html()), o.removeClass(i.activeClass)
                    },
                    "click touchend": function() {
                        var e = t.find(":selected").html();
                        l.html() !== e && t.trigger("change")
                    },
                    keyup: function() {
                        l.html(t.find(":selected").html())
                    }
                }), b(l, i), {
                    remove: function() {
                        return l.remove(), t.unwrap().unbind(i.eventNamespace), t
                    },
                    update: function() {
                        i.selectAutoWidth ? (e.uniform.restore(t), t.uniform(i)) : (s(o, i), l.html(t.find(":selected").html()), u(o, t, i))
                    }
                }
            }
        }, {
            match: function(t) {
                return !(!t.is("select") || !v(t))
            },
            apply: function(t, e) {
                var i;
                return t.addClass(e.selectMultiClass), i = p(t, e), r(t, t, e), {
                    remove: function() {
                        t.removeClass(e.selectMultiClass), i && t.unwrap()
                    },
                    update: _
                }
            }
        }, {
            match: function(t) {
                return t.is("textarea")
            },
            apply: function(t, e) {
                var i;
                return t.addClass(e.textareaClass), i = p(t, e), r(t, t, e), {
                    remove: function() {
                        t.removeClass(e.textareaClass), i && t.unwrap()
                    },
                    update: _
                }
            }
        }];
    m() && !g() && (C = !1), e.uniform = {
        defaults: {
            activeClass: "active",
            autoHide: !0,
            buttonClass: "button",
            checkboxClass: "checker",
            checkedClass: "checked",
            disabledClass: "disabled",
            eventNamespace: ".uniform",
            fileButtonClass: "action",
            fileButtonHtml: "Choose File",
            fileClass: "uploader",
            fileDefaultHtml: "No file selected",
            filenameClass: "filename",
            focusClass: "focus",
            hoverClass: "hover",
            idPrefix: "uniform",
            inputAddTypeAsClass: !0,
            inputClass: "uniform-input",
            radioClass: "radio",
            resetDefaultHtml: "Reset",
            resetSelector: !1,
            selectAutoWidth: !0,
            selectClass: "selector",
            selectMultiClass: "uniform-multiselect",
            submitDefaultHtml: "Submit",
            textareaClass: "uniform",
            useID: !0,
            wrapperClass: null
        },
        elements: []
    }, e.fn.uniform = function(i) {
        var n = this;
        return i = e.extend({}, e.uniform.defaults, i), T || (T = !0, f() && (C = !1)), C ? (i.resetSelector && e(i.resetSelector).mouseup(function() {
            t.setTimeout(function() {
                e.uniform.update(n)
            }, 10)
        }), this.each(function() {
            var t, n, a, r = e(this);
            if (r.data("uniformed")) return void e.uniform.update(r);
            for (t = 0; t < S.length; t += 1)
                if (n = S[t], n.match(r, i)) return a = n.apply(r, i), r.data("uniformed", a), void e.uniform.elements.push(r.get(0))
        })) : this
    }, e.uniform.restore = e.fn.uniform.restore = function(t) {
        t === i && (t = e.uniform.elements), e(t).each(function() {
            var t, i, n = e(this);
            i = n.data("uniformed"), i && (i.remove(), t = e.inArray(this, e.uniform.elements), t >= 0 && e.uniform.elements.splice(t, 1), n.removeData("uniformed"))
        })
    }, e.uniform.update = e.fn.uniform.update = function(t) {
        t === i && (t = e.uniform.elements), e(t).each(function() {
            var t, i = e(this);
            t = i.data("uniformed"), t && t.update(i, t.options)
        })
    }
}(this, jQuery), define("uniform", function() {}),
    function(t, e, i, n) {
        i.swipebox = function(a, r) {
            var s = {
                    useCSS: !0,
                    initialIndexOnArray: 0,
                    hideBarsDelay: 3e3,
                    videoMaxWidth: 1140,
                    vimeoColor: "CCCCCC",
                    useInnerWrap: !1,
                    beforeOpen: null,
                    afterClose: null
                },
                o = this,
                l = [],
                a = a,
                u = a.selector,
                c = i(u),
                d = e.createTouch !== n || "ontouchstart" in t || "onmsgesturechange" in t || navigator.msMaxTouchPoints,
                p = !!t.SVGSVGElement,
                f = t.innerWidth ? t.innerWidth : i(t).width(),
                h = t.innerHeight ? t.innerHeight : i(t).height(),
                m = "";
            i.extend({}, s, r).useInnerWrap === !0 && (m = ' class="swipebox-overlay_type_use-inner"');
            var g = '<div id="swipebox-overlay"' + m + '>        <div id="swipebox-slider"></div>        <div id="swipebox-caption"></div>        <div id="swipebox-action">          <a id="swipebox-close"></a>          <a id="swipebox-prev"></a>          <a id="swipebox-next"></a>        </div>    </div>';
            o.settings = {}, o.init = function() {
                o.settings = i.extend({}, s, r), i.isArray(a) ? (l = a, v.target = i(t), v.init(o.settings.initialIndexOnArray)) : c.click(function(t) {
                    l = [];
                    var e, n, a;
                    a || (n = "rel", a = i(this).attr(n)), a && "" !== a && "nofollow" !== a ? $elem = c.filter("[" + n + '="' + a + '"]') : $elem = i(u), $elem.each(function() {
                        var t = null,
                            e = null;
                        i(this).attr("title") && (t = i(this).attr("title")), i(this).attr("href") && (e = i(this).attr("href")), l.push({
                            href: e,
                            title: t
                        })
                    }), e = $elem.index(i(this)), t.preventDefault(), t.stopPropagation(), v.target = i(t.target), v.init(e)
                })
            }, o.refresh = function() {
                i.isArray(a) || (v.destroy(), $elem = i(u), v.actions())
            };
            var v = {
                init: function(t) {
                    o.settings.beforeOpen && o.settings.beforeOpen(), this.target.trigger("swipebox-start"), i.swipebox.isOpen = !0, this.build(), this.openSlide(t), this.openMedia(t), this.preloadMedia(t + 1), this.preloadMedia(t - 1)
                },
                build: function() {
                    var t = this;
                    if (i("body").append(g), t.doCssTrans() && (i("#swipebox-slider").css({
                            "-webkit-transition": "left 0.4s ease",
                            "-moz-transition": "left 0.4s ease",
                            "-o-transition": "left 0.4s ease",
                            "-khtml-transition": "left 0.4s ease",
                            transition: "left 0.4s ease"
                        }), i("#swipebox-overlay").css({
                            "-webkit-transition": "opacity 1s ease",
                            "-moz-transition": "opacity 1s ease",
                            "-o-transition": "opacity 1s ease",
                            "-khtml-transition": "opacity 1s ease",
                            transition: "opacity 1s ease"
                        }), i("#swipebox-action, #swipebox-caption").css({
                            "-webkit-transition": "0.5s",
                            "-moz-transition": "0.5s",
                            "-o-transition": "0.5s",
                            "-khtml-transition": "0.5s",
                            transition: "0.5s"
                        })), p) {
                        var e = i("#swipebox-action #swipebox-close").css("background-image");
                        e = e.replace("png", "svg"), i("#swipebox-action #swipebox-prev,#swipebox-action #swipebox-next,#swipebox-action #swipebox-close").css({
                            "background-image": e
                        })
                    }
                    i.each(l, function() {
                        o.settings.useInnerWrap === !0 ? i("#swipebox-slider").append('<div class="slide"><div class="slide-inner"></div></div>') : i("#swipebox-slider").append('<div class="slide"></div>')
                    }), t.setDim(), t.actions(), t.keyboard(), t.gesture(), t.animBars(), t.resize()
                },
                setDim: function() {
                    var e, n, a = {};
                    "onorientationchange" in t ? t.addEventListener("orientationchange", function() {
                        0 == t.orientation ? (e = f, n = h) : 90 != t.orientation && t.orientation != -90 || (e = h, n = f)
                    }, !1) : (e = t.innerWidth ? t.innerWidth : i(t).width(), n = t.innerHeight ? t.innerHeight : i(t).height()), a = {
                        width: e,
                        height: n
                    }, i("#swipebox-overlay").css(a)
                },
                resize: function() {
                    var e = this;
                    i(t).resize(function() {
                        e.setDim()
                    }).resize()
                },
                supportTransition: function() {
                    for (var t = "transition WebkitTransition MozTransition OTransition msTransition KhtmlTransition".split(" "), i = 0; i < t.length; i++)
                        if (e.createElement("div").style[t[i]] !== n) return t[i];
                    return !1
                },
                doCssTrans: function() {
                    if (o.settings.useCSS && this.supportTransition()) return !0
                },
                gesture: function() {
                    if (d) {
                        var t = this,
                            e = null,
                            n = 10,
                            a = {},
                            r = {},
                            s = i("#swipebox-caption, #swipebox-action");
                        s.addClass("visible-bars"), t.setTimeout(), i("body").bind("touchstart", function(t) {
                            return i(this).addClass("touching"), r = t.originalEvent.targetTouches[0], a.pageX = t.originalEvent.targetTouches[0].pageX, i(".touching").bind("touchmove", function(t) {
                                t.preventDefault(), t.stopPropagation(), r = t.originalEvent.targetTouches[0]
                            }), !1
                        }).bind("touchend", function(o) {
                            o.preventDefault(), o.stopPropagation(), e = r.pageX - a.pageX, e >= n ? t.getPrev() : e <= -n ? t.getNext() : s.hasClass("visible-bars") ? (t.clearTimeout(), t.hideBars()) : (t.showBars(), t.setTimeout()), i(".touching").off("touchmove").removeClass("touching")
                        })
                    }
                },
                setTimeout: function() {
                    if (o.settings.hideBarsDelay > 0) {
                        var e = this;
                        e.clearTimeout(), e.timeout = t.setTimeout(function() {
                            e.hideBars()
                        }, o.settings.hideBarsDelay)
                    }
                },
                clearTimeout: function() {
                    t.clearTimeout(this.timeout), this.timeout = null
                },
                showBars: function() {
                    var t = i("#swipebox-caption, #swipebox-action");
                    this.doCssTrans() ? t.addClass("visible-bars") : (i("#swipebox-caption").animate({
                        top: 0
                    }, 500), i("#swipebox-action").animate({
                        bottom: 0
                    }, 500), setTimeout(function() {
                        t.addClass("visible-bars")
                    }, 1e3))
                },
                hideBars: function() {
                    var t = i("#swipebox-caption, #swipebox-action");
                    this.doCssTrans() ? t.removeClass("visible-bars") : (i("#swipebox-caption").animate({
                        top: "-50px"
                    }, 500), i("#swipebox-action").animate({
                        bottom: "-50px"
                    }, 500), setTimeout(function() {
                        t.removeClass("visible-bars")
                    }, 1e3))
                },
                animBars: function() {
                    var t = this,
                        e = i("#swipebox-caption, #swipebox-action");
                    e.addClass("visible-bars"), t.setTimeout(), i("#swipebox-slider").click(function(i) {
                        e.hasClass("visible-bars") || (t.showBars(), t.setTimeout())
                    }), i("#swipebox-action").hover(function() {
                        t.showBars(), e.addClass("force-visible-bars"), t.clearTimeout()
                    }, function() {
                        e.removeClass("force-visible-bars"), t.setTimeout()
                    })
                },
                keyboard: function() {
                    var e = this;
                    i(t).bind("keyup", function(t) {
                        t.preventDefault(), t.stopPropagation(), 37 == t.keyCode ? e.getPrev() : 39 == t.keyCode ? e.getNext() : 27 == t.keyCode && e.closeSlide()
                    })
                },
                actions: function() {
                    var t = this;
                    l.length < 2 ? i("#swipebox-prev, #swipebox-next").hide() : (i("#swipebox-prev").bind("click touchend", function(e) {
                        e.preventDefault(), e.stopPropagation(), t.getPrev(), t.setTimeout()
                    }), i("#swipebox-next").bind("click touchend", function(e) {
                        e.preventDefault(), e.stopPropagation(), t.getNext(), t.setTimeout()
                    })), i("#swipebox-close").bind("click touchend", function(e) {
                        t.closeSlide()
                    })
                },
                setSlide: function(t, e) {
                    e = e || !1;
                    var n = i("#swipebox-slider");
                    this.doCssTrans() ? n.css({
                        left: 100 * -t + "%"
                    }) : n.animate({
                        left: 100 * -t + "%"
                    }), i("#swipebox-slider .slide").removeClass("current"), i("#swipebox-slider .slide").eq(t).addClass("current"), this.setTitle(t), e && n.fadeIn(), i("#swipebox-prev, #swipebox-next").removeClass("disabled"), 0 == t ? i("#swipebox-prev").addClass("disabled") : t == l.length - 1 && i("#swipebox-next").addClass("disabled")
                },
                openSlide: function(e) {
                    i("html").addClass("swipebox"), i(t).trigger("resize"), this.setSlide(e, !0)
                },
                preloadMedia: function(t) {
                    var e = this,
                        i = null;
                    l[t] !== n && (i = l[t].href), e.isVideo(i) ? e.openMedia(t) : setTimeout(function() {
                        e.openMedia(t)
                    }, 1e3)
                },
                openMedia: function(t) {
                    var e = this,
                        a = null;
                    return l[t] !== n && (a = l[t].href), !(t < 0 || t >= l.length) && void(e.isVideo(a) ? i("#swipebox-slider .slide").eq(t).html(e.getVideo(a)) : e.loadMedia(a, function() {
                        o.settings.useInnerWrap === !0 ? i("#swipebox-slider .slide").eq(t).find(".slide-inner").html(this) : i("#swipebox-slider .slide").eq(t).html(this)
                    }))
                },
                setTitle: function(t, e) {
                    var a = null;
                    i("#swipebox-caption").empty(), l[t] !== n && (a = l[t].title), a && i("#swipebox-caption").append(a)
                },
                isVideo: function(t) {
                    if (t && (t.match(/youtube\.com\/watch\?v=([a-zA-Z0-9\-_]+)/) || t.match(/vimeo\.com\/([0-9]*)/))) return !0
                },
                getVideo: function(t) {
                    var e = "",
                        i = t.match(/watch\?v=([a-zA-Z0-9\-_]+)/),
                        n = t.match(/vimeo\.com\/([0-9]*)/);
                    return i ? e = '<iframe width="560" height="315" src="//www.youtube.com/embed/' + i[1] + '" frameborder="0" allowfullscreen></iframe>' : n && (e = '<iframe width="560" height="315"  src="http://player.vimeo.com/video/' + n[1] + "?byline=0&amp;portrait=0&amp;color=" + o.settings.vimeoColor + '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'), '<div class="swipebox-video-container" style="max-width:' + o.settings.videomaxWidth + 'px"><div class="swipebox-video">' + e + "</div></div>"
                },
                loadMedia: function(t, e) {
                    if (!this.isVideo(t)) {
                        var n = i("<img>").on("load", function() {
                            e.call(n)
                        });
                        n.attr("src", t)
                    }
                },
                getNext: function() {
                    var t = this;
                    index = i("#swipebox-slider .slide").index(i("#swipebox-slider .slide.current")), index + 1 < l.length ? (index++, t.setSlide(index), t.preloadMedia(index + 1)) : (i("#swipebox-slider").addClass("rightSpring"), setTimeout(function() {
                        i("#swipebox-slider").removeClass("rightSpring")
                    }, 500))
                },
                getPrev: function() {
                    index = i("#swipebox-slider .slide").index(i("#swipebox-slider .slide.current")), index > 0 ? (index--, this.setSlide(index), this.preloadMedia(index - 1)) : (i("#swipebox-slider").addClass("leftSpring"), setTimeout(function() {
                        i("#swipebox-slider").removeClass("leftSpring")
                    }, 500))
                },
                closeSlide: function() {
                    i("html").removeClass("swipebox"), i(t).trigger("resize"), this.destroy()
                },
                destroy: function() {
                    i(t).unbind("keyup"), i("body").unbind("touchstart"), i("body").unbind("touchmove"), i("body").unbind("touchend"), i("#swipebox-slider").unbind(), i("#swipebox-overlay").remove(), i.isArray(a) || a.removeData("_swipebox"), this.target && this.target.trigger("swipebox-destroy"), i.swipebox.isOpen = !1, o.settings.afterClose && o.settings.afterClose()
                }
            };
            o.init()
        }, i.fn.swipebox = function(t) {
            if (!i.data(this, "_swipebox")) {
                var e = new i.swipebox(this, t);
                this.data("_swipebox", e)
            }
            return this.data("_swipebox")
        }
    }(window, document, jQuery), define("swipebox", function() {}),
    function(t) {
        "function" == typeof define && define.amd ? define("powertip", ["jquery"], t) : t(jQuery)
    }(function(t) {
        function e() {
            var e = this;
            e.top = "auto", e.left = "auto", e.right = "auto", e.bottom = "auto", e.set = function(i, n) {
                t.isNumeric(n) && (e[i] = Math.round(n))
            }
        }

        function i(t, e, i) {
            function n(n, a) {
                s(), t.data(g) || (n ? (a && t.data(v, !0), i.showTip(t)) : (T.tipOpenImminent = !0, l = setTimeout(function() {
                    l = null, r()
                }, e.intentPollInterval)))
            }

            function a(n) {
                s(), T.tipOpenImminent = !1, t.data(g) && (t.data(v, !1), n ? i.hideTip(t) : (T.delayInProgress = !0, l = setTimeout(function() {
                    l = null, i.hideTip(t), T.delayInProgress = !1
                }, e.closeDelay)))
            }

            function r() {
                var a = Math.abs(T.previousX - T.currentX),
                    r = Math.abs(T.previousY - T.currentY),
                    s = a + r;
                s < e.intentSensitivity ? i.showTip(t) : (T.previousX = T.currentX, T.previousY = T.currentY,
                    n())
            }

            function s() {
                l = clearTimeout(l), T.delayInProgress = !1
            }

            function o() {
                i.resetPosition(t)
            }
            var l = null;
            this.show = n, this.hide = a, this.cancel = s, this.resetPosition = o
        }

        function n() {
            function t(t, a, s, o, l) {
                var u, c = a.split("-")[0],
                    d = new e;
                switch (u = r(t) ? n(t, c) : i(t, c), a) {
                    case "n":
                        d.set("left", u.left - s / 2), d.set("bottom", T.windowHeight - u.top + l);
                        break;
                    case "e":
                        d.set("left", u.left + l), d.set("top", u.top - o / 2);
                        break;
                    case "s":
                        d.set("left", u.left - s / 2), d.set("top", u.top + l);
                        break;
                    case "w":
                        d.set("top", u.top - o / 2), d.set("right", T.windowWidth - u.left + l);
                        break;
                    case "nw":
                        d.set("bottom", T.windowHeight - u.top + l), d.set("right", T.windowWidth - u.left - 20);
                        break;
                    case "nw-alt":
                        d.set("left", u.left), d.set("bottom", T.windowHeight - u.top + l);
                        break;
                    case "ne":
                        d.set("left", u.left - 20), d.set("bottom", T.windowHeight - u.top + l);
                        break;
                    case "ne-alt":
                        d.set("bottom", T.windowHeight - u.top + l), d.set("right", T.windowWidth - u.left);
                        break;
                    case "sw":
                        d.set("top", u.top + l), d.set("right", T.windowWidth - u.left - 20);
                        break;
                    case "sw-alt":
                        d.set("left", u.left), d.set("top", u.top + l);
                        break;
                    case "se":
                        d.set("left", u.left - 20), d.set("top", u.top + l);
                        break;
                    case "se-alt":
                        d.set("top", u.top + l), d.set("right", T.windowWidth - u.left)
                }
                return d
            }

            function i(t, e) {
                var i, n, a = t.offset(),
                    r = t.outerWidth(),
                    s = t.outerHeight();
                switch (e) {
                    case "n":
                        i = a.left + r / 2, n = a.top;
                        break;
                    case "e":
                        i = a.left + r, n = a.top + s / 2;
                        break;
                    case "s":
                        i = a.left + r / 2, n = a.top + s;
                        break;
                    case "w":
                        i = a.left, n = a.top + s / 2;
                        break;
                    case "nw":
                        i = a.left, n = a.top;
                        break;
                    case "ne":
                        i = a.left + r, n = a.top;
                        break;
                    case "sw":
                        i = a.left, n = a.top + s;
                        break;
                    case "se":
                        i = a.left + r, n = a.top + s
                }
                return {
                    top: n,
                    left: i
                }
            }

            function n(t, e) {
                function i() {
                    h.push(u.matrixTransform(d))
                }
                var n, a, r, s, o = t.closest("svg")[0],
                    l = t[0],
                    u = o.createSVGPoint(),
                    c = l.getBBox(),
                    d = l.getScreenCTM(),
                    p = c.width / 2,
                    f = c.height / 2,
                    h = [],
                    m = ["nw", "n", "ne", "e", "se", "s", "sw", "w"];
                if (u.x = c.x, u.y = c.y, i(), u.x += p, i(), u.x += p, i(), u.y += f, i(), u.y += f, i(), u.x -= p, i(), u.x -= p, i(), u.y -= f, i(), h[0].y !== h[1].y || h[0].x !== h[7].x)
                    for (a = Math.atan2(d.b, d.a) * C, r = Math.ceil((a % 360 - 22.5) / 45), r < 1 && (r += 8); r--;) m.push(m.shift());
                for (s = 0; s < h.length; s++)
                    if (m[s] === e) {
                        n = h[s];
                        break
                    }
                return {
                    top: n.y + T.scrollTop,
                    left: n.x + T.scrollLeft
                }
            }
            this.compute = t
        }

        function a(i) {
            function a(t) {
                t.data(g, !0), C.queue(function(e) {
                    r(t), e()
                })
            }

            function r(t) {
                var e;
                if (t.data(g)) {
                    if (T.isTipOpen) return T.isClosing || s(T.activeHover), void C.delay(100).queue(function(e) {
                        r(t), e()
                    });
                    t.trigger("powerTipPreRender"), e = u(t), e && (C.empty().append(e), t.trigger("powerTipRender"), T.activeHover = t, T.isTipOpen = !0, C.data(b, i.mouseOnToPopup), i.followMouse ? o() : (y(t), T.isFixedTipOpen = !0), C.fadeIn(i.fadeInTime, function() {
                        T.desyncTimeout || (T.desyncTimeout = setInterval(x, 500)), t.trigger("powerTipOpen")
                    }))
                }
            }

            function s(t) {
                T.isClosing = !0, T.activeHover = null, T.isTipOpen = !1, T.desyncTimeout = clearInterval(T.desyncTimeout), t.data(g, !1), t.data(v, !1), C.fadeOut(i.fadeOutTime, function() {
                    var n = new e;
                    T.isClosing = !1, T.isFixedTipOpen = !1, C.removeClass(), n.set("top", T.currentY + i.offset), n.set("left", T.currentX + i.offset), C.css(n), t.trigger("powerTipClose")
                })
            }

            function o() {
                if (!T.isFixedTipOpen && (T.isTipOpen || T.tipOpenImminent && C.data(_))) {
                    var t, n, a = C.outerWidth(),
                        r = C.outerHeight(),
                        s = new e;
                    s.set("top", T.currentY + i.offset), s.set("left", T.currentX + i.offset), t = c(s, a, r), t !== S.none && (n = d(t), 1 === n ? t === S.right ? s.set("left", T.windowWidth - a) : t === S.bottom && s.set("top", T.scrollTop + T.windowHeight - r) : (s.set("left", T.currentX - a - i.offset), s.set("top", T.currentY - r - i.offset))), C.css(s)
                }
            }

            function y(e) {
                var n, a;
                i.smartPlacement ? (n = t.fn.powerTip.smartPlacementLists[i.placement], t.each(n, function(t, i) {
                    var n = c(w(e, i), C.outerWidth(), C.outerHeight());
                    if (a = i, n === S.none) return !1
                })) : (w(e, i.placement), a = i.placement), C.addClass(a)
            }

            function w(t, n) {
                var a, r, s = 0,
                    o = new e;
                o.set("top", 0), o.set("left", 0), C.css(o);
                do a = C.outerWidth(), r = C.outerHeight(), o = k.compute(t, n, a, r, i.offset), C.css(o); while (++s <= 5 && (a !== C.outerWidth() || r !== C.outerHeight()));
                return o
            }

            function x() {
                var t = !1;
                !T.isTipOpen || T.isClosing || T.delayInProgress || (T.activeHover.data(g) === !1 || T.activeHover.is(":disabled") ? t = !0 : l(T.activeHover) || T.activeHover.is(":focus") || T.activeHover.data(v) || (C.data(b) ? l(C) || (t = !0) : t = !0), t && s(T.activeHover))
            }
            var k = new n,
                C = t("#" + i.popupId);
            0 === C.length && (C = t("<div/>", {
                id: i.popupId
            }), 0 === h.length && (h = t("body")), h.append(C)), i.followMouse && (C.data(_) || (p.on("mousemove", o), f.on("scroll", o), C.data(_, !0))), i.mouseOnToPopup && C.on({
                mouseenter: function() {
                    C.data(b) && T.activeHover && T.activeHover.data(m).cancel()
                },
                mouseleave: function() {
                    T.activeHover && T.activeHover.data(m).hide()
                }
            }), this.showTip = a, this.hideTip = s, this.resetPosition = y
        }

        function r(t) {
            return window.SVGElement && t[0] instanceof SVGElement
        }

        function s() {
            T.mouseTrackingActive || (T.mouseTrackingActive = !0, t(function() {
                T.scrollLeft = f.scrollLeft(), T.scrollTop = f.scrollTop(), T.windowWidth = f.width(), T.windowHeight = f.height()
            }), p.on("mousemove", o), f.on({
                resize: function() {
                    T.windowWidth = f.width(), T.windowHeight = f.height()
                },
                scroll: function() {
                    var t = f.scrollLeft(),
                        e = f.scrollTop();
                    t !== T.scrollLeft && (T.currentX += t - T.scrollLeft, T.scrollLeft = t), e !== T.scrollTop && (T.currentY += e - T.scrollTop, T.scrollTop = e)
                }
            }))
        }

        function o(t) {
            T.currentX = t.pageX, T.currentY = t.pageY
        }

        function l(t) {
            var e = t.offset(),
                i = t[0].getBoundingClientRect(),
                n = i.right - i.left,
                a = i.bottom - i.top;
            return T.currentX >= e.left && T.currentX <= e.left + n && T.currentY >= e.top && T.currentY <= e.top + a
        }

        function u(e) {
            var i, n, a = e.data(w),
                r = e.data(x),
                s = e.data(k);
            return a ? (t.isFunction(a) && (a = a.call(e[0])), n = a) : r ? (t.isFunction(r) && (r = r.call(e[0])), r.length > 0 && (n = r.clone(!0, !0))) : s && (i = t("#" + s), i.length > 0 && (n = i.html())), n
        }

        function c(t, e, i) {
            var n = T.scrollTop,
                a = T.scrollLeft,
                r = n + T.windowHeight,
                s = a + T.windowWidth,
                o = S.none;
            return (t.top < n || Math.abs(t.bottom - T.windowHeight) - i < n) && (o |= S.top), (t.top + i > r || Math.abs(t.bottom - T.windowHeight) > r) && (o |= S.bottom), (t.left < a || t.right + e > s) && (o |= S.left), (t.left + e > s || t.right < a) && (o |= S.right), o
        }

        function d(t) {
            for (var e = 0; t;) t &= t - 1, e++;
            return e
        }
        var p = t(document),
            f = t(window),
            h = t("body"),
            m = "displayController",
            g = "hasActiveHover",
            v = "forcedOpen",
            _ = "hasMouseMove",
            b = "mouseOnToPopup",
            y = "originalTitle",
            w = "powertip",
            x = "powertipjq",
            k = "powertiptarget",
            C = 180 / Math.PI,
            T = {
                isTipOpen: !1,
                isFixedTipOpen: !1,
                isClosing: !1,
                tipOpenImminent: !1,
                activeHover: null,
                currentX: 0,
                currentY: 0,
                previousX: 0,
                previousY: 0,
                desyncTimeout: null,
                mouseTrackingActive: !1,
                delayInProgress: !1,
                windowWidth: 0,
                windowHeight: 0,
                scrollTop: 0,
                scrollLeft: 0
            },
            S = {
                none: 0,
                top: 1,
                bottom: 2,
                left: 4,
                right: 8
            };
        t.fn.powerTip = function(e, n) {
            if (!this.length) return this;
            if ("string" === t.type(e) && t.powerTip[e]) return t.powerTip[e].call(this, this, n);
            var r = t.extend({}, t.fn.powerTip.defaults, e),
                o = new a(r);
            return s(), this.each(function() {
                var e, n = t(this),
                    a = n.data(w),
                    s = n.data(x),
                    l = n.data(k);
                n.data(m) && t.powerTip.destroy(n), e = n.attr("title"), a || l || s || !e || (n.data(w, e), n.data(y, e), n.removeAttr("title")), n.data(m, new i(n, r, o))
            }), r.manual || this.on({
                "mouseenter.powertip": function(e) {
                    t.powerTip.show(this, e)
                },
                "mouseleave.powertip": function() {
                    t.powerTip.hide(this)
                },
                "focus.powertip": function() {
                    t.powerTip.show(this)
                },
                "blur.powertip": function() {
                    t.powerTip.hide(this, !0)
                },
                "keydown.powertip": function(e) {
                    27 === e.keyCode && t.powerTip.hide(this, !0)
                }
            }), this
        }, t.fn.powerTip.defaults = {
            fadeInTime: 200,
            fadeOutTime: 100,
            followMouse: !1,
            popupId: "powerTip",
            intentSensitivity: 7,
            intentPollInterval: 100,
            closeDelay: 100,
            placement: "n",
            smartPlacement: !1,
            offset: 10,
            mouseOnToPopup: !1,
            manual: !1
        }, t.fn.powerTip.smartPlacementLists = {
            n: ["n", "ne", "nw", "s"],
            e: ["e", "ne", "se", "w", "nw", "sw", "n", "s", "e"],
            s: ["s", "se", "sw", "n"],
            w: ["w", "nw", "sw", "e", "ne", "se", "n", "s", "w"],
            nw: ["nw", "w", "sw", "n", "s", "se", "nw"],
            ne: ["ne", "e", "se", "n", "s", "sw", "ne"],
            sw: ["sw", "w", "nw", "s", "n", "ne", "sw"],
            se: ["se", "e", "ne", "s", "n", "nw", "se"],
            "nw-alt": ["nw-alt", "n", "ne-alt", "sw-alt", "s", "se-alt", "w", "e"],
            "ne-alt": ["ne-alt", "n", "nw-alt", "se-alt", "s", "sw-alt", "e", "w"],
            "sw-alt": ["sw-alt", "s", "se-alt", "nw-alt", "n", "ne-alt", "w", "e"],
            "se-alt": ["se-alt", "s", "sw-alt", "ne-alt", "n", "nw-alt", "e", "w"]
        }, t.powerTip = {
            show: function(e, i) {
                return i ? (o(i), T.previousX = i.pageX, T.previousY = i.pageY, t(e).data(m).show()) : t(e).first().data(m).show(!0, !0), e
            },
            reposition: function(e) {
                return t(e).first().data(m).resetPosition(), e
            },
            hide: function(e, i) {
                return e ? t(e).first().data(m).hide(i) : T.activeHover && T.activeHover.data(m).hide(!0), e
            },
            destroy: function(e) {
                return t(e).off(".powertip").each(function() {
                    var e = t(this),
                        i = [y, m, g, v];
                    e.data(y) && (e.attr("title", e.data(y)), i.push(w)), e.removeData(i)
                }), e
            }
        }, t.powerTip.showTip = t.powerTip.show, t.powerTip.closeTip = t.powerTip.hide
    }),
    function() {
        ! function(t, e, i) {
            var n, a, r;
            return r = "uwinTree", a = {}, n = function() {
                function e(e, i) {
                    var n;
                    this.element = e, this.$element = t(this.element), this.settings = t.extend({}, a, i), this._defaults = a, this._name = r, n = this, this.$element.on("click", this.settings.selector, function(e) {
                        var i;
                        return i = t(e.currentTarget).parent(), i.hasClass("car-autoparts-tree__item") && i.toggleClass("car-autoparts-tree__item" + n.settings.class_expand_suffix), i.hasClass("car-autoparts-tree__subitem") && i.toggleClass("car-autoparts-tree__subitem" + n.settings.class_expand_suffix), e.preventDefault()
                    }), this.settings.success && this.settings.success()
                }
                return e
            }(), t.fn[r] = function(e) {
                return this.each(function() {
                    if (!t.data(this, "plugin_" + r)) return t.data(this, "plugin_" + r, new n(this, e))
                })
            }
        }(jQuery, window, document)
    }.call(this), define("uwinTree", function() {}),
    function() {
        var t = [].indexOf || function(t) {
            for (var e = 0, i = this.length; e < i; e++)
                if (e in this && this[e] === t) return e;
            return -1
        };
        define("Car", ["jquery", "pubsub", "flexslider", "uwinPaging", "scrollto", "uniform", "swipebox", "powertip", "uwinTree"], function(e, i) {
            var n;
            return n = {
                _filter: {
                    replica: !1,
                    restaurare: !1,
                    secondhand: !1
                },
                init: function() {
                    var t, n, a;
                    return a = this, this.quickBuySend(), e("SELECT").uniform({
                        selectAutoWidth: !1
                    }), e("[data-paging]").uwinPaging({
                        additionParams: this._filter,
                        gotoTopPage: function() {
                            return e.scrollTo(".layout__content.autoparts", 800, {
                                offset: -65
                            })
                        },
                        callback: function() {
                            return i.publish("LOAD_PAGE_CONTENT", {})
                        }
                    }), i.subscribe("FILTERED", function(t, e) {
                        return a.filteredProducts(e)
                    }), t = ".table__actions-link_type_print,.detail-info__schema-actions-link_type_print", e(t).on("click", function(t) {
                        return window.print(), t.preventDefault()
                    }), n = ".car-autoparts-tree__item-plus-minus, .car-autoparts-tree__subitem-plus-minus", e(".car-autoparts-tree__list").on("click", n, this.openAutopartNodeTree), e(".car-autoparts-tree__list").uwinTree({
                        selector: ".expanded",
                        class_expand_suffix: "_state_expand",
                        success: function() {
                            return a.openCurrentAutopartNodeTree()
                        }
                    }), e(".detail-info__select").on("change", function(t) {
                        var n, a, r;
                        if (i.publish("CHANGE_COLOR_SIZE", {
                                color_id: ~~e("#color").val(),
                                size_id: ~~e("#size").val()
                            }), n = e(this).find(":selected"), a = n.attr("data-image")) return r = n.attr("data-image-medium"), e(".detail-info__image-link").attr("href", a), e(".detail-info__image-link IMG").attr("src", r)
                    }), i.subscribe("CHANGE_COLOR_SIZE", function(t, i) {
                        var n, a, r, s;
                        return n = e("#detail-name"), r = ~~n.attr("data-id"), a = ~~n.attr("data-car-id"), s = "/json/car/" + a + "/detail-color-size/" + r + "/", e.ajax({
                            type: "GET",
                            url: s,
                            data: i
                        }).done(function(t) {
                            var i, n;
                            return t = e.parseJSON(t), e(".detail-info__cost").text(t.currency_abb + t.cost), e(".quick-buy__detail-cost").text(t.currency_abb + t.cost), i = ~~e("#quick-buy-form__count").val() || 1, e(".quick-buy__total-cost").text(t.currency_abb + t.cost * i), e(".detail-info__cost").attr("data-cost", t.cost_unformat), e(".detail-info__basket-button").attr("data-cost", t.cost_unformat), e(".quick-buy__detail-cost").attr("data-cost", t.cost_unformat), e(".detail-info__cost").attr("data-usd-cost", t.cost_usd), e(".detail-info__basket-button").attr("data-usd-cost", t.cost_usd), e(".quick-buy__detail-cost").attr("data-usd-cost", t.cost_usd), 0 === ~~t.count ? (n = e(".detail-info__cost").attr("data-not-fount-text"), e(".detail-info__cost").addClass("detail-info__cost_state_disabled"), e(".detail-info__cost").text(n), e(".detail-info__basket-button").addClass("detail-info__basket-button_state_disabled").removeClass("button-buy").css("pointer-events", "none"), e(".detail-info__buy-fast").addClass("detail-info__buy-fast_state_disabled").css("pointer-events", "none")) : (e(".detail-info__cost").removeClass("detail-info__cost_state_disabled"), e(".detail-info__basket-button").removeClass("detail-info__basket-button_state_disabled").addClass("button-buy").css("pointer-events", ""), e(".detail-info__buy-fast").removeClass("detail-info__buy-fast_state_disabled").css("pointer-events", ""))
                        })
                    })
                },
                quickBuySend: function() {
                    return e("INPUT, SELECT, TEXTAREA").on("keyup", function(i) {
                        var n, a;
                        if (n = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40], a = i.keyCode, !(t.call(n, a) >= 0)) return e(this).nextAll(".error").css("opacity", "0")
                    }), e(".quick-buy__form").on("submit", function(t) {
                        var i, n, a, r;
                        return r = "quick-buy-form__", i = e(this), i.find(".error").css("opacity", ""), n = i.find('button[type="submit"]'), n.attr("disabled", "disabled"), a = i.serialize(), 0 !== e("#color").length && (a += "&color=" + e("#color").val() || ""), 0 !== e("#size").length && (a += "&size=" + e("#size").val() || ""), e.ajax({
                            url: i.attr("action"),
                            data: a,
                            type: "POST",
                            dataType: "json",
                            success: function(t) {
                                return e(".quick-buy__success-order-num .success-num-order").text(t.order_num), e(".quick-buy__success-text").css("visibility", "visible"), e(".quick-buy__inputs-wrap").css("visibility", "hidden")
                            },
                            error: function(t) {
                                var i, s, o, l, u;
                                n.removeAttr("disabled"), a = t.responseJSON, l = a.errors, delete a.errors;
                                for (u in a) s = e("#" + r + a[u].id), o = s.parent(), i = o.find(".error"), i.text(a[u].text), i.css("opacity", 1);
                                if (a) return e("#" + r + a[0].id).focus()
                            }
                        }), t.preventDefault()
                    })
                },
                openAutopartNodeTree: function(t, i) {
                    var n;
                    if (null == i && (i = null), n = e(t.target).parent() || t.parent(), n = n.closest("LI"), n.siblings(".__item-" + n.data("id")).toggleClass("__hidden"), i) return i()
                },
                openCurrentAutopartNodeTree: function() {
                    var t, i, n, a;
                    if (a = this, i = e("#autopart-path LI"), 0 !== i.length) return n = ~~e(i.get(0)).text(), t = e('.car-autoparts-tree__item[data-id="' + n + '"]'), a.openAutopartNodeTree(t, function() {
                        var r;
                        return n = ~~e(i.get(1)).text(), r = setInterval(function() {
                            var s;
                            if (t = e('.car-autoparts-tree__subitem[data-id="' + n + '"]'), 0 !== t.length) return a.openAutopartNodeTree(t), t.find(".expanded").trigger("click"), s = setInterval(function() {
                                var t, n;
                                if (n = ~~e(i.get(2)).text(), t = e('.car-autoparts-tree__subsubitem[data-id="' + n + '"]'), t.css("font-style", "italic"), 0 !== t.length) return clearInterval(s)
                            }, 100), 0 !== t.length ? clearInterval(r) : void 0
                        }, 100)
                    }), t.find(".expanded").trigger("click")
                },
                initGallery: function(t) {
                    var i;
                    return i = e(t), i.swipebox({
                        hideBarsDelay: 0
                    })
                },
                positionMiniSchema: function(t) {
                    var i, n, a, r, s, o, l, u, c;
                    return a = e(t), n = e(".detail-info__schema-coord_state_current"), i = e(".detail-info__schema-coord-wrap"), i.css("display", "block"), r = e(".detail-info__schema-link"), s = e(".detail-info__schema-image"), u = r.outerWidth(), l = r.outerHeight(), c = o = 0, n.length ? (c = (n.position().top - l / 2 + 14) * -1, o = (n.position().left - u / 2 + 14) * -1) : (c = (l / 2 + 14) * -1, o = (u / 2 + 14) * -1), i.css("display", "none"), s.css("top", c), s.css("left", o), e(".detail-info__schema-coord").each(function() {
                        var t;
                        if (t = e(this).clone().appendTo(e(".detail-info__schema")), t.css("top", "+=" + c + "px"), t.css("left", "+=" + o + "px"), ~~t.position().top < 0 && t.remove(), ~~t.position().left < 0 && t.remove(), ~~t.position().left > u - 24 && t.remove(), ~~t.position().top > l - 24) return t.remove()
                    }), a.removeClass("detail-info__schema_state_invisible")
                },
                _initSchema: function(t) {
                    return t.find(".__schema-inner").on("mousedown", function(t) {
                        var i, n, a, r, s, o, l, u;
                        return r = document.body.clientWidth, a = document.body.clientHeight, l = e(this).scrollLeft(), u = e(this).scrollTop(), s = t.pageX - (r - l), o = t.pageY - (a - u), i = n = 0, e(this).on("mousemove", function(t) {
                            var l, u;
                            return l = t.pageX, u = t.pageY, i = l - s, n = u - o, console.log(i, n), e(this).scrollTop(a - n), e(this).scrollLeft(r - i), !1
                        }), !1
                    }), t.find(".__schema-inner").on("mouseup", function() {
                        return e(this).unbind("mousemove"), !1
                    }), t.find(".__schema-inner").on("mouseout", function() {
                        return e(this).unbind("mousemove"), !1
                    })
                },
                initSchemaSwipebox: function(t) {
                    var i;
                    return i = e(t), i.swipebox({
                        hideBarsDelay: 0,
                        useInnerWrap: !0,
                        beforeOpen: function() {
                            var t;
                            return t = setTimeout(function() {
                                if (i = e(".detail-info__schema-coord-wrap .detail-info__schema-coord"), e.swipebox.isOpen) return i.clone().appendTo(e("#swipebox-slider .slide-inner")), e("#swipebox-slider .slide-inner .__tooltip").each(function() {
                                    return e(this).powerTip({
                                        placement: e(this).data("placement") || "n",
                                        smartPlacement: !0
                                    })
                                }), clearTimeout(t), e("#swipebox-slider .slide-inner").on("mousedown", function(t) {
                                    var i, n, a, r, s, o, l, u;
                                    return r = document.body.clientWidth, a = document.body.clientHeight, l = e("#swipebox-slider .slide-inner").scrollLeft(), u = e("#swipebox-slider .slide-inner").scrollTop(), s = t.pageX - (r - l), o = t.pageY - (a - u), i = n = 0, e("#swipebox-slider .slide-inner").on("mousemove", function(t) {
                                        var l, u;
                                        return l = t.pageX, u = t.pageY, i = l - s, n = u - o, e("#swipebox-slider .slide-inner").scrollTop(a - n), e("#swipebox-slider .slide-inner").scrollLeft(r - i), !1
                                    }), !1
                                }), e("#swipebox-slider .slide-inner").on("mouseup", function() {
                                    return e("#swipebox-slider .slide-inner").unbind("mousemove"), !1
                                }), e("#swipebox-slider .slide-inner").on("mouseout", function() {
                                    return e("#swipebox-slider .slide-inner").unbind("mousemove"), !1
                                })
                            }, 500)
                        }
                    })
                },
                initSlider: function(t) {
                    var i, n, a, r, s;
                    if (i = e(t), a = e(".cars-slider__list").children(".cars-slider__item_type_current").index(), s = e(".cars-slider__list .slider__item").length, a >= 2 ? a -= 2 : a = 0, r = !1, a + 5 >= s && (r = !0, a = s - 5), n = i.flexslider({
                            animation: "slide",
                            directionNav: !0,
                            controlNav: !1,
                            animationLoop: !1,
                            itemWidth: 144,
                            itemMargin: 35,
                            slideshow: !1,
                            move: 1,
                            startAt: a,
                            start: function(t) {
                                var i, n, s, o;
                                return i = e(".cars-slider__list"), s = i.css("-webkit-transform") || i.css("-moz-transform") || i.css("-ms-transform") || i.css("-o-transform") || i.css("transform"), o = s.split("(")[1].split(")")[0].split(","), n = 0, r || (n = 35 * a), e(".cars-slider__list").css("-webkit-transform", "translate3d(" + (~~o[4] - n) + "px, 0, 0)"), e(".cars-slider__slider").css("opacity", 1)
                            }
                        }), 0 !== n.length) return n.data("flexslider").resize().update()
                },
                filteredProducts: function(t) {
                    if ("products" === t.type) return "true" === t.value ? t.value = !0 : t.value = !1, this._filter[t.name] = t.value
                }
            }
        })
    }.call(this);
var withAdverts = !1;
(function() {
    var t = [].indexOf || function(t) {
        for (var e = 0, i = this.length; e < i; e++)
            if (e in this && this[e] === t) return e;
        return -1
    };
    define("Basket", ["jquery", "pubsub", "scrollto"], function(e, i) {
        var n;
        return n = {
            init: function() {
                var t;
                return t = this, this.promocode(), this.sidebar(), this.continueOrder(), e(document).on("click", ".cabinet__edit-link", function(t) {
                    return e("#user-data__form").toggleClass("form_state_hidden"), e(".basket-user-info__block").toggleClass("form_state_hidden"), t.preventDefault()
                }), i.subscribe("DRAW_BASKET", function(e, i) {
                    return t.drawBasket(i)
                }), i.subscribe("CHANGE_BASKET", function(t, n) {
                    var a;
                    return a = {
                        method: n.method,
                        id: n.id,
                        count: n.count,
                        color: n.color,
                        size: n.size
                    }, e.ajax({
                        url: "/json/basket/change/",
                        data: a,
                        type: "POST",
                        dataType: "json",
                        success: function(t) {
                            var a, r, s, o, l, u;
                            if (i.publish("DRAW_BASKET", t), "change" === n.method && (r = e("#product-sum-" + n.id), r.length > 0)) return a = e("#product-cost-" + n.id), l = a.attr("data-cost") * n.count, u = a.attr("data-usd-cost") * n.count, r.attr("data-cost", l), r.attr("data-usd-cost", u), o = numeral(l).format("0,0.00"), o = o.replace(",00", ""), s = e("#currencies LI A").first().attr("data-short-name"), "P" === s && (s = '<span class="rur">' + s + "</span>"), "грн." === s ? r.html(numeral(l).format("0,0") + "&thinsp;<small>" + s + "</small>") : r.html(s + o)
                        }
                    })
                })
            },
            _loadBasketPage: function(n) {
                var a;
                return n = n || 1, a = e(".basket-bar__sum").attr("data-cost"), 0 === ~~a && (window.location = "/"), e.ajax({
                    url: "/json/basket/step/" + n + "/",
                    type: "GET",
                    success: function(r) {
                        var s, o, l;
                        return setTimeout(function() {
                            // createPushToGA()
                        }, 300), s = e("#basket-page-content"), l = s.find("#basket-page").attr("data-num"), "undefined" != typeof l && (l = l), o = e(r).attr("data-num") || 1, location.hash = "step=" + o, e(".basket-steps__item").removeClass("basket-steps__item_state_active"), e(".basket-steps__link_step_" + n).parent().addClass("basket-steps__item_state_active"), s.html(r), e(".tabs").uwinTabs(), e(".auth__form SELECT").uniform({
                            selectAutoWidth: !1,
                            selectClass: "selector form__selector_size_big"
                        }), i.publish("LOAD_PAGE_CONTENT", {}), e("SELECT").on("change", function(t) {
                            return e(this).parent().nextAll(".error").css("opacity", "0")
                        }), 1 == e("#basket-page").attr("data-num") ? withAdverts = !!e('h2[class^="basket-products__name data-advert-"]').length : 2 == e("#basket-page").attr("data-num") && (e(".advertStatus").length || (e("#user-data__form").append('<input type="hidden" name="with_advets" class="advertStatus" value="0">'), e("#form-page-2").append('<input type="hidden" name="with_advets" class="advertStatus" value="0">')), 1 == withAdverts && e(".basket-sidebar__item").length == e('h2[class^="basket-products__name data-advert-"]').length ? hideDelivering() : 1 == withAdverts && e(".advertStatus").val(1), e("[data-advert-id]").length == e(".basket-sidebar__item").length ? hideDelivering() : e("[data-advert-id]").length ? e(".advertStatus").val(1) : e(".advertStatus").val(0)), e("INPUT, TEXTAREA").on("keyup", function(i) {
                            var n, a;
                            if (n = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40], a = i.keyCode, !(t.call(n, a) >= 0)) return e(this).nextAll(".error").css("opacity", "0")
                        }), e(".basket-steps__add-comment").on("click", function(t) {
                            return e(".basket-steps__comment-textarea").toggleClass("basket-steps__comment-textarea_type_visible"), t.preventDefault()
                        }), e(document).on("change", ".payments-deliveries__radio", function(t) {
                            var i, n, r, s, o, l, u;
                            if (n = e("#form-page-2"), r = n.find(".form__submit"), u = n.find('INPUT[name="payment"]:checked').length, l = n.find('INPUT[name="delivery"]:checked').length, 1 === u && 1 === l ? r.removeAttr("disabled") : r.attr("disabled", "disabled"),  o = e(this).attr("data-cost"), "undefined" != typeof o) return i = e(".payments-deliveries__delivery-cost-value"), i.attr("data-cost", o), i.attr("data-usd-cost", e(this).attr("data-usd-cost")), 0 === ~~o ? a = 0 : (a = numeral(o).format("0,0.00"), a = a.replace(",00", "")), s = e("#currencies LI A").first().attr("data-short-name"), "P" === s && (s = '<span class="rur">' + s + "</span>"), "грн." === s ? i.html(a + "&thinsp;<small>" + s + "</small>") : i.html(s + a), $('.total_sum .basket_bar_cost div').html(s + (parseFloat(a.toString().replace(',','.')) + parseFloat($('.sum .basket_bar_cost div').attr('data-cost').replace(',','.'))).toFixed(2).toString().replace('.',','))
                        }), e("#user-data__form").on("submit", function(t) {
                            var i, n, a, r;
                            return i = e(this), i.find(".error").css("opacity", ""), n = i.find("button[type=submit]"), n.attr("disabled", "disabled"), r = "user-info__", a = i.serialize(), e.ajax({
                                type: "POST",
                                url: i.attr("action"),
                                data: a,
                                dataType: "json",
                                success: function(t) {
                                    var i, a;
                                    return e("#basket-user-info__surname").text(t.surname), e("#basket-user-info__secondname").text(t.secondname), e(".basket-user-info__item_type_phone").text(t.phone), e("#basket-user-info__street").text(t.street), e("#basket-user-info__build").text(t.build), e("#basket-user-info__flat").text(t.flat), e("#basket-user-info__city").text(t.city), e("#basket-user-info__index").text(t.index), a = e("#user-info__country OPTION:selected").text(), e("#basket-user-info__country").text(a), e("#user-data__form").toggleClass("form_state_hidden"), e(".basket-user-info__block").toggleClass("form_state_hidden"), e("#payments-deliveries-wrap").html(t.html), e(".payments-deliveries__delivery-cost-value").attr("data-cost", 0), e(".payments-deliveries__delivery-cost-value").attr("data-usd-cost", 0), i = e("#currencies LI A").first().attr("data-short-name"), "P" === i && (i = '<span class="rur">' + i + "</span>"), e(".payments-deliveries__delivery-cost-value").html(i + "0"), n.removeAttr("disabled")
                                },
                                error: function(t) {
                                    var i, s, o, l, u;
                                    n.removeAttr("disabled"), a = t.responseJSON, l = a.errors, delete a.errors;
                                    for (u in a) s = e("#" + r + a[u].id), o = s.parent(), o.hasClass("selector") && (o = o.parent()), i = o.find(".error"), i.text(a[u].text), i.css("opacity", 1), n.removeAttr("disabled");
                                    if (a) return e("#" + r + a[0].id).focus()
                                }
                            }, t.preventDefault())
                        }), e("#user-info__form").on("submit", function(t) {
                            var i, n, a, r;
                            return i = e(this), i.find(".error").css("opacity", ""), n = i.find("button[type=submit]"), n.attr("disabled", "disabled"), r = "user-info__", a = i.serialize(), e.ajax({
                                type: "POST",
                                url: i.attr("action"),
                                data: a,
                                dataType: "json",
                                success: function(t) {
                                    return location.hash = "step=1"
                                },
                                error: function(t) {
                                    var i, s, o, l, u;
                                    n.removeAttr("disabled"), a = t.responseJSON, l = a.errors, delete a.errors;
                                    for (u in a) s = e("#" + r + a[u].id), o = s.parent(), o.hasClass("selector") && (o = o.parent()), i = o.find(".error"), i.text(a[u].text), i.css("opacity", 1), n.removeAttr("disabled");
                                    if (a) return e("#" + r + a[0].id).focus()
                                }
                            }, t.preventDefault())
                        }), e("#form-page-2").on("submit", function(t) {
                            var i, n, a;
                            return i = e(this), n = i.find("button[type=submit]"), a = i.serialize(), console.log(a), e.ajax({
                                type: "POST",
                                url: i.attr("action"),
                                data: a,
                                dataType: "json",
                                success: function(t) {
                                    var i;
                                    if (console.log(t), i = e("HTML").attr("lang").split("-")[0], _gaq.push(["_addTrans", t.num, i + "dev.avtoclassika.com", ~~e(".basket-products__total-cost").attr("data-usd-cost") + ~~e(".payments-deliveries__delivery-cost-value").attr("data-usd-cost"), e(".payments-deliveries__delivery-cost-value").attr("data-usd-cost")]), e("#basket-sidebar__list LI").each(function() {
                                            return _gaq.push(["_addItem", t.num, "SKU/code", e(this).find(".basket-sidebar__item-name").text(), e(this).attr("data-car"), e(this).find(".basket-sidebar__item-cost").attr("data-usd-cost"), e(this).find(".basket-sidebar__item-count").text().split(" ")[0]])
                                        }), _gaq.push(["_trackTrans"]), e.removeCookie("basket", {
                                            path: "/",
                                            domain: "." + e('META[property="uwin:serverName"]').attr("content")
                                        }), "finish" === t.state && (window.location = t.redirect), "privat24" === t.state || "liqpay" === t.state || "portmone" === t.state) {
                                        var n = document.createElement("div");
                                        n.setAttribute("id", "privat24"), n.innerHTML = t.form, document.body.appendChild(n), e("#privat24 form").submit()
                                    }
                                },
                                error: function(t) {
                                    return console.log(t), n.removeAttr("disabled")
                                }
                            }, t.preventDefault())
                        })
                    }
                })
            },
            sidebar: function() {
                return e(document).on("click", ".basket-sidebar__item-delete, .basket-products__delete", function(t) {
                    var n, a, r, s;
                    return n = e(this), n.addClass("__disabled"), n.is("[data-advert-id]") ? (r = n.attr("data-id"), s = n.attr("data-advert-id")) : (r = n.attr("data-id"), s = null), a = n.closest(".basket-sidebar__item, .basket-products__item"), e.ajax({
                        url: "/json/basket/delete-item/" + r + "/",
                        type: "POST",
                        data: {
                            advert_id: s
                        },
                        dataType: "json",
                        success: function(t) {
                            return n.removeClass("__disabled"), a.remove(), 0 === ~~t.count && e("#related-details").html(""), e("[data-advert-id]").length == e(".basket-sidebar__item").length ? hideDelivering() : e("[data-advert-id]").length ? e(".advertStatus").val(1) : e(".advertStatus").val(0), i.publish("DRAW_BASKET", t)
                        }
                    }).detectHref(), t.preventDefault()
                })
            },
            quickBuyForm: function() {
                return e(".detail-info__buy-fast").on("click", function(t) {
                    var i, n;
                    return i = e("#quick-buy-form"), i.css("top", e(this).position().top + e(this).height() + 15), i.hasClass("quick-buy_state_visible") || e.scrollTo(".detail-info__buy-fast", 500, {
                        offset: -100
                    }), i.toggleClass("quick-buy_state_visible"), n = setTimeout(function() {
                        return i.find("[autofocus]").focus(), clearTimeout(n)
                    }, 50), t.preventDefault()
                }), e("BODY").on("click", function(t) {
                    var i;
                    if (i = e("#quick-buy-form"), i.hasClass("quick-buy_state_visible") && !e(t.target).hasClass("detail-info__buy-fast") && 0 === e(t.target).closest("#quick-buy-form").length) return i.removeClass("quick-buy_state_visible")
                })
            },
            initSteps: function() {
                var t;
                return t = this, e(window).bind("hashchange", function(e) {
                    var i, n;
                    return i = location.hash.substr(1), n = i.substr(i.indexOf("=") + 1), t._loadBasketPage(n)
                }), e(window).trigger("hashchange")
            },
            promocode: function() {
                return e(document).on("submit", "#basket-promocode-wrap", function(t) {
                    var n, a, r;
                    return n = e(this), n.find(".error").css("opacity", ""), a = n.find("button[type=submit]"), a.attr("disabled", "disabled"), r = n.serialize(), e.ajax({
                        url: n.attr("action"),
                        data: r,
                        dataType: "json",
                        type: "POST",
                        success: function(t) {
                            return n.find(".form__input, .form__button_type_withinput, .form__input-error").css("display", "none"), i.publish("CHANGE_BASKET", {
                                method: "null"
                            })
                        },
                        error: function(t) {
                            var i, n, s, o;
                            return a.removeAttr("disabled"), r = t.responseJSON, o = r.errors, delete r.errors, n = e("#promocode"), s = n.parent(), s.hasClass("selector") && (s = s.parent()), i = s.find(".error"), i.text(r[0].text), i.css("opacity", 1), a.removeAttr("disabled")
                        }
                    }), t.preventDefault()
                })
            },
            continueOrder: function() {
                return e(".payments-deliveries__radio").on("change", function(t) {
                    var i, n, a;
                    return i = e("#form-order-continue"), n = i.find(".form__submit"), a = i.find('INPUT[name="payment"]:checked').length, 1 === a ? n.removeAttr("disabled") : n.attr("disabled", "disabled")
                }), e("#form-order-continue").on("submit", function(t) {
                    var i, n, a;
                    return i = e(this), n = i.find("button[type=submit]"), n.attr("disabled", "disabled"), a = i.serialize(), t.preventDefault(), e.ajax({
                        type: "POST",
                        url: i.attr("action"),
                        data: a,
                        dataType: "json",
                        success: function(t) {
                            if ("finish" === t.state && (window.location = t.redirect, console.log(t)), "privat24" === t.state || "liqpay" === t.state || "portmone" === t.state) {
                                var i = document.createElement("div");
                                i.setAttribute("id", "privat24"), i.innerHTML = t.form, document.body.appendChild(i), e("#privat24 form").submit()
                            }
                        },
                        error: function(t) {
                            return n.removeAttr("disabled")
                        }
                    })
                })
            },
            drawBasket: function(t) {
                var i, n, a, r, s, o, l, u;
                return i = e(".basket-bar__notifier"), i.addClass("pulse"), i.text(t.count), ~~t.count > 0 ? e(".basket-bar").removeClass("basket-bar_state_disabled") : e(".basket-bar").addClass("basket-bar_state_disabled"), r = e(".basket-bar__sum_type_int"), a = e(".basket-bar__sum_type_decimal"), l = t.sum, u = t.sum_usd, o = 0 === ~~l ? "0,0".split(",") : numeral(l).format("0,0.0").split(","), r.text(o[0]), a.text(o[1]), e(".basket-bar__sum").attr("data-cost", l), e(".basket-bar__sum").attr("data-usd-cost", u), o = 0 === ~~l ? "0" : numeral(l).format("0,0.00"), o = o.replace(",00", ""), n = e(".basket-products__total-cost"), n.attr("data-cost", l), n.attr("data-usd-cost", u), s = e("#currencies LI A").first().attr("data-short-name"), "P" === s && (s = '<span class="rur">' + s + "</span>"), "грн." === s ? 0 === ~~l ? n.html("0&thinsp;<small>" + s + "</small>") : n.html(numeral(l).format("0,0") + "&thinsp;<small>" + s + "</small>") : n.html(s + o), e(".basket-bar__currency").html(s), "грн." === s ? (e("HEADER .layout__header-info-bar .basket-bar__currency").detach().insertAfter("HEADER .layout__header-info-bar .basket-bar__sum_type_int"), e(".layout__header-info-bar_type_sticky .basket-bar__currency").detach().insertAfter(".layout__header-info-bar_type_sticky .basket-bar__sum_type_int"), e(".basket-bar__currency").each(function() {
                    return e(this).replaceWith('<small class="basket-bar__currency" style="font-size:65%;"> ' + e(this).text() + "</small>")
                })) : (e("HEADER .layout__header-info-bar .basket-bar__currency").detach().insertBefore("HEADER .layout__header-info-bar .basket-bar__sum_type_int"), e(".layout__header-info-bar_type_sticky .basket-bar__currency").detach().insertBefore(".layout__header-info-bar_type_sticky .basket-bar__sum_type_int"), e(".basket-bar__currency").each(function() {
                    return e(this).replaceWith('<span class="basket-bar__currency">' + e(this).text() + "</span>")
                })), l <= 0 ? e("#basket-submit").attr("disabled", "disabled") : e("#basket-submit").removeAttr("disabled")
            }
        }
    })
}).call(this),
    function() {
        define("News", ["jquery", "pubsub", "uwinTree"], function(t, e) {
            var i;
            return i = {
                init: function() {
                    return t("[data-paging]").uwinPaging({
                        gotoTopPage: function() {
                            return t.scrollTo(".layout__content.news", 800, {
                                offset: -65
                            })
                        },
                        callback: function() {
                            return e.publish("LOAD_PAGE_CONTENT", {})
                        }
                    }), t(".car-autoparts-tree__list").uwinTree({
                        selector: ".expanded",
                        class_expand_suffix: "_state_expand"
                    })
                }
            }
        })
    }.call(this),
    function() {
        var t = [].indexOf || function(t) {
            for (var e = 0, i = this.length; e < i; e++)
                if (e in this && this[e] === t) return e;
            return -1
        };
        define("Users", ["jquery", "pubsub"], function(e, i) {
            var n;
            return n = {
                init: function() {},
                initSubscriptionBar: function() {
                    return e("INPUT").on("keyup", function(i) {
                        var n, a;
                        if (n = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40], a = i.keyCode, !(t.call(n, a) >= 0)) return e(this).nextAll(".error").css("opacity", "0")
                    }), e(".subscription-bar__unsubscribe-link").on("click", function(t) {
                        var i, n;
                        return n = e(this).attr("href"), i = e(this).attr("data-email"), e.ajax({
                            url: n,
                            data: "email=" + i,
                            type: "post",
                            success: function(t) {
                                var i, n;
                                return i = e(".subscription-bar__form"), i.css("opacity", 1), i.css("visibility", "visible"), n = i.parent(), n.find(".subscription-bar__success").css("opacity", 0)
                            }
                        }), t.preventDefault()
                    }), e(".subscription-bar__form").on("submit", function(t) {
                        var i, n, a;
                        return a = "subscription-bar-form__", i = e(this), i.find(".error").css("opacity", ""), n = i.find('button[type="submit"]'), n.attr("disabled", "disabled"), e.ajax({
                            url: i.attr("action"),
                            data: i.serialize(),
                            type: "POST",
                            dataType: "json",
                            success: function(t) {
                                var e, a;
                                return ga("send", "event", "Subscription", "Subscribed"),
                                    n.removeAttr("disabled"), i.css("opacity", 0), i.css("visibility", "hidden"), e = i.parent(), a = i.find("#subscription-bar-form__email").val(), e.find(".subscription-bar__email-link").attr("href", "mailto:" + a).text(a), e.find(".subscription-bar__unsubscribe-link").attr("data-email", a), e.find(".subscription-bar__success").css("opacity", 1)
                            },
                            error: function(t) {
                                var i, r, s, o, l, u;
                                n.removeAttr("disabled"), o = t.responseJSON, l = o.errors, delete o.errors;
                                for (u in o) r = e("#" + a + o[u].id), s = r.parent(), i = s.find(".error"), i.text(o[u].text), i.css("opacity", 1);
                                if (o) return e("#" + a + o[0].id).focus()
                            }
                        }), t.preventDefault()
                    })
                },
                initAddReviewForm: function() {
                    var i;
                    return i = this, e(document).on(e.modal.OPEN, function(t, i) {
                        var n;
                        return n = e("#add-review, #add-request"), n.find("INPUT, SELECT").filter(":first").focus()
                    }), e(document).on(e.modal.BEFORE_OPEN, function(i, n) {
                        return e("SELECT").on("change", function(t) {
                            return e(this).parent().nextAll(".error").css("opacity", "0")
                        }), e("INPUT, TEXTAREA").on("keyup", function(i) {
                            var n, a;
                            if (n = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40], a = i.keyCode, !(t.call(n, a) >= 0)) return e(this).nextAll(".error").css("opacity", "0")
                        }), e("#add-review SELECT, #add-request SELECT").uniform({
                            selectAutoWidth: !1,
                            selectClass: "selector form__selector_size_big"
                        }), e("#add-review, #add-request").on("submit", function(t) {
                            var i, n, a, r;
                            return i = e(this), i.find(".error").css("opacity", ""), n = i.find("button[type=submit]"), n.attr("disabled", "disabled"), r = "add-review__", a = i.serialize(), e.ajax({
                                type: "POST",
                                url: i.attr("action"),
                                data: a,
                                dataType: "json",
                                success: function() {
                                    return e.modal.close()
                                },
                                error: function(t) {
                                    var i, s, o, l, u;
                                    n.removeAttr("disabled"), a = t.responseJSON, l = a.errors, delete a.errors;
                                    for (u in a) s = e("#" + r + a[u].id), o = s.parent(), o.hasClass("selector") && (o = o.parent()), i = o.find(".error"), i.text(a[u].text), i.css("opacity", 1), n.removeAttr("disabled");
                                    if (a) return e("#" + r + a[0].id).focus()
                                }
                            }, t.preventDefault())
                        })
                    })
                },
                initCabinet: function() {
                    return e(".cabinet__form SELECT").uniform({
                        selectAutoWidth: !1,
                        selectClass: "selector form__selector_size_big"
                    }), e(".cabinet__edit-link").parent().find(".hideShowPassword-toggle").addClass("cabinet__submit_state_hidden"), e(".cabinet__edit-link").on("click", function(t) {
                        var i, n;
                        return i = e(this).parent(), n = i.find("INPUT, SELECT, TEXTAREA"), n.is(":disabled") ? (n.removeAttr("disabled"), n.parent().removeClass("cabinet__form-input-wrap_type_disabled"), i.find(".form__submit, .hideShowPassword-toggle").removeClass("cabinet__submit_state_hidden"), n.first().focus(), n.closest(".cabinet__form-input-wrap_type_disabled").removeClass("cabinet__form-input-wrap_type_disabled").addClass("cabinet__form-input-wrap_type_enabled")) : (n.attr("disabled", "disabled"), n.parent().addClass("cabinet__form-input-wrap_type_disabled"), i.find(".form__submit, .hideShowPassword-toggle").addClass("cabinet__submit_state_hidden"), n.closest(".cabinet__form-input-wrap_type_enabled").removeClass("cabinet__form-input-wrap_type_enabled").addClass("cabinet__form-input-wrap_type_disabled")), t.preventDefault()
                    }), e(".cabinet__form").on("submit", function(t) {
                        var i, n, a, r;
                        return r = "cabinet__", i = e(this), n = i.find("INPUT, SELECT, TEXTAREA"), i.find(".error").css("opacity", ""), a = i.find('button[type="submit"]'), a.attr("disabled", "disabled"), e.ajax({
                            url: i.attr("action"),
                            data: i.serialize(),
                            type: "POST",
                            dataType: "json",
                            success: function(t) {
                                return a.removeAttr("disabled"), n.attr("disabled", "disabled"), n.parent().addClass("cabinet__form-input-wrap_type_disabled"), i.find(".form__submit").addClass("cabinet__submit_state_hidden"), n.closest(".cabinet__form-input-wrap_type_enabled").removeClass("cabinet__form-input-wrap_type_enabled").addClass("cabinet__form-input-wrap_type_disabled")
                            },
                            error: function(t) {
                                var i, n, s, o, l, u;
                                a.removeAttr("disabled"), o = t.responseJSON, l = o.errors, delete o.errors;
                                for (u in o) n = e("#" + r + o[u].id), s = n.parent(), i = s.find(".error"), i.text(o[u].text), i.css("opacity", 1);
                                if (o) return e("#" + r + o[0].id).focus()
                            }
                        }), t.preventDefault()
                    })
                },
                initAuthPopup: function() {
                    return e(document).on("click", ".auth-popup__social-link", function(t) {
                        var i, n, a, r, s, o;
                        return o = 640, i = 480, n = screen.width / 2 - o / 2, s = screen.height / 2 - i / 2, a = window.open(e(this).attr("href"), "Authentication", "resizeable=true,width=" + o + ", height=" + i + ", top=" + s + ", left=" + n), a.focus(), r = setInterval(function() {
                            if (a.closed) return clearInterval(r), location.reload()
                        }, 100), t.preventDefault()
                    }), e(".auth-popup").removeClass("auth-popup_state_invisible"), e(".enter-button").on("click", function(t) {
                        var i, n, a;
                        return i = e(".auth-popup"), 0 === e(this).parent().find(".auth-popup").length && (i.detach(), e(this).parent().append(i)), n = setTimeout(function() {
                            return i.toggleClass("auth-popup_state_visible"), clearTimeout(n)
                        }, 20), a = setTimeout(function() {
                            return i.find("[autofocus]").focus(), clearTimeout(a)
                        }, 70), t.preventDefault()
                    }), e(document).on("submit", "#user-register", function(t) {
                        var i, n, a;
                        return a = "user-register__", i = e(this), i.find(".error").css("opacity", ""), n = i.find('button[type="submit"]'), n.attr("disabled", "disabled"), e.ajax({
                            url: i.attr("action"),
                            data: i.serialize(),
                            type: "POST",
                            dataType: "json",
                            success: function(t) {
                                var e;
                                return n.removeAttr("disabled"), i.css("opacity", 0), i.css("visibility", "hidden"), ga("send", "event", "UserReg", "registration"), e = i.attr("data-redirect") || "/cabinet/", window.location = e
                            },
                            error: function(t) {
                                var i, r, s, o, l, u;
                                n.removeAttr("disabled"), o = t.responseJSON, l = o.errors, delete o.errors;
                                for (u in o) r = e("#" + a + o[u].id), s = r.parent(), i = s.find(".error"), i.text(o[u].text), i.css("opacity", 1);
                                if (o) return e("#" + a + o[0].id).focus()
                            }
                        }), t.preventDefault()
                    }), e("BODY").on("click", function(t) {
                        var i;
                        if (i = e(".auth-popup"), i.hasClass("auth-popup_state_visible") && !e(t.target).hasClass("enter-button") && 0 === e(t.target).closest(".auth-popup").length) return i.removeClass("auth-popup_state_visible")
                    }), e(document).on("submit", "#auth-form", function(t) {
                        var i, n, a;
                        return a = "auth-form__", i = e(this), i.find(".error").css("opacity", ""), n = i.find('button[type="submit"]'), n.attr("disabled", "disabled"), e.ajax({
                            url: i.attr("action"),
                            data: i.serialize(),
                            type: "POST",
                            dataType: "json",
                            success: function(t) {
                                return "undefined" == typeof t.success ? location.reload() : e(".auth-popup__lost-text").html(t.success), n.removeAttr("disabled")
                            },
                            error: function(t) {
                                var e, r, s, o, l, u;
                                n.removeAttr("disabled"), o = t.responseJSON, l = o.errors, delete o.errors;
                                for (u in o) r = i.find("#" + a + o[u].id), s = r.parent(), e = s.find(".error"), e.text(o[u].text), e.css("opacity", 1);
                                if (o) return i.find("#" + a + o[0].id).focus()
                            }
                        }), t.preventDefault()
                    }), e(document).on("click", ".auth-popup__lost-password-link", function(t) {
                        var i, n, a;
                        return n = e(this).closest("form"), a = n.find(".form__submit"), i = n.find(".auth-popup__lost-password .auth-popup__lost-password-link"), "lost" === i.attr("data-state") ? (i.text(i.attr("data-login-caption")), i.attr("data-state", "login"), n.find(".auth-popup__input-wrap_type_password").css("display", "none"), n.find(".auth-popup__lost-text").html(n.find(".auth-popup__lost-text").attr("data-text")), n.find(".auth-popup__lost-text").css("display", "block"), a.text(a.attr("data-repair-caption")), n.attr("action", "/users/repair/")) : (i.text(i.attr("data-lost-caption")), i.attr("data-state", "lost"), n.find(".auth-popup__input-wrap_type_password").css("display", "block"), n.find(".auth-popup__lost-text").css("display", "none"), a.text(a.attr("data-login-caption")), n.attr("action", "/users/auth/")), e("#auth-form__email").focus(), t.preventDefault()
                    })
                }
            }
        })
    }.call(this),
    function() {
        define("Comments", ["jquery"], function(t) {
            var e;
            return e = {
                form_selector: "#add-comment-wrap",
                reply_selector: ".comments__actions-reply",
                reply_form_selector: ".comments__reply-wrap",
                $form: null,
                init: function() {
                    var e, i;
                    return i = this, e = t(this.form_selector).html(), t(".comments").on("click", this.reply_selector, function(n) {
                        var a, r, s, o;
                        return a = t(this).closest(".comments__item"), s = ~~a.attr("data-id"), o = ~~a.attr("data-level") + 1, r = t(this).closest(".comments__item").find(i.reply_form_selector), "" === r.html() && (r.html(e), r.find("#add-comment").attr("id", "add-comment" + s), r.find("#add-comment__name").attr("id", "add-comment__name" + s), r.find("#add-comment__email").attr("id", "add-comment__email" + s), r.find("#add-comment__text").attr("id", "add-comment__text" + s), r.find("[name=parent_id]").val(s), r.find("[name=level]").val(o), r.find("FORM").on("submit", function(t) {
                            return i.add(s, t), t.preventDefault()
                        })), r.toggleClass("comments__reply-wrap_state_show"), r.hasClass("comments__reply-wrap_state_show") && r.find("INPUT, SELECT, TEXTAREA").first().focus(), n.preventDefault()
                    }), t(this.form_selector).find("FORM").on("submit", function(t) {
                        return i.add("", t), t.preventDefault()
                    })
                },
                add: function(e, i) {
                    var n, a, r, s;
                    return n = t(i.target), n.find(".error").css("opacity", ""), a = n.find("button[type=submit]"), a.attr("disabled", "disabled"), s = "add-comment__", r = n.serialize(), t.ajax({
                        type: "POST",
                        url: n.attr("action"),
                        data: r,
                        dataType: "json",
                        success: function(e) {
                            return "add-comment-wrap" === n.parent().attr("id") ? t(".comments").append(e.html) : (t(e.html).insertAfter(n.closest(".comments__item ")), n.closest(".comments__reply-wrap_state_show").removeClass("comments__reply-wrap_state_show")), n.find("INPUT, TEXTAREA").val(""), a.removeAttr("disabled")
                        },
                        error: function(i) {
                            var n, o, l, u, c;
                            a.removeAttr("disabled"), r = i.responseJSON, u = r.errors, delete r.errors;
                            for (c in r) o = t("#" + s + r[c].id + e), l = o.parent(), l.hasClass("selector") && (l = l.parent()), n = l.find(".error"), n.text(r[c].text), n.css("opacity", 1), a.removeAttr("disabled");
                            if (r) return t("#" + s + r[0].id + e).focus()
                        }
                    })
                }
            }
        })
    }.call(this),
    function() {
        define("UwinGoogleMap", ["jquery", "yepnope"], function() {
            var t;
            return t = function(t, e) {
                var i, n, a;
                return e = e || {}, a = this, yepnope({
                    load: "//www.google.com/jsapi?",
                    complete: function() {
                        return google.load("maps", "3", {
                            callback: function() {
                                return i(a, e)
                            },
                            other_params: "sensor=false&language=" + e.lang
                        })
                    }
                }), this.defaults = {
                    centerCoord: "50.486081,30.49324",
                    zoom: 10,
                    draggable: !0,
                    disableDefaultUI: !1,
                    disableDoubleClickZoom: !1,
                    scrollwheel: !1,
                    mapTypeControl: !0,
                    streetViewControl: !0,
                    zoomControl: !0,
                    panControl: !0,
                    pin: "/img/pins/me.png"
                }, this.markers = [], this.settings = $.extend({}, this.defaults, e), n = function(t, e) {
                    var i, n;
                    return null == e && (e = !1), n = t.split(","), i = 0, e && (i = -5e-4), t = {
                        lat: parseFloat(n[0]) + i,
                        lng: parseFloat(n[1])
                    }, new google.maps.LatLng(t.lat, t.lng)
                }, i = function(e, i) {
                    var a, r, s, o, l;
                    for (e.settings.centerCoord = i.centerCoord || t.attr("data-center") || e.settings.centerCoord, e.settings.zoom = ~~(i.zoom || t.attr("data-zoom") || e.settings.zoom), e.settings.center = n(e.settings.centerCoord, !0), i.markers || e.markers.push(t.attr("data-marker")), e.map = new google.maps.Map(t.get(0), e.settings), o = e.markers, l = [], a = 0, r = o.length; a < r; a++) s = o[a], l.push(new google.maps.Marker({
                        icon: e.settings.pin,
                        map: e.map,
                        draggable: !1,
                        position: n(s)
                    }));
                    return l
                }, this
            },
                function(e, i) {
                    return new t(e, i)
                }
        })
    }.call(this),
    function() {
        ! function(t, e, i) {
            var n, a;
            return a = "uwinTabs", n = function() {
                function e(e) {
                    var i;
                    this.element = e, this.$element = t(this.element), this._name = a, i = this, this.$element.find(".tabs__nav-link").on("click", function(e) {
                        var n;
                        return i.$element.find(".tabs__nav-item").removeClass("tabs__nav-item_state_current"), t(this).parent().addClass("tabs__nav-item_state_current"), n = t(this).attr("href"), i.$element.find(".tabs__content").removeClass("tabs__content_state_current"), t(n).addClass("tabs__content_state_current"), e.preventDefault()
                    })
                }
                return e
            }(), t.fn[a] = function() {
                return this.each(function() {
                    if (!t.data(this, "plugin_" + a)) return t.data(this, "plugin_" + a, new n(this))
                })
            }
        }(jQuery, window, document)
    }.call(this), define("uwinTabs", function() {}), "object" != typeof JSON && (JSON = {}),
    function() {
        function f(t) {
            return t < 10 ? "0" + t : t
        }

        function quote(t) {
            return escapable.lastIndex = 0, escapable.test(t) ? '"' + t.replace(escapable, function(t) {
                var e = meta[t];
                return "string" == typeof e ? e : "\\u" + ("0000" + t.charCodeAt(0).toString(16)).slice(-4)
            }) + '"' : '"' + t + '"'
        }

        function str(t, e) {
            var i, n, a, r, s, o = gap,
                l = e[t];
            switch (l && "object" == typeof l && "function" == typeof l.toJSON && (l = l.toJSON(t)), "function" == typeof rep && (l = rep.call(e, t, l)), typeof l) {
                case "string":
                    return quote(l);
                case "number":
                    return isFinite(l) ? String(l) : "null";
                case "boolean":
                case "null":
                    return String(l);
                case "object":
                    if (!l) return "null";
                    if (gap += indent, s = [], "[object Array]" === Object.prototype.toString.apply(l)) {
                        for (r = l.length, i = 0; i < r; i += 1) s[i] = str(i, l) || "null";
                        return a = 0 === s.length ? "[]" : gap ? "[\n" + gap + s.join(",\n" + gap) + "\n" + o + "]" : "[" + s.join(",") + "]", gap = o, a
                    }
                    if (rep && "object" == typeof rep)
                        for (r = rep.length, i = 0; i < r; i += 1) "string" == typeof rep[i] && (n = rep[i], a = str(n, l), a && s.push(quote(n) + (gap ? ": " : ":") + a));
                    else
                        for (n in l) Object.prototype.hasOwnProperty.call(l, n) && (a = str(n, l), a && s.push(quote(n) + (gap ? ": " : ":") + a));
                    return a = 0 === s.length ? "{}" : gap ? "{\n" + gap + s.join(",\n" + gap) + "\n" + o + "}" : "{" + s.join(",") + "}", gap = o, a
            }
        }
        "function" != typeof Date.prototype.toJSON && (Date.prototype.toJSON = function() {
            return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z" : null
        }, String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function() {
            return this.valueOf()
        });
        var cx, escapable, gap, indent, meta, rep;
        "function" != typeof JSON.stringify && (escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, meta = {
            "\b": "\\b",
            "\t": "\\t",
            "\n": "\\n",
            "\f": "\\f",
            "\r": "\\r",
            '"': '\\"',
            "\\": "\\\\"
        }, JSON.stringify = function(t, e, i) {
            var n;
            if (gap = "", indent = "", "number" == typeof i)
                for (n = 0; n < i; n += 1) indent += " ";
            else "string" == typeof i && (indent = i);
            if (rep = e, e && "function" != typeof e && ("object" != typeof e || "number" != typeof e.length)) throw new Error("JSON.stringify");
            return str("", {
                "": t
            })
        }), "function" != typeof JSON.parse && (cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, JSON.parse = function(text, reviver) {
            function walk(t, e) {
                var i, n, a = t[e];
                if (a && "object" == typeof a)
                    for (i in a) Object.prototype.hasOwnProperty.call(a, i) && (n = walk(a, i), void 0 !== n ? a[i] = n : delete a[i]);
                return reviver.call(t, e, a)
            }
            var j;
            if (text = String(text), cx.lastIndex = 0, cx.test(text) && (text = text.replace(cx, function(t) {
                    return "\\u" + ("0000" + t.charCodeAt(0).toString(16)).slice(-4)
                })), /^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) return j = eval("(" + text + ")"), "function" == typeof reviver ? walk({
                "": j
            }, "") : j;
            throw new SyntaxError("JSON.parse")
        })
    }(), define("json2", function() {}),
    function(t) {
        "function" == typeof define && define.amd ? define("cookie", ["jquery"], t) : t("object" == typeof exports ? require("jquery") : jQuery)
    }(function(t) {
        function e(t) {
            return o.raw ? t : encodeURIComponent(t)
        }

        function i(t) {
            return o.raw ? t : decodeURIComponent(t)
        }

        function n(t) {
            return e(o.json ? JSON.stringify(t) : String(t))
        }

        function a(t) {
            0 === t.indexOf('"') && (t = t.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
            try {
                return t = decodeURIComponent(t.replace(s, " ")), o.json ? JSON.parse(t) : t
            } catch (e) {}
        }

        function r(e, i) {
            var n = o.raw ? e : a(e);
            return t.isFunction(i) ? i(n) : n
        }
        var s = /\+/g,
            o = t.cookie = function(a, s, l) {
                if (void 0 !== s && !t.isFunction(s)) {
                    if (l = t.extend({}, o.defaults, l), "number" == typeof l.expires) {
                        var u = l.expires,
                            c = l.expires = new Date;
                        c.setTime(+c + 864e5 * u)
                    }
                    return document.cookie = [e(a), "=", n(s), l.expires ? "; expires=" + l.expires.toUTCString() : "", l.path ? "; path=" + l.path : "", l.domain ? "; domain=" + l.domain : "", l.secure ? "; secure" : ""].join("")
                }
                for (var d = a ? void 0 : {}, p = document.cookie ? document.cookie.split("; ") : [], f = 0, h = p.length; f < h; f++) {
                    var m = p[f].split("="),
                        g = i(m.shift()),
                        v = m.join("=");
                    if (a && a === g) {
                        d = r(v, s);
                        break
                    }
                    a || void 0 === (v = r(v)) || (d[g] = v)
                }
                return d
            };
        o.defaults = {}, t.removeCookie = function(e, i) {
            return void 0 !== t.cookie(e) && (t.cookie(e, "", t.extend({}, i, {
                expires: -1
            })), !t.cookie(e))
        }
    }),
    function(t, e, i) {
        "$:nomunge";

        function n(t) {
            return t = t || location.href, "#" + t.replace(/^[^#]*#?(.*)$/, "$1")
        }
        var a, r = "hashchange",
            s = document,
            o = t.event.special,
            l = s.documentMode,
            u = "on" + r in e && (l === i || l > 7);
        t.fn[r] = function(t) {
            return t ? this.bind(r, t) : this.trigger(r)
        }, t.fn[r].delay = 50, o[r] = t.extend(o[r], {
            setup: function() {
                return !u && void t(a.start)
            },
            teardown: function() {
                return !u && void t(a.stop)
            }
        }), a = function() {
            function a() {
                var i = n(),
                    o = d(l);
                i !== l ? (c(l = i, o), t(e).trigger(r)) : o !== l && (location.href = location.href.replace(/#.*/, "") + o), s = setTimeout(a, t.fn[r].delay)
            }
            var s, o = {},
                l = n(),
                u = function(t) {
                    return t
                },
                c = u,
                d = u;
            return o.start = function() {
                s || a()
            }, o.stop = function() {
                s && clearTimeout(s), s = i
            }, o
        }()
    }(jQuery, this), define("hashchange", function() {}),
    function(t) {
        function e(e, n, s, o, l) {
            function u() {
                m.unbind("webkitTransitionEnd transitionend otransitionend oTransitionEnd"), n && i(n, s, o, l), l.startOrder = [], l.newOrder = [], l.origSort = [], l.checkSort = [], h.removeStyle(l.prefix + "filter, filter, " + l.prefix + "transform, transform, opacity, display").css(l.clean).removeAttr("data-checksum"), window.atob || h.css({
                    display: "none",
                    opacity: "0"
                }), m.removeStyle(l.prefix + "transition, transition, " + l.prefix + "perspective, perspective, " + l.prefix + "perspective-origin, perspective-origin, " + (l.resizeContainer ? "height" : "")), "list" == l.layoutMode ? (g.css({
                    display: l.targetDisplayList,
                    opacity: "1"
                }), l.origDisplay = l.targetDisplayList) : (g.css({
                    display: l.targetDisplayGrid,
                    opacity: "1"
                }), l.origDisplay = l.targetDisplayGrid), l.origLayout = l.layoutMode, setTimeout(function() {
                    if (h.removeStyle(l.prefix + "transition, transition"), l.mixing = !1, "function" == typeof l.onMixEnd) {
                        var t = l.onMixEnd.call(this, l);
                        l = t ? t : l
                    }
                })
            }
            if (clearInterval(l.failsafe), l.mixing = !0, l.filter = e, "function" == typeof l.onMixStart) {
                var c = l.onMixStart.call(this, l);
                l = c ? c : l
            }
            for (var d = l.transitionSpeed, c = 0; 2 > c; c++) {
                var p = 0 == c ? p = l.prefix : "";
                l.transition[p + "transition"] = "all " + d + "ms linear", l.transition[p + "transform"] = p + "translate3d(0,0,0)", l.perspective[p + "perspective"] = l.perspectiveDistance + "px", l.perspective[p + "perspective-origin"] = l.perspectiveOrigin
            }
            var f = l.targetSelector,
                h = o.find(f);
            h.each(function() {
                this.data = {}
            });
            var m = h.parent();
            m.css(l.perspective), l.easingFallback = "ease-in-out", "smooth" == l.easing && (l.easing = "cubic-bezier(0.25, 0.46, 0.45, 0.94)"), "snap" == l.easing && (l.easing = "cubic-bezier(0.77, 0, 0.175, 1)"), "windback" == l.easing && (l.easing = "cubic-bezier(0.175, 0.885, 0.320, 1.275)", l.easingFallback = "cubic-bezier(0.175, 0.885, 0.320, 1)"), "windup" == l.easing && (l.easing = "cubic-bezier(0.6, -0.28, 0.735, 0.045)", l.easingFallback = "cubic-bezier(0.6, 0.28, 0.735, 0.045)"), c = "list" == l.layoutMode && null != l.listEffects ? l.listEffects : l.effects, Array.prototype.indexOf && (l.fade = -1 < c.indexOf("fade") ? "0" : "", l.scale = -1 < c.indexOf("scale") ? "scale(.01)" : "", l.rotateZ = -1 < c.indexOf("rotateZ") ? "rotate(180deg)" : "", l.rotateY = -1 < c.indexOf("rotateY") ? "rotateY(90deg)" : "", l.rotateX = -1 < c.indexOf("rotateX") ? "rotateX(90deg)" : "", l.blur = -1 < c.indexOf("blur") ? "blur(8px)" : "", l.grayscale = -1 < c.indexOf("grayscale") ? "grayscale(100%)" : "");
            var g = t(),
                v = t(),
                _ = [],
                b = !1;
            "string" == typeof e ? _ = r(e) : (b = !0, t.each(e, function(t) {
                _[t] = r(this)
            })), "or" == l.filterLogic ? ("" == _[0] && _.shift(), 1 > _.length ? v = v.add(o.find(f + ":visible")) : h.each(function() {
                var e = t(this);
                if (b) {
                    var i = 0;
                    t.each(_, function(t) {
                        this.length ? e.is("." + this.join(", .")) && i++ : 0 < i && i++
                    }), i == _.length ? g = g.add(e) : v = v.add(e)
                } else e.is("." + _.join(", .")) ? g = g.add(e) : v = v.add(e)
            })) : (g = g.add(m.find(f + "." + _.join("."))), v = v.add(m.find(f + ":not(." + _.join(".") + "):visible"))), e = g.length;
            var y = t(),
                w = t(),
                x = t();
            if (v.each(function() {
                    var e = t(this);
                    "none" != e.css("display") && (y = y.add(e), x = x.add(e))
                }), g.filter(":visible").length == e && !y.length && !n) {
                if (l.origLayout == l.layoutMode) return u(), !1;
                if (1 == g.length) return "list" == l.layoutMode ? (o.addClass(l.listClass), o.removeClass(l.gridClass), x.css("display", l.targetDisplayList)) : (o.addClass(l.gridClass), o.removeClass(l.listClass), x.css("display", l.targetDisplayGrid)), u(), !1
            }
            if (l.origHeight = m.height(), g.length) {
                if (o.removeClass(l.failClass), g.each(function() {
                        var e = t(this);
                        "none" == e.css("display") ? w = w.add(e) : x = x.add(e)
                    }), l.origLayout != l.layoutMode && 0 == l.animateGridList) return "list" == l.layoutMode ? (o.addClass(l.listClass), o.removeClass(l.gridClass), x.css("display", l.targetDisplayList)) : (o.addClass(l.gridClass), o.removeClass(l.listClass), x.css("display", l.targetDisplayGrid)), u(), !1;
                if (!window.atob) return u(), !1;
                if (h.css(l.clean), x.each(function() {
                        this.data.origPos = t(this).offset()
                    }), "list" == l.layoutMode ? (o.addClass(l.listClass), o.removeClass(l.gridClass), w.css("display", l.targetDisplayList)) : (o.addClass(l.gridClass), o.removeClass(l.listClass), w.css("display", l.targetDisplayGrid)), w.each(function() {
                        this.data.showInterPos = t(this).offset()
                    }), y.each(function() {
                        this.data.hideInterPos = t(this).offset()
                    }), x.each(function() {
                        this.data.preInterPos = t(this).offset()
                    }), "list" == l.layoutMode ? x.css("display", l.targetDisplayList) : x.css("display", l.targetDisplayGrid), n && i(n, s, o, l), n && a(l.origSort, l.checkSort)) return u(), !1;
                for (y.hide(), w.each(function(e) {
                    this.data.finalPos = t(this).offset()
                }), x.each(function() {
                    this.data.finalPrePos = t(this).offset()
                }), l.newHeight = m.height(), n && i("reset", null, o, l), w.hide(), x.css("display", l.origDisplay), "block" == l.origDisplay ? (o.addClass(l.listClass), w.css("display", l.targetDisplayList)) : (o.removeClass(l.listClass), w.css("display", l.targetDisplayGrid)), l.resizeContainer && m.css("height", l.origHeight + "px"), e = {}, c = 0; 2 > c; c++) p = 0 == c ? p = l.prefix : "", e[p + "transform"] = l.scale + " " + l.rotateX + " " + l.rotateY + " " + l.rotateZ, e[p + "filter"] = l.blur + " " + l.grayscale;
                w.css(e), x.each(function() {
                    var e = this.data,
                        i = t(this);
                    i.hasClass("mix_tohide") ? (e.preTX = e.origPos.left - e.hideInterPos.left, e.preTY = e.origPos.top - e.hideInterPos.top) : (e.preTX = e.origPos.left - e.preInterPos.left, e.preTY = e.origPos.top - e.preInterPos.top);
                    for (var n = {}, a = 0; 2 > a; a++) {
                        var r = 0 == a ? r = l.prefix : "";
                        n[r + "transform"] = "translate(" + e.preTX + "px," + e.preTY + "px)"
                    }
                    i.css(n)
                }), "list" == l.layoutMode ? (o.addClass(l.listClass), o.removeClass(l.gridClass)) : (o.addClass(l.gridClass), o.removeClass(l.listClass)), setTimeout(function() {
                    if (l.resizeContainer) {
                        for (var e = {}, i = 0; 2 > i; i++) {
                            var n = 0 == i ? n = l.prefix : "";
                            e[n + "transition"] = "all " + d + "ms ease-in-out", e.height = l.newHeight + "px"
                        }
                        m.css(e)
                    }
                    for (y.css("opacity", l.fade), w.css("opacity", 1), w.each(function() {
                        var e = this.data;
                        e.tX = e.finalPos.left - e.showInterPos.left, e.tY = e.finalPos.top - e.showInterPos.top;
                        for (var i = {}, n = 0; 2 > n; n++) {
                            var a = 0 == n ? a = l.prefix : "";
                            i[a + "transition-property"] = a + "transform, " + a + "filter, opacity", i[a + "transition-timing-function"] = l.easing + ", linear, linear", i[a + "transition-duration"] = d + "ms", i[a + "transition-delay"] = "0", i[a + "transform"] = "translate(" + e.tX + "px," + e.tY + "px)", i[a + "filter"] = "none"
                        }
                        t(this).css("-webkit-transition", "all " + d + "ms " + l.easingFallback).css(i)
                    }), x.each(function() {
                        var e = this.data;
                        e.tX = 0 != e.finalPrePos.left ? e.finalPrePos.left - e.preInterPos.left : 0, e.tY = 0 != e.finalPrePos.left ? e.finalPrePos.top - e.preInterPos.top : 0;
                        for (var i = {}, n = 0; 2 > n; n++) {
                            var a = 0 == n ? a = l.prefix : "";
                            i[a + "transition"] = "all " + d + "ms " + l.easing, i[a + "transform"] = "translate(" + e.tX + "px," + e.tY + "px)"
                        }
                        t(this).css("-webkit-transition", "all " + d + "ms " + l.easingFallback).css(i)
                    }), e = {}, i = 0; 2 > i; i++) n = 0 == i ? n = l.prefix : "", e[n + "transition"] = "all " + d + "ms " + l.easing + ", " + n + "filter " + d + "ms linear, opacity " + d + "ms linear", e[n + "transform"] = l.scale + " " + l.rotateX + " " + l.rotateY + " " + l.rotateZ, e[n + "filter"] = l.blur + " " + l.grayscale, e.opacity = l.fade;
                    y.css(e), m.bind("webkitTransitionEnd transitionend otransitionend oTransitionEnd", function(e) {
                        (-1 < e.originalEvent.propertyName.indexOf("transform") || -1 < e.originalEvent.propertyName.indexOf("opacity")) && (-1 < f.indexOf(".") ? t(e.target).hasClass(f.replace(".", "")) && u() : t(e.target).is(f) && u())
                    })
                }, 10), l.failsafe = setTimeout(function() {
                    l.mixing && u()
                }, d + 400)
            } else {
                if (l.resizeContainer && m.css("height", l.origHeight + "px"), !window.atob) return u(), !1;
                y = v, setTimeout(function() {
                    if (m.css(l.perspective), l.resizeContainer) {
                        for (var t = {}, e = 0; 2 > e; e++) {
                            var i = 0 == e ? i = l.prefix : "";
                            t[i + "transition"] = "height " + d + "ms ease-in-out", t.height = l.minHeight + "px"
                        }
                        m.css(t)
                    }
                    if (h.css(l.transition), v.length) {
                        for (t = {}, e = 0; 2 > e; e++) i = 0 == e ? i = l.prefix : "", t[i + "transform"] = l.scale + " " + l.rotateX + " " + l.rotateY + " " + l.rotateZ, t[i + "filter"] = l.blur + " " + l.grayscale, t.opacity = l.fade;
                        y.css(t), m.bind("webkitTransitionEnd transitionend otransitionend oTransitionEnd", function(t) {
                            (-1 < t.originalEvent.propertyName.indexOf("transform") || -1 < t.originalEvent.propertyName.indexOf("opacity")) && (o.addClass(l.failClass), u())
                        })
                    } else l.mixing = !1
                }, 10)
            }
        }

        function i(e, i, n, a) {
            function r(t, i) {
                var n = isNaN(1 * t.attr(e)) ? t.attr(e).toLowerCase() : 1 * t.attr(e),
                    a = isNaN(1 * i.attr(e)) ? i.attr(e).toLowerCase() : 1 * i.attr(e);
                return n < a ? -1 : n > a ? 1 : 0
            }

            function s(t) {
                "asc" == i ? l.prepend(t).prepend(" ") : l.append(t).append(" ")
            }

            function o(t) {
                t = t.slice();
                for (var e = t.length, i = e; i--;) {
                    var n = parseInt(Math.random() * e),
                        a = t[i];
                    t[i] = t[n], t[n] = a
                }
                return t
            }
            n.find(a.targetSelector).wrapAll('<div class="mix_sorter"/>');
            var l = n.find(".mix_sorter");
            if (a.origSort.length || l.find(a.targetSelector + ":visible").each(function() {
                    t(this).wrap("<s/>"), a.origSort.push(t(this).parent().html().replace(/\s+/g, "")), t(this).unwrap()
                }), l.empty(), "reset" == e) t.each(a.startOrder, function() {
                l.append(this).append(" ")
            });
            else if ("default" == e) t.each(a.origOrder, function() {
                s(this)
            });
            else if ("random" == e) a.newOrder.length || (a.newOrder = o(a.startOrder)), t.each(a.newOrder, function() {
                l.append(this).append(" ")
            });
            else if ("custom" == e) t.each(i, function() {
                s(this)
            });
            else {
                if ("undefined" == typeof a.origOrder[0].attr(e)) return console.log("No such attribute found. Terminating"), !1;
                a.newOrder.length || (t.each(a.origOrder, function() {
                    a.newOrder.push(t(this))
                }), a.newOrder.sort(r)), t.each(a.newOrder, function() {
                    s(this)
                })
            }
            a.checkSort = [], l.find(a.targetSelector + ":visible").each(function(e) {
                var i = t(this);
                0 == e && i.attr("data-checksum", "1"), i.wrap("<s/>"), a.checkSort.push(i.parent().html().replace(/\s+/g, "")), i.unwrap()
            }), n.find(a.targetSelector).unwrap()
        }

        function n(t) {
            for (var e = ["Webkit", "Moz", "O", "ms"], i = 0; i < e.length; i++)
                if (e[i] + "Transition" in t.style) return e[i];
            return "transition" in t.style && ""
        }

        function a(t, e) {
            if (t.length != e.length) return !1;
            for (var i = 0; i < e.length; i++)
                if (t[i].compare && !t[i].compare(e[i]) || t[i] !== e[i]) return !1;
            return !0
        }

        function r(e) {
            e = e.replace(/\s{2,}/g, " ");
            var i = e.split(" ");
            return t.each(i, function(t) {
                "all" == this && (i[t] = "mix_all")
            }), "" == i[0] && i.shift(), i
        }
        var s = {
            init: function(a) {
                return this.each(function() {
                    var r = window.navigator.appVersion.match(/Chrome\/(\d+)\./),
                        r = !!r && parseInt(r[1], 10),
                        s = function(t) {
                            var e = t.parentElement,
                                i = document.createElement("div"),
                                n = document.createDocumentFragment();
                            e.insertBefore(i, t), n.appendChild(t), e.replaceChild(t, i)
                        };
                    (r && 31 == r || 32 == r) && s(this);
                    var o = {
                        targetSelector: ".mix",
                        filterSelector: ".filter",
                        sortSelector: ".sort",
                        buttonEvent: "click",
                        effects: ["fade", "scale"],
                        listEffects: null,
                        easing: "smooth",
                        layoutMode: "grid",
                        targetDisplayGrid: "inline-block",
                        targetDisplayList: "block",
                        listClass: "",
                        gridClass: "",
                        transitionSpeed: 600,
                        showOnLoad: "all",
                        sortOnLoad: !1,
                        multiFilter: !1,
                        filterLogic: "or",
                        resizeContainer: !0,
                        minHeight: 0,
                        failClass: "fail",
                        perspectiveDistance: "3000",
                        perspectiveOrigin: "50% 50%",
                        animateGridList: !0,
                        onMixLoad: null,
                        onMixStart: null,
                        onMixEnd: null,
                        container: null,
                        origOrder: [],
                        startOrder: [],
                        newOrder: [],
                        origSort: [],
                        checkSort: [],
                        filter: "",
                        mixing: !1,
                        origDisplay: "",
                        origLayout: "",
                        origHeight: 0,
                        newHeight: 0,
                        isTouch: !1,
                        resetDelay: 0,
                        failsafe: null,
                        prefix: "",
                        easingFallback: "ease-in-out",
                        transition: {},
                        perspective: {},
                        clean: {},
                        fade: "1",
                        scale: "",
                        rotateX: "",
                        rotateY: "",
                        rotateZ: "",
                        blur: "",
                        grayscale: ""
                    };
                    a && t.extend(o, a), this.config = o, t.support.touch = "ontouchend" in document, t.support.touch && (o.isTouch = !0, o.resetDelay = 350), o.container = t(this);
                    var l = o.container;
                    if (o.prefix = n(l[0]), o.prefix = o.prefix ? "-" + o.prefix.toLowerCase() + "-" : "", l.find(o.targetSelector).each(function() {
                            o.origOrder.push(t(this))
                        }), o.sortOnLoad) {
                        var u;
                        t.isArray(o.sortOnLoad) ? (r = o.sortOnLoad[0], u = o.sortOnLoad[1], t(o.sortSelector + "[data-sort=" + o.sortOnLoad[0] + "][data-order=" + o.sortOnLoad[1] + "]").addClass("active")) : (t(o.sortSelector + "[data-sort=" + o.sortOnLoad + "]").addClass("active"), r = o.sortOnLoad, o.sortOnLoad = "desc"), i(r, u, l, o)
                    }
                    for (u = 0; 2 > u; u++) r = 0 == u ? r = o.prefix : "", o.transition[r + "transition"] = "all " + o.transitionSpeed + "ms ease-in-out", o.perspective[r + "perspective"] = o.perspectiveDistance + "px", o.perspective[r + "perspective-origin"] = o.perspectiveOrigin;
                    for (u = 0; 2 > u; u++) r = 0 == u ? r = o.prefix : "", o.clean[r + "transition"] = "none";
                    "list" == o.layoutMode ? (l.addClass(o.listClass), o.origDisplay = o.targetDisplayList) : (l.addClass(o.gridClass), o.origDisplay = o.targetDisplayGrid), o.origLayout = o.layoutMode, u = o.showOnLoad.split(" "), t.each(u, function() {
                        t(o.filterSelector + '[data-filter="' + this + '"]').addClass("active")
                    }), l.find(o.targetSelector).addClass("mix_all"), "all" == u[0] && (u[0] = "mix_all", o.showOnLoad = "mix_all");
                    var c = t();
                    t.each(u, function() {
                        c = c.add(t("." + this))
                    }), c.each(function() {
                        var e = t(this);
                        "list" == o.layoutMode ? e.css("display", o.targetDisplayList) : e.css("display", o.targetDisplayGrid), e.css(o.transition)
                    }), setTimeout(function() {
                        o.mixing = !0, c.css("opacity", "1"), setTimeout(function() {
                            if ("list" == o.layoutMode ? c.removeStyle(o.prefix + "transition, transition").css({
                                    display: o.targetDisplayList,
                                    opacity: 1
                                }) : c.removeStyle(o.prefix + "transition, transition").css({
                                    display: o.targetDisplayGrid,
                                    opacity: 1
                                }), o.mixing = !1, "function" == typeof o.onMixLoad) {
                                var t = o.onMixLoad.call(this, o);
                                o = t ? t : o
                            }
                        }, o.transitionSpeed)
                    }, 10), o.filter = o.showOnLoad, t(o.sortSelector).bind(o.buttonEvent, function() {
                        if (!o.mixing) {
                            var i = t(this),
                                n = i.attr("data-sort"),
                                a = i.attr("data-order");
                            if (i.hasClass("active")) {
                                if ("random" != n) return !1
                            } else t(o.sortSelector).removeClass("active"), i.addClass("active");
                            l.find(o.targetSelector).each(function() {
                                o.startOrder.push(t(this))
                            }), e(o.filter, n, a, l, o)
                        }
                    }), t(o.filterSelector).bind(o.buttonEvent, function() {
                        if (!o.mixing) {
                            var i = t(this);
                            if (0 == o.multiFilter) t(o.filterSelector).removeClass("active"), i.addClass("active"), o.filter = i.attr("data-filter"), t(o.filterSelector + '[data-filter="' + o.filter + '"]').addClass("active");
                            else {
                                var n = i.attr("data-filter");
                                i.hasClass("active") ? (i.removeClass("active"), o.filter = o.filter.replace(RegExp("(\\s|^)" + n), "")) : (i.addClass("active"), o.filter = o.filter + " " + n)
                            }
                            e(o.filter, null, null, l, o)
                        }
                    })
                })
            },
            toGrid: function() {
                return this.each(function() {
                    var i = this.config;
                    "grid" != i.layoutMode && (i.layoutMode = "grid", e(i.filter, null, null, t(this), i))
                })
            },
            toList: function() {
                return this.each(function() {
                    var i = this.config;
                    "list" != i.layoutMode && (i.layoutMode = "list", e(i.filter, null, null, t(this), i))
                })
            },
            filter: function(i) {
                return this.each(function() {
                    var n = this.config;
                    n.mixing || (t(n.filterSelector).removeClass("active"), t(n.filterSelector + '[data-filter="' + i + '"]').addClass("active"), e(i, null, null, t(this), n))
                })
            },
            sort: function(i) {
                return this.each(function() {
                    var n = this.config,
                        a = t(this);
                    if (!n.mixing) {
                        if (t(n.sortSelector).removeClass("active"), t.isArray(i)) {
                            var r = i[0],
                                s = i[1];
                            t(n.sortSelector + '[data-sort="' + i[0] + '"][data-order="' + i[1] + '"]').addClass("active")
                        } else t(n.sortSelector + '[data-sort="' + i + '"]').addClass("active"), r = i, s = "desc";
                        a.find(n.targetSelector).each(function() {
                            n.startOrder.push(t(this))
                        }), e(n.filter, r, s, a, n)
                    }
                })
            },
            multimix: function(i) {
                return this.each(function() {
                    var n = this.config,
                        a = t(this);
                    multiOut = {
                        filter: n.filter,
                        sort: null,
                        order: "desc",
                        layoutMode: n.layoutMode
                    }, t.extend(multiOut, i), n.mixing || (t(n.filterSelector).add(n.sortSelector).removeClass("active"), t(n.filterSelector + '[data-filter="' + multiOut.filter + '"]').addClass("active"), "undefined" != typeof multiOut.sort && (t(n.sortSelector + '[data-sort="' + multiOut.sort + '"][data-order="' + multiOut.order + '"]').addClass("active"), a.find(n.targetSelector).each(function() {
                        n.startOrder.push(t(this))
                    })), n.layoutMode = multiOut.layoutMode, e(multiOut.filter, multiOut.sort, multiOut.order, a, n))
                })
            },
            remix: function(i) {
                return this.each(function() {
                    var n = this.config,
                        a = t(this);
                    n.origOrder = [], a.find(n.targetSelector).each(function() {
                        var e = t(this);
                        e.addClass("mix_all"), n.origOrder.push(e)
                    }), n.mixing || "undefined" == typeof i || (t(n.filterSelector).removeClass("active"), t(n.filterSelector + '[data-filter="' + i + '"]').addClass("active"), e(i, null, null, a, n))
                })
            }
        };
        t.fn.mixitup = function(t, e) {
            return s[t] ? s[t].apply(this, Array.prototype.slice.call(arguments, 1)) : "object" != typeof t && t ? void 0 : s.init.apply(this, arguments)
        }, t.fn.removeStyle = function(e) {
            return this.each(function() {
                var i = t(this);
                e = e.replace(/\s+/g, "");
                var n = e.split(",");
                t.each(n, function() {
                    var t = RegExp(this.toString() + "[^;]+;?", "g");
                    i.attr("style", function(e, i) {
                        if (i) return i.replace(t, "")
                    })
                })
            })
        }
    }(jQuery), define("mixitup", function() {}),
    function(t) {
        t.path = {};
        var e = {
            rotate: function(t, e) {
                var i = e * Math.PI / 180,
                    n = Math.cos(i),
                    a = Math.sin(i);
                return [n * t[0] - a * t[1], a * t[0] + n * t[1]]
            },
            scale: function(t, e) {
                return [e * t[0], e * t[1]]
            },
            add: function(t, e) {
                return [t[0] + e[0], t[1] + e[1]]
            },
            minus: function(t, e) {
                return [t[0] - e[0], t[1] - e[1]]
            }
        };
        t.path.bezier = function(i, n) {
            i.start = t.extend({
                angle: 0,
                length: .3333
            }, i.start), i.end = t.extend({
                angle: 0,
                length: .3333
            }, i.end), this.p1 = [i.start.x, i.start.y], this.p4 = [i.end.x, i.end.y];
            var a = e.minus(this.p4, this.p1),
                r = e.scale(a, i.start.length),
                s = e.scale(a, -1),
                o = e.scale(s, i.end.length);
            r = e.rotate(r, i.start.angle), this.p2 = e.add(this.p1, r), o = e.rotate(o, i.end.angle), this.p3 = e.add(this.p4, o), this.f1 = function(t) {
                return t * t * t
            }, this.f2 = function(t) {
                return 3 * t * t * (1 - t)
            }, this.f3 = function(t) {
                return 3 * t * (1 - t) * (1 - t)
            }, this.f4 = function(t) {
                return (1 - t) * (1 - t) * (1 - t)
            }, this.css = function(t) {
                var e = this.f1(t),
                    i = this.f2(t),
                    a = this.f3(t),
                    r = this.f4(t),
                    s = {};
                return n && (s.prevX = this.x, s.prevY = this.y), s.x = this.x = this.p1[0] * e + this.p2[0] * i + this.p3[0] * a + this.p4[0] * r + .5 | 0, s.y = this.y = this.p1[1] * e + this.p2[1] * i + this.p3[1] * a + this.p4[1] * r + .5 | 0, s.left = s.x + "px", s.top = s.y + "px", s
            }
        }, t.path.arc = function(t, e) {
            for (var i in t) this[i] = t[i];
            for (this.dir = this.dir || 1; this.start > this.end && this.dir > 0;) this.start -= 360;
            for (; this.start < this.end && this.dir < 0;) this.start += 360;
            this.css = function(t) {
                var i = (this.start * t + this.end * (1 - t)) * Math.PI / 180,
                    n = {};
                return e && (n.prevX = this.x, n.prevY = this.y), n.x = this.x = Math.sin(i) * this.radius + this.center[0] + .5 | 0, n.y = this.y = Math.cos(i) * this.radius + this.center[1] + .5 | 0, n.left = n.x + "px", n.top = n.y + "px", n
            }
        }, t.fx.step.path = function(e) {
            var i = e.end.css(1 - e.pos);
            null != i.prevX && t.cssHooks.transform.set(e.elem, "rotate(" + Math.atan2(i.prevY - i.y, i.prevX - i.x) + ")"), e.elem.style.top = i.top, e.elem.style.left = i.left
        }
    }(jQuery), define("bezier", function() {}),
    function() {
        function t(t) {
            this._value = t
        }

        function e(t, e, i, n) {
            var a, r, s = Math.pow(10, e);
            return r = (i(t * s) / s).toFixed(e), n && (a = new RegExp("0{1," + n + "}$"), r = r.replace(a, "")), r
        }

        function i(t, e, i) {
            var n;
            return n = e.indexOf("$") > -1 ? a(t, e, i) : e.indexOf("%") > -1 ? r(t, e, i) : e.indexOf(":") > -1 ? s(t, e) : l(t._value, e, i)
        }

        function n(t, e) {
            var i, n, a, r, s, l = e,
                u = ["KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
                c = !1;
            if (e.indexOf(":") > -1) t._value = o(e);
            else if (e === h) t._value = 0;
            else {
                for ("." !== p[f].delimiters.decimal && (e = e.replace(/\./g, "").replace(p[f].delimiters.decimal, ".")), i = new RegExp("[^a-zA-Z]" + p[f].abbreviations.thousand + "(?:\\)|(\\" + p[f].currency.symbol + ")?(?:\\))?)?$"), n = new RegExp("[^a-zA-Z]" + p[f].abbreviations.million + "(?:\\)|(\\" + p[f].currency.symbol + ")?(?:\\))?)?$"), a = new RegExp("[^a-zA-Z]" + p[f].abbreviations.billion + "(?:\\)|(\\" + p[f].currency.symbol + ")?(?:\\))?)?$"), r = new RegExp("[^a-zA-Z]" + p[f].abbreviations.trillion + "(?:\\)|(\\" + p[f].currency.symbol + ")?(?:\\))?)?$"), s = 0; s <= u.length && !(c = e.indexOf(u[s]) > -1 && Math.pow(1024, s + 1)); s++);
                t._value = (c ? c : 1) * (l.match(i) ? Math.pow(10, 3) : 1) * (l.match(n) ? Math.pow(10, 6) : 1) * (l.match(a) ? Math.pow(10, 9) : 1) * (l.match(r) ? Math.pow(10, 12) : 1) * (e.indexOf("%") > -1 ? .01 : 1) * ((e.split("-").length + Math.min(e.split("(").length - 1, e.split(")").length - 1)) % 2 ? 1 : -1) * Number(e.replace(/[^0-9\.]+/g, "")), t._value = c ? Math.ceil(t._value) : t._value
            }
            return t._value
        }

        function a(t, e, i) {
            var n, a = e.indexOf("$") <= 1,
                r = "";
            return e.indexOf(" $") > -1 ? (r = " ", e = e.replace(" $", "")) : e.indexOf("$ ") > -1 ? (r = " ", e = e.replace("$ ", "")) : e = e.replace("$", ""), n = l(t._value, e, i), a ? n.indexOf("(") > -1 || n.indexOf("-") > -1 ? (n = n.split(""), n.splice(1, 0, p[f].currency.symbol + r), n = n.join("")) : n = p[f].currency.symbol + r + n : n.indexOf(")") > -1 ? (n = n.split(""), n.splice(-1, 0, r + p[f].currency.symbol), n = n.join("")) : n = n + r + p[f].currency.symbol, n
        }

        function r(t, e, i) {
            var n, a = "",
                r = 100 * t._value;
            return e.indexOf(" %") > -1 ? (a = " ", e = e.replace(" %", "")) : e = e.replace("%", ""), n = l(r, e, i), n.indexOf(")") > -1 ? (n = n.split(""), n.splice(-1, 0, a + "%"), n = n.join("")) : n = n + a + "%", n
        }

        function s(t) {
            var e = Math.floor(t._value / 60 / 60),
                i = Math.floor((t._value - 3600 * e) / 60),
                n = Math.round(t._value - 3600 * e - 60 * i);
            return e + ":" + (10 > i ? "0" + i : i) + ":" + (10 > n ? "0" + n : n)
        }

        function o(t) {
            var e = t.split(":"),
                i = 0;
            return 3 === e.length ? (i += 3600 * Number(e[0]), i += 60 * Number(e[1]), i += Number(e[2])) : 2 === e.length && (i += 60 * Number(e[0]), i += Number(e[1])), Number(i)
        }

        function l(t, i, n) {
            var a, r, s, o, l, u, c = !1,
                d = !1,
                m = !1,
                g = "",
                v = "",
                _ = "",
                b = Math.abs(t),
                y = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
                w = "",
                x = !1;
            if (0 === t && null !== h) return h;
            if (i.indexOf("(") > -1 ? (c = !0, i = i.slice(1, -1)) : i.indexOf("+") > -1 && (d = !0, i = i.replace(/\+/g, "")), i.indexOf("a") > -1 && (i.indexOf(" a") > -1 ? (g = " ", i = i.replace(" a", "")) : i = i.replace("a", ""), b >= Math.pow(10, 12) ? (g += p[f].abbreviations.trillion, t /= Math.pow(10, 12)) : b < Math.pow(10, 12) && b >= Math.pow(10, 9) ? (g += p[f].abbreviations.billion, t /= Math.pow(10, 9)) : b < Math.pow(10, 9) && b >= Math.pow(10, 6) ? (g += p[f].abbreviations.million, t /= Math.pow(10, 6)) : b < Math.pow(10, 6) && b >= Math.pow(10, 3) && (g += p[f].abbreviations.thousand, t /= Math.pow(10, 3))), i.indexOf("b") > -1)
                for (i.indexOf(" b") > -1 ? (v = " ", i = i.replace(" b", "")) : i = i.replace("b", ""), s = 0; s <= y.length; s++)
                    if (a = Math.pow(1024, s), r = Math.pow(1024, s + 1), t >= a && r > t) {
                        v += y[s], a > 0 && (t /= a);
                        break
                    }
            return i.indexOf("o") > -1 && (i.indexOf(" o") > -1 ? (_ = " ", i = i.replace(" o", "")) : i = i.replace("o", ""), _ += p[f].ordinal(t)), i.indexOf("[.]") > -1 && (m = !0, i = i.replace("[.]", ".")), o = t.toString().split(".")[0], l = i.split(".")[1], u = i.indexOf(","), l ? (l.indexOf("[") > -1 ? (l = l.replace("]", ""), l = l.split("["), w = e(t, l[0].length + l[1].length, n, l[1].length)) : w = e(t, l.length, n), o = w.split(".")[0], w = w.split(".")[1].length ? p[f].delimiters.decimal + w.split(".")[1] : "", m && 0 === Number(w.slice(1)) && (w = "")) : o = e(t, null, n), o.indexOf("-") > -1 && (o = o.slice(1), x = !0), u > -1 && (o = o.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + p[f].delimiters.thousands)), 0 === i.indexOf(".") && (o = ""), (c && x ? "(" : "") + (!c && x ? "-" : "") + (!x && d ? "+" : "") + o + w + (_ ? _ : "") + (g ? g : "") + (v ? v : "") + (c && x ? ")" : "")
        }

        function u(t, e) {
            p[t] = e
        }
        var c, d = "1.5.2",
            p = {},
            f = "en",
            h = null,
            m = "0,0",
            g = "undefined" != typeof module && module.exports;
        c = function(e) {
            return c.isNumeral(e) ? e = e.value() : 0 === e || "undefined" == typeof e ? e = 0 : Number(e) || (e = c.fn.unformat(e)), new t(Number(e))
        }, c.version = d, c.isNumeral = function(e) {
            return e instanceof t
        }, c.language = function(t, e) {
            if (!t) return f;
            if (t && !e) {
                if (!p[t]) throw new Error("Unknown language : " + t);
                f = t
            }
            return (e || !p[t]) && u(t, e), c
        }, c.languageData = function(t) {
            if (!t) return p[f];
            if (!p[t]) throw new Error("Unknown language : " + t);
            return p[t]
        }, c.language("en", {
            delimiters: {
                thousands: ",",
                decimal: "."
            },
            abbreviations: {
                thousand: "k",
                million: "m",
                billion: "b",
                trillion: "t"
            },
            ordinal: function(t) {
                var e = t % 10;
                return 1 === ~~(t % 100 / 10) ? "th" : 1 === e ? "st" : 2 === e ? "nd" : 3 === e ? "rd" : "th"
            },
            currency: {
                symbol: "$"
            }
        }), c.zeroFormat = function(t) {
            h = "string" == typeof t ? t : null
        }, c.defaultFormat = function(t) {
            m = "string" == typeof t ? t : "0.0"
        }, c.fn = t.prototype = {
            clone: function() {
                return c(this)
            },
            format: function(t, e) {
                return i(this, t ? t : m, void 0 !== e ? e : Math.round)
            },
            unformat: function(t) {
                return "[object Number]" === Object.prototype.toString.call(t) ? t : n(this, t ? t : m)
            },
            value: function() {
                return this._value
            },
            valueOf: function() {
                return this._value
            },
            set: function(t) {
                return this._value = Number(t), this
            },
            add: function(t) {
                return this._value = this._value + Number(t), this
            },
            subtract: function(t) {
                return this._value = this._value - Number(t), this
            },
            multiply: function(t) {
                return this._value = this._value * Number(t), this
            },
            divide: function(t) {
                return this._value = this._value / Number(t), this
            },
            difference: function(t) {
                var e = this._value - Number(t);
                return 0 > e && (e = -e), e
            }
        }, g && (module.exports = c), "undefined" == typeof ender && (this.numeral = c), "function" == typeof define && define.amd && define("numeral", [], function() {
            return c
        })
    }.call(this), ! function(t) {
    var e = function() {
            return {
                isMsie: function() {
                    return !!/(msie|trident)/i.test(navigator.userAgent) && navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2]
                },
                isBlankString: function(t) {
                    return !t || /^\s*$/.test(t)
                },
                escapeRegExChars: function(t) {
                    return t.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&")
                },
                isString: function(t) {
                    return "string" == typeof t
                },
                isNumber: function(t) {
                    return "number" == typeof t
                },
                isArray: t.isArray,
                isFunction: t.isFunction,
                isObject: t.isPlainObject,
                isUndefined: function(t) {
                    return "undefined" == typeof t
                },
                toStr: function(t) {
                    return e.isUndefined(t) || null === t ? "" : t + ""
                },
                bind: t.proxy,
                each: function(e, i) {
                    function n(t, e) {
                        return i(e, t)
                    }
                    t.each(e, n)
                },
                map: t.map,
                filter: t.grep,
                every: function(e, i) {
                    var n = !0;
                    return e ? (t.each(e, function(t, a) {
                        return !!(n = i.call(null, a, t, e)) && void 0
                    }), !!n) : n
                },
                some: function(e, i) {
                    var n = !1;
                    return e ? (t.each(e, function(t, a) {
                        return !(n = i.call(null, a, t, e)) && void 0
                    }), !!n) : n
                },
                mixin: t.extend,
                getUniqueId: function() {
                    var t = 0;
                    return function() {
                        return t++
                    }
                }(),
                templatify: function(e) {
                    function i() {
                        return String(e)
                    }
                    return t.isFunction(e) ? e : i
                },
                defer: function(t) {
                    setTimeout(t, 0)
                },
                debounce: function(t, e, i) {
                    var n, a;
                    return function() {
                        var r, s, o = this,
                            l = arguments;
                        return r = function() {
                            n = null, i || (a = t.apply(o, l))
                        }, s = i && !n, clearTimeout(n), n = setTimeout(r, e), s && (a = t.apply(o, l)), a
                    }
                },
                throttle: function(t, e) {
                    var i, n, a, r, s, o;
                    return s = 0, o = function() {
                        s = new Date, a = null, r = t.apply(i, n)
                    },
                        function() {
                            var l = new Date,
                                u = e - (l - s);
                            return i = this, n = arguments, 0 >= u ? (clearTimeout(a), a = null, s = l, r = t.apply(i, n)) : a || (a = setTimeout(o, u)), r
                        }
                },
                noop: function() {}
            }
        }(),
        i = "0.10.5",
        n = function() {
            function t(t) {
                return t = e.toStr(t), t ? t.split(/\s+/) : []
            }

            function i(t) {
                return t = e.toStr(t), t ? t.split(/\W+/) : []
            }

            function n(t) {
                return function() {
                    var i = [].slice.call(arguments, 0);
                    return function(n) {
                        var a = [];
                        return e.each(i, function(i) {
                            a = a.concat(t(e.toStr(n[i])))
                        }), a
                    }
                }
            }
            return {
                nonword: i,
                whitespace: t,
                obj: {
                    nonword: n(i),
                    whitespace: n(t)
                }
            }
        }(),
        a = function() {
            function i(i) {
                this.maxSize = e.isNumber(i) ? i : 100, this.reset(), this.maxSize <= 0 && (this.set = this.get = t.noop)
            }

            function n() {
                this.head = this.tail = null
            }

            function a(t, e) {
                this.key = t, this.val = e, this.prev = this.next = null
            }
            return e.mixin(i.prototype, {
                set: function(t, e) {
                    var i, n = this.list.tail;
                    this.size >= this.maxSize && (this.list.remove(n), delete this.hash[n.key]), (i = this.hash[t]) ? (i.val = e, this.list.moveToFront(i)) : (i = new a(t, e), this.list.add(i), this.hash[t] = i, this.size++)
                },
                get: function(t) {
                    var e = this.hash[t];
                    return e ? (this.list.moveToFront(e), e.val) : void 0
                },
                reset: function() {
                    this.size = 0, this.hash = {}, this.list = new n
                }
            }), e.mixin(n.prototype, {
                add: function(t) {
                    this.head && (t.next = this.head, this.head.prev = t), this.head = t, this.tail = this.tail || t
                },
                remove: function(t) {
                    t.prev ? t.prev.next = t.next : this.head = t.next, t.next ? t.next.prev = t.prev : this.tail = t.prev
                },
                moveToFront: function(t) {
                    this.remove(t), this.add(t)
                }
            }), i
        }(),
        r = function() {
            function t(t) {
                this.prefix = ["__", t, "__"].join(""), this.ttlKey = "__ttl__", this.keyMatcher = new RegExp("^" + e.escapeRegExChars(this.prefix))
            }

            function i() {
                return (new Date).getTime()
            }

            function n(t) {
                return JSON.stringify(e.isUndefined(t) ? null : t)
            }

            function a(t) {
                return JSON.parse(t)
            }
            var r, s;
            try {
                r = window.localStorage, r.setItem("~~~", "!"), r.removeItem("~~~")
            } catch (o) {
                r = null
            }
            return s = r && window.JSON ? {
                _prefix: function(t) {
                    return this.prefix + t
                },
                _ttlKey: function(t) {
                    return this._prefix(t) + this.ttlKey
                },
                get: function(t) {
                    return this.isExpired(t) && this.remove(t), a(r.getItem(this._prefix(t)))
                },
                set: function(t, a, s) {
                    return e.isNumber(s) ? r.setItem(this._ttlKey(t), n(i() + s)) : r.removeItem(this._ttlKey(t)), r.setItem(this._prefix(t), n(a))
                },
                remove: function(t) {
                    return r.removeItem(this._ttlKey(t)), r.removeItem(this._prefix(t)), this
                },
                clear: function() {
                    var t, e, i = [],
                        n = r.length;
                    for (t = 0; n > t; t++)(e = r.key(t)).match(this.keyMatcher) && i.push(e.replace(this.keyMatcher, ""));
                    for (t = i.length; t--;) this.remove(i[t]);
                    return this
                },
                isExpired: function(t) {
                    var n = a(r.getItem(this._ttlKey(t)));
                    return !!(e.isNumber(n) && i() > n)
                }
            } : {
                get: e.noop,
                set: e.noop,
                remove: e.noop,
                clear: e.noop,
                isExpired: e.noop
            }, e.mixin(t.prototype, s), t
        }(),
        s = function() {
            function i(e) {
                e = e || {}, this.cancelled = !1, this.lastUrl = null, this._send = e.transport ? n(e.transport) : t.ajax, this._get = e.rateLimiter ? e.rateLimiter(this._get) : this._get, this._cache = e.cache === !1 ? new a(0) : l
            }

            function n(i) {
                return function(n, a) {
                    function r(t) {
                        e.defer(function() {
                            o.resolve(t)
                        })
                    }

                    function s(t) {
                        e.defer(function() {
                            o.reject(t)
                        })
                    }
                    var o = t.Deferred();
                    return i(n, a, r, s), o
                }
            }
            var r = 0,
                s = {},
                o = 6,
                l = new a(10);
            return i.setMaxPendingRequests = function(t) {
                o = t
            }, i.resetCache = function() {
                l.reset()
            }, e.mixin(i.prototype, {
                _get: function(t, e, i) {
                    function n(e) {
                        i && i(null, e), c._cache.set(t, e)
                    }

                    function a() {
                        i && i(!0)
                    }

                    function l() {
                        r--, delete s[t], c.onDeckRequestArgs && (c._get.apply(c, c.onDeckRequestArgs), c.onDeckRequestArgs = null)
                    }
                    var u, c = this;
                    this.cancelled || t !== this.lastUrl || ((u = s[t]) ? u.done(n).fail(a) : o > r ? (r++, s[t] = this._send(t, e).done(n).fail(a).always(l)) : this.onDeckRequestArgs = [].slice.call(arguments, 0))
                },
                get: function(t, i, n) {
                    var a;
                    return e.isFunction(i) && (n = i, i = {}), this.cancelled = !1, this.lastUrl = t, (a = this._cache.get(t)) ? e.defer(function() {
                        n && n(null, a)
                    }) : this._get(t, i, n), !!a
                },
                cancel: function() {
                    this.cancelled = !0
                }
            }), i
        }(),
        o = function() {
            function i(e) {
                e = e || {}, e.datumTokenizer && e.queryTokenizer || t.error("datumTokenizer and queryTokenizer are both required"), this.datumTokenizer = e.datumTokenizer, this.queryTokenizer = e.queryTokenizer, this.reset()
            }

            function n(t) {
                return t = e.filter(t, function(t) {
                    return !!t
                }), t = e.map(t, function(t) {
                    return t.toLowerCase()
                })
            }

            function a() {
                return {
                    ids: [],
                    children: {}
                }
            }

            function r(t) {
                for (var e = {}, i = [], n = 0, a = t.length; a > n; n++) e[t[n]] || (e[t[n]] = !0, i.push(t[n]));
                return i
            }

            function s(t, e) {
                function i(t, e) {
                    return t - e
                }
                var n = 0,
                    a = 0,
                    r = [];
                t = t.sort(i), e = e.sort(i);
                for (var s = t.length, o = e.length; s > n && o > a;) t[n] < e[a] ? n++ : t[n] > e[a] ? a++ : (r.push(t[n]), n++, a++);
                return r
            }
            return e.mixin(i.prototype, {
                bootstrap: function(t) {
                    this.datums = t.datums, this.trie = t.trie
                },
                add: function(t) {
                    var i = this;
                    t = e.isArray(t) ? t : [t], e.each(t, function(t) {
                        var r, s;
                        r = i.datums.push(t) - 1, s = n(i.datumTokenizer(t)), e.each(s, function(t) {
                            var e, n, s;
                            for (e = i.trie, n = t.split(""); s = n.shift();) e = e.children[s] || (e.children[s] = a()), e.ids.push(r)
                        })
                    })
                },
                get: function(t) {
                    var i, a, o = this;
                    return i = n(this.queryTokenizer(t)), e.each(i, function(t) {
                        var e, i, n, r;
                        if (a && 0 === a.length) return !1;
                        for (e = o.trie, i = t.split(""); e && (n = i.shift());) e = e.children[n];
                        return e && 0 === i.length ? (r = e.ids.slice(0), void(a = a ? s(a, r) : r)) : (a = [], !1)
                    }), a ? e.map(r(a), function(t) {
                        return o.datums[t]
                    }) : []
                },
                reset: function() {
                    this.datums = [], this.trie = a()
                },
                serialize: function() {
                    return {
                        datums: this.datums,
                        trie: this.trie
                    }
                }
            }), i
        }(),
        l = function() {
            function n(t) {
                return t.local || null
            }

            function a(n) {
                var a, r;
                return r = {
                    url: null,
                    thumbprint: "",
                    ttl: 864e5,
                    filter: null,
                    ajax: {}
                }, (a = n.prefetch || null) && (a = e.isString(a) ? {
                    url: a
                } : a, a = e.mixin(r, a), a.thumbprint = i + a.thumbprint, a.ajax.type = a.ajax.type || "GET", a.ajax.dataType = a.ajax.dataType || "json", !a.url && t.error("prefetch requires url to be set")), a
            }

            function r(i) {
                function n(t) {
                    return function(i) {
                        return e.debounce(i, t)
                    }
                }

                function a(t) {
                    return function(i) {
                        return e.throttle(i, t)
                    }
                }
                var r, s;
                return s = {
                    url: null,
                    cache: !0,
                    wildcard: "%QUERY",
                    replace: null,
                    rateLimitBy: "debounce",
                    rateLimitWait: 300,
                    send: null,
                    filter: null,
                    ajax: {}
                }, (r = i.remote || null) && (r = e.isString(r) ? {
                    url: r
                } : r, r = e.mixin(s, r), r.rateLimiter = /^throttle$/i.test(r.rateLimitBy) ? a(r.rateLimitWait) : n(r.rateLimitWait), r.ajax.type = r.ajax.type || "GET", r.ajax.dataType = r.ajax.dataType || "json", delete r.rateLimitBy, delete r.rateLimitWait, !r.url && t.error("remote requires url to be set")), r
            }
            return {
                local: n,
                prefetch: a,
                remote: r
            }
        }();
    ! function(i) {
        function a(e) {
            e && (e.local || e.prefetch || e.remote) || t.error("one of local, prefetch, or remote is required"), this.limit = e.limit || 5, this.sorter = u(e.sorter), this.dupDetector = e.dupDetector || c, this.local = l.local(e), this.prefetch = l.prefetch(e), this.remote = l.remote(e), this.cacheKey = this.prefetch ? this.prefetch.cacheKey || this.prefetch.url : null, this.index = new o({
                datumTokenizer: e.datumTokenizer,
                queryTokenizer: e.queryTokenizer
            }), this.storage = this.cacheKey ? new r(this.cacheKey) : null
        }

        function u(t) {
            function i(e) {
                return e.sort(t)
            }

            function n(t) {
                return t
            }
            return e.isFunction(t) ? i : n
        }

        function c() {
            return !1
        }
        var d, p;
        return d = i.Bloodhound, p = {
            data: "data",
            protocol: "protocol",
            thumbprint: "thumbprint"
        }, i.Bloodhound = a, a.noConflict = function() {
            return i.Bloodhound = d, a
        }, a.tokenizers = n, e.mixin(a.prototype, {
            _loadPrefetch: function(e) {
                function i(t) {
                    r.clear(), r.add(e.filter ? e.filter(t) : t), r._saveToStorage(r.index.serialize(), e.thumbprint, e.ttl)
                }
                var n, a, r = this;
                return (n = this._readFromStorage(e.thumbprint)) ? (this.index.bootstrap(n), a = t.Deferred().resolve()) : a = t.ajax(e.url, e.ajax).done(i), a
            },
            _getFromRemote: function(t, e) {
                function i(t, i) {
                    e(t ? [] : r.remote.filter ? r.remote.filter(i) : i)
                }
                var n, a, r = this;
                if (this.transport) return t = t || "", a = encodeURIComponent(t), n = this.remote.replace ? this.remote.replace(this.remote.url, t) : this.remote.url.replace(this.remote.wildcard, a), this.transport.get(n, this.remote.ajax, i)
            },
            _cancelLastRemoteRequest: function() {
                this.transport && this.transport.cancel()
            },
            _saveToStorage: function(t, e, i) {
                this.storage && (this.storage.set(p.data, t, i), this.storage.set(p.protocol, location.protocol, i), this.storage.set(p.thumbprint, e, i))
            },
            _readFromStorage: function(t) {
                var e, i = {};
                return this.storage && (i.data = this.storage.get(p.data), i.protocol = this.storage.get(p.protocol), i.thumbprint = this.storage.get(p.thumbprint)), e = i.thumbprint !== t || i.protocol !== location.protocol, i.data && !e ? i.data : null
            },
            _initialize: function() {
                function i() {
                    a.add(e.isFunction(r) ? r() : r)
                }
                var n, a = this,
                    r = this.local;
                return n = this.prefetch ? this._loadPrefetch(this.prefetch) : t.Deferred().resolve(), r && n.done(i), this.transport = this.remote ? new s(this.remote) : null, this.initPromise = n.promise()
            },
            initialize: function(t) {
                return !this.initPromise || t ? this._initialize() : this.initPromise
            },
            add: function(t) {
                this.index.add(t)
            },
            get: function(t, i) {
                function n(t) {
                    var n = r.slice(0);
                    e.each(t, function(t) {
                        var i;
                        return i = e.some(n, function(e) {
                            return a.dupDetector(t, e)
                        }), !i && n.push(t), n.length < a.limit
                    }), i && i(a.sorter(n))
                }
                var a = this,
                    r = [],
                    s = !1;
                r = this.index.get(t), r = this.sorter(r).slice(0, this.limit), r.length < this.limit ? s = this._getFromRemote(t, n) : this._cancelLastRemoteRequest(), s || (r.length > 0 || !this.transport) && i && i(r)
            },
            clear: function() {
                this.index.reset()
            },
            clearPrefetchCache: function() {
                this.storage && this.storage.clear()
            },
            clearRemoteCache: function() {
                this.transport && s.resetCache()
            },
            ttAdapter: function() {
                return e.bind(this.get, this)
            }
        }), a
    }(this);
    var u = function() {
            return {
                wrapper: '<span class="twitter-typeahead"></span>',
                dropdown: '<span class="tt-dropdown-menu"></span>',
                dataset: '<div class="tt-dataset-%CLASS%"></div>',
                suggestions: '<span class="tt-suggestions"></span>',
                suggestion: '<div class="tt-suggestion"></div>'
            }
        }(),
        c = function() {
            var t = {
                wrapper: {
                    position: "relative",
                    display: "inline-block"
                },
                hint: {
                    position: "absolute",
                    top: "0",
                    left: "0",
                    borderColor: "transparent",
                    boxShadow: "none",
                    opacity: "1"
                },
                input: {
                    position: "relative",
                    verticalAlign: "top",
                    backgroundColor: "transparent"
                },
                inputWithNoHint: {
                    position: "relative",
                    verticalAlign: "top"
                },
                dropdown: {
                    position: "absolute",
                    top: "100%",
                    left: "0",
                    zIndex: "100",
                    display: "none"
                },
                suggestions: {
                    display: "block"
                },
                suggestion: {
                    whiteSpace: "nowrap",
                    cursor: "pointer"
                },
                suggestionChild: {
                    whiteSpace: "normal"
                },
                ltr: {
                    left: "0",
                    right: "auto"
                },
                rtl: {
                    left: "auto",
                    right: " 0"
                }
            };
            return e.isMsie() && e.mixin(t.input, {
                backgroundImage: "url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)"
            }), e.isMsie() && e.isMsie() <= 7 && e.mixin(t.input, {
                marginTop: "-1px"
            }), t
        }(),
        d = function() {
            function i(e) {
                e && e.el || t.error("EventBus initialized without el"), this.$el = t(e.el)
            }
            var n = "typeahead:";
            return e.mixin(i.prototype, {
                trigger: function(t) {
                    var e = [].slice.call(arguments, 1);
                    this.$el.trigger(n + t, e)
                }
            }), i
        }(),
        p = function() {
            function t(t, e, i, n) {
                var a;
                if (!i) return this;
                for (e = e.split(l), i = n ? o(i, n) : i, this._callbacks = this._callbacks || {}; a = e.shift();) this._callbacks[a] = this._callbacks[a] || {
                    sync: [],
                    async: []
                }, this._callbacks[a][t].push(i);
                return this
            }

            function e(e, i, n) {
                return t.call(this, "async", e, i, n)
            }

            function i(e, i, n) {
                return t.call(this, "sync", e, i, n)
            }

            function n(t) {
                var e;
                if (!this._callbacks) return this;
                for (t = t.split(l); e = t.shift();) delete this._callbacks[e];
                return this
            }

            function a(t) {
                var e, i, n, a, s;
                if (!this._callbacks) return this;
                for (t = t.split(l), n = [].slice.call(arguments, 1);
                     (e = t.shift()) && (i = this._callbacks[e]);) a = r(i.sync, this, [e].concat(n)), s = r(i.async, this, [e].concat(n)), a() && u(s);
                return this
            }

            function r(t, e, i) {
                function n() {
                    for (var n, a = 0, r = t.length; !n && r > a; a += 1) n = t[a].apply(e, i) === !1;
                    return !n
                }
                return n
            }

            function s() {
                var t;
                return t = window.setImmediate ? function(t) {
                    setImmediate(function() {
                        t()
                    })
                } : function(t) {
                    setTimeout(function() {
                        t()
                    }, 0)
                }
            }

            function o(t, e) {
                return t.bind ? t.bind(e) : function() {
                    t.apply(e, [].slice.call(arguments, 0))
                }
            }
            var l = /\s+/,
                u = s();
            return {
                onSync: i,
                onAsync: e,
                off: n,
                trigger: a
            }
        }(),
        f = function(t) {
            function i(t, i, n) {
                for (var a, r = [], s = 0, o = t.length; o > s; s++) r.push(e.escapeRegExChars(t[s]));
                return a = n ? "\\b(" + r.join("|") + ")\\b" : "(" + r.join("|") + ")", i ? new RegExp(a) : new RegExp(a, "i")
            }
            var n = {
                node: null,
                pattern: null,
                tagName: "strong",
                className: null,
                wordsOnly: !1,
                caseSensitive: !1
            };
            return function(a) {
                function r(e) {
                    var i, n, r;
                    return (i = o.exec(e.data)) && (r = t.createElement(a.tagName), a.className && (r.className = a.className), n = e.splitText(i.index), n.splitText(i[0].length), r.appendChild(n.cloneNode(!0)), e.parentNode.replaceChild(r, n)), !!i
                }

                function s(t, e) {
                    for (var i, n = 3, a = 0; a < t.childNodes.length; a++) i = t.childNodes[a], i.nodeType === n ? a += e(i) ? 1 : 0 : s(i, e)
                }
                var o;
                a = e.mixin({}, n, a), a.node && a.pattern && (a.pattern = e.isArray(a.pattern) ? a.pattern : [a.pattern], o = i(a.pattern, a.caseSensitive, a.wordsOnly), s(a.node, r))
            }
        }(window.document),
        h = function() {
            function i(i) {
                var a, r, o, l, u = this;
                i = i || {}, i.input || t.error("input is missing"), a = e.bind(this._onBlur, this), r = e.bind(this._onFocus, this), o = e.bind(this._onKeydown, this), l = e.bind(this._onInput, this), this.$hint = t(i.hint), this.$input = t(i.input).on("blur.tt", a).on("focus.tt", r).on("keydown.tt", o), 0 === this.$hint.length && (this.setHint = this.getHint = this.clearHint = this.clearHintIfInvalid = e.noop), e.isMsie() ? this.$input.on("keydown.tt keypress.tt cut.tt paste.tt", function(t) {
                    s[t.which || t.keyCode] || e.defer(e.bind(u._onInput, u, t))
                }) : this.$input.on("input.tt", l), this.query = this.$input.val(), this.$overflowHelper = n(this.$input)
            }

            function n(e) {
                return t('<pre aria-hidden="true"></pre>').css({
                    position: "absolute",
                    visibility: "hidden",
                    whiteSpace: "pre",
                    fontFamily: e.css("font-family"),
                    fontSize: e.css("font-size"),
                    fontStyle: e.css("font-style"),
                    fontVariant: e.css("font-variant"),
                    fontWeight: e.css("font-weight"),
                    wordSpacing: e.css("word-spacing"),
                    letterSpacing: e.css("letter-spacing"),
                    textIndent: e.css("text-indent"),
                    textRendering: e.css("text-rendering"),
                    textTransform: e.css("text-transform")
                }).insertAfter(e)
            }

            function a(t, e) {
                return i.normalizeQuery(t) === i.normalizeQuery(e)
            }

            function r(t) {
                return t.altKey || t.ctrlKey || t.metaKey || t.shiftKey
            }
            var s;
            return s = {
                9: "tab",
                27: "esc",
                37: "left",
                39: "right",
                13: "enter",
                38: "up",
                40: "down"
            }, i.normalizeQuery = function(t) {
                return (t || "").replace(/^\s*/g, "").replace(/\s{2,}/g, " ")
            }, e.mixin(i.prototype, p, {
                _onBlur: function() {
                    this.resetInputValue(), this.trigger("blurred")
                },
                _onFocus: function() {
                    this.trigger("focused")
                },
                _onKeydown: function(t) {
                    var e = s[t.which || t.keyCode];
                    this._managePreventDefault(e, t), e && this._shouldTrigger(e, t) && this.trigger(e + "Keyed", t)
                },
                _onInput: function() {
                    this._checkInputValue()
                },
                _managePreventDefault: function(t, e) {
                    var i, n, a;
                    switch (t) {
                        case "tab":
                            n = this.getHint(), a = this.getInputValue(), i = n && n !== a && !r(e);
                            break;
                        case "up":
                        case "down":
                            i = !r(e);
                            break;
                        default:
                            i = !1
                    }
                    i && e.preventDefault()
                },
                _shouldTrigger: function(t, e) {
                    var i;
                    switch (t) {
                        case "tab":
                            i = !r(e);
                            break;
                        default:
                            i = !0
                    }
                    return i
                },
                _checkInputValue: function() {
                    var t, e, i;
                    t = this.getInputValue(), e = a(t, this.query), i = !!e && this.query.length !== t.length, this.query = t, e ? i && this.trigger("whitespaceChanged", this.query) : this.trigger("queryChanged", this.query)
                },
                focus: function() {
                    this.$input.focus()
                },
                blur: function() {
                    this.$input.blur()
                },
                getQuery: function() {
                    return this.query
                },
                setQuery: function(t) {
                    this.query = t
                },
                getInputValue: function() {
                    return this.$input.val()
                },
                setInputValue: function(t, e) {
                    this.$input.val(t), e ? this.clearHint() : this._checkInputValue()
                },
                resetInputValue: function() {
                    this.setInputValue(this.query, !0)
                },
                getHint: function() {
                    return this.$hint.val()
                },
                setHint: function(t) {
                    this.$hint.val(t)
                },
                clearHint: function() {
                    this.setHint("")
                },
                clearHintIfInvalid: function() {
                    var t, e, i, n;
                    t = this.getInputValue(), e = this.getHint(), i = t !== e && 0 === e.indexOf(t), n = "" !== t && i && !this.hasOverflow(), !n && this.clearHint()
                },
                getLanguageDirection: function() {
                    return (this.$input.css("direction") || "ltr").toLowerCase()
                },
                hasOverflow: function() {
                    var t = this.$input.width() - 2;
                    return this.$overflowHelper.text(this.getInputValue()), this.$overflowHelper.width() >= t
                },
                isCursorAtEnd: function() {
                    var t, i, n;
                    return t = this.$input.val().length, i = this.$input[0].selectionStart, e.isNumber(i) ? i === t : !document.selection || (n = document.selection.createRange(), n.moveStart("character", -t), t === n.text.length)
                },
                destroy: function() {
                    this.$hint.off(".tt"), this.$input.off(".tt"), this.$hint = this.$input = this.$overflowHelper = null
                }
            }), i
        }(),
        m = function() {
            function i(i) {
                i = i || {}, i.templates = i.templates || {}, i.source || t.error("missing source"), i.name && !r(i.name) && t.error("invalid dataset name: " + i.name), this.query = null, this.highlight = !!i.highlight, this.name = i.name || e.getUniqueId(), this.source = i.source, this.displayFn = n(i.display || i.displayKey), this.templates = a(i.templates, this.displayFn), this.$el = t(u.dataset.replace("%CLASS%", this.name))
            }

            function n(t) {
                function i(e) {
                    return e[t]
                }
                return t = t || "value", e.isFunction(t) ? t : i
            }

            function a(t, i) {
                function n(t) {
                    return "<p>" + i(t) + "</p>"
                }
                return {
                    empty: t.empty && e.templatify(t.empty),
                    header: t.header && e.templatify(t.header),
                    footer: t.footer && e.templatify(t.footer),
                    suggestion: t.suggestion || n
                }
            }

            function r(t) {
                return /^[_a-zA-Z0-9-]+$/.test(t)
            }
            var s = "ttDataset",
                o = "ttValue",
                l = "ttDatum";
            return i.extractDatasetName = function(e) {
                return t(e).data(s)
            }, i.extractValue = function(e) {
                return t(e).data(o)
            }, i.extractDatum = function(e) {
                return t(e).data(l)
            }, e.mixin(i.prototype, p, {
                _render: function(i, n) {
                    function a() {
                        return m.templates.empty({
                            query: i,
                            isEmpty: !0
                        })
                    }

                    function r() {
                        function a(e) {
                            var i;
                            return i = t(u.suggestion).append(m.templates.suggestion(e)).data(s, m.name).data(o, m.displayFn(e)).data(l, e), i.children().each(function() {
                                t(this).css(c.suggestionChild)
                            }), i
                        }
                        var r, d;
                        return r = t(u.suggestions).css(c.suggestions), d = e.map(n, a), r.append.apply(r, d), m.highlight && f({
                            className: "tt-highlight",
                            node: r[0],
                            pattern: i
                        }), r
                    }

                    function d() {
                        return m.templates.header({
                            query: i,
                            isEmpty: !h
                        })
                    }

                    function p() {
                        return m.templates.footer({
                            query: i,
                            isEmpty: !h
                        })
                    }
                    if (this.$el) {
                        var h, m = this;
                        this.$el.empty(), h = n && n.length, !h && this.templates.empty ? this.$el.html(a()).prepend(m.templates.header ? d() : null).append(m.templates.footer ? p() : null) : h && this.$el.html(r()).prepend(m.templates.header ? d() : null).append(m.templates.footer ? p() : null), this.trigger("rendered")
                    }
                },
                getRoot: function() {
                    return this.$el
                },
                update: function(t) {
                    function e(e) {
                        i.canceled || t !== i.query || i._render(t, e)
                    }
                    var i = this;
                    this.query = t, this.canceled = !1, this.source(t, e)
                },
                cancel: function() {
                    this.canceled = !0
                },
                clear: function() {
                    this.cancel(), this.$el.empty(), this.trigger("rendered")
                },
                isEmpty: function() {
                    return this.$el.is(":empty")
                },
                destroy: function() {
                    this.$el = null
                }
            }), i
        }(),
        g = function() {
            function i(i) {
                var a, r, s, o = this;
                i = i || {}, i.menu || t.error("menu is required"), this.isOpen = !1, this.isEmpty = !0, this.datasets = e.map(i.datasets, n), a = e.bind(this._onSuggestionClick, this), r = e.bind(this._onSuggestionMouseEnter, this), s = e.bind(this._onSuggestionMouseLeave, this), this.$menu = t(i.menu).on("click.tt", ".tt-suggestion", a).on("mouseenter.tt", ".tt-suggestion", r).on("mouseleave.tt", ".tt-suggestion", s), e.each(this.datasets, function(t) {
                    o.$menu.append(t.getRoot()), t.onSync("rendered", o._onRendered, o)
                })
            }

            function n(t) {
                return new m(t)
            }
            return e.mixin(i.prototype, p, {
                _onSuggestionClick: function(e) {
                    this.trigger("suggestionClicked", t(e.currentTarget))
                },
                _onSuggestionMouseEnter: function(e) {
                    this._removeCursor(), this._setCursor(t(e.currentTarget), !0)
                },
                _onSuggestionMouseLeave: function() {
                    this._removeCursor()
                },
                _onRendered: function() {
                    function t(t) {
                        return t.isEmpty()
                    }
                    this.isEmpty = e.every(this.datasets, t), this.isEmpty ? this._hide() : this.isOpen && this._show(), this.trigger("datasetRendered")
                },
                _hide: function() {
                    this.$menu.hide()
                },
                _show: function() {
                    this.$menu.css("display", "block")
                },
                _getSuggestions: function() {
                    return this.$menu.find(".tt-suggestion")
                },
                _getCursor: function() {
                    return this.$menu.find(".tt-cursor").first()
                },
                _setCursor: function(t, e) {
                    t.first().addClass("tt-cursor"), !e && this.trigger("cursorMoved")
                },
                _removeCursor: function() {
                    this._getCursor().removeClass("tt-cursor")
                },
                _moveCursor: function(t) {
                    var e, i, n, a;
                    if (this.isOpen) {
                        if (i = this._getCursor(), e = this._getSuggestions(), this._removeCursor(), n = e.index(i) + t, n = (n + 1) % (e.length + 1) - 1, -1 === n) return void this.trigger("cursorRemoved"); - 1 > n && (n = e.length - 1), this._setCursor(a = e.eq(n)), this._ensureVisible(a)
                    }
                },
                _ensureVisible: function(t) {
                    var e, i, n, a;
                    e = t.position().top, i = e + t.outerHeight(!0), n = this.$menu.scrollTop(), a = this.$menu.height() + parseInt(this.$menu.css("paddingTop"), 10) + parseInt(this.$menu.css("paddingBottom"), 10), 0 > e ? this.$menu.scrollTop(n + e) : i > a && this.$menu.scrollTop(n + (i - a))
                },
                close: function() {
                    this.isOpen && (this.isOpen = !1, this._removeCursor(), this._hide(), this.trigger("closed"))
                },
                open: function() {
                    this.isOpen || (this.isOpen = !0, !this.isEmpty && this._show(), this.trigger("opened"))
                },
                setLanguageDirection: function(t) {
                    this.$menu.css("ltr" === t ? c.ltr : c.rtl)
                },
                moveCursorUp: function() {
                    this._moveCursor(-1)
                },
                moveCursorDown: function() {
                    this._moveCursor(1)
                },
                getDatumForSuggestion: function(t) {
                    var e = null;
                    return t.length && (e = {
                        raw: m.extractDatum(t),
                        value: m.extractValue(t),
                        datasetName: m.extractDatasetName(t)
                    }), e
                },
                getDatumForCursor: function() {
                    return this.getDatumForSuggestion(this._getCursor().first())
                },
                getDatumForTopSuggestion: function() {
                    return this.getDatumForSuggestion(this._getSuggestions().first())
                },
                update: function(t) {
                    function i(e) {
                        e.update(t)
                    }
                    e.each(this.datasets, i)
                },
                empty: function() {
                    function t(t) {
                        t.clear()
                    }
                    e.each(this.datasets, t), this.isEmpty = !0
                },
                isVisible: function() {
                    return this.isOpen && !this.isEmpty
                },
                destroy: function() {
                    function t(t) {
                        t.destroy()
                    }
                    this.$menu.off(".tt"), this.$menu = null, e.each(this.datasets, t)
                }
            }), i
        }(),
        v = function() {
            function i(i) {
                var a, r, s;
                i = i || {}, i.input || t.error("missing input"), this.isActivated = !1, this.autoselect = !!i.autoselect, this.minLength = e.isNumber(i.minLength) ? i.minLength : 1, this.$node = n(i.input, i.withHint), a = this.$node.find(".tt-dropdown-menu"), r = this.$node.find(".tt-input"), s = this.$node.find(".tt-hint"), r.on("blur.tt", function(t) {
                    var i, n, s;
                    i = document.activeElement, n = a.is(i), s = a.has(i).length > 0, e.isMsie() && (n || s) && (t.preventDefault(), t.stopImmediatePropagation(), e.defer(function() {
                        r.focus()
                    }))
                }), a.on("mousedown.tt", function(t) {
                    t.preventDefault()
                }), this.eventBus = i.eventBus || new d({
                    el: r
                }), this.dropdown = new g({
                    menu: a,
                    datasets: i.datasets
                }).onSync("suggestionClicked", this._onSuggestionClicked, this).onSync("cursorMoved", this._onCursorMoved, this).onSync("cursorRemoved", this._onCursorRemoved, this).onSync("opened", this._onOpened, this).onSync("closed", this._onClosed, this).onAsync("datasetRendered", this._onDatasetRendered, this), this.input = new h({
                    input: r,
                    hint: s
                }).onSync("focused", this._onFocused, this).onSync("blurred", this._onBlurred, this).onSync("enterKeyed", this._onEnterKeyed, this).onSync("tabKeyed", this._onTabKeyed, this).onSync("escKeyed", this._onEscKeyed, this).onSync("upKeyed", this._onUpKeyed, this).onSync("downKeyed", this._onDownKeyed, this).onSync("leftKeyed", this._onLeftKeyed, this).onSync("rightKeyed", this._onRightKeyed, this).onSync("queryChanged", this._onQueryChanged, this).onSync("whitespaceChanged", this._onWhitespaceChanged, this), this._setLanguageDirection()
            }

            function n(e, i) {
                var n, r, o, l;
                n = t(e), r = t(u.wrapper).css(c.wrapper), o = t(u.dropdown).css(c.dropdown), l = n.clone().css(c.hint).css(a(n)), l.val("").removeData().addClass("tt-hint").removeAttr("id name placeholder required").prop("readonly", !0).attr({
                    autocomplete: "off",
                    spellcheck: "false",
                    tabindex: -1
                }), n.data(s, {
                    dir: n.attr("dir"),
                    autocomplete: n.attr("autocomplete"),
                    spellcheck: n.attr("spellcheck"),
                    style: n.attr("style")
                }), n.addClass("tt-input").attr({
                    autocomplete: "off",
                    spellcheck: !1
                }).css(i ? c.input : c.inputWithNoHint);
                try {
                    !n.attr("dir") && n.attr("dir", "auto")
                } catch (d) {}
                return n.wrap(r).parent().prepend(i ? l : null).append(o)
            }

            function a(t) {
                return {
                    backgroundAttachment: t.css("background-attachment"),
                    backgroundClip: t.css("background-clip"),
                    backgroundColor: t.css("background-color"),
                    backgroundImage: t.css("background-image"),
                    backgroundOrigin: t.css("background-origin"),
                    backgroundPosition: t.css("background-position"),
                    backgroundRepeat: t.css("background-repeat"),
                    backgroundSize: t.css("background-size")
                }
            }

            function r(t) {
                var i = t.find(".tt-input");
                e.each(i.data(s), function(t, n) {
                    e.isUndefined(t) ? i.removeAttr(n) : i.attr(n, t)
                }), i.detach().removeData(s).removeClass("tt-input").insertAfter(t), t.remove()
            }
            var s = "ttAttrs";
            return e.mixin(i.prototype, {
                _onSuggestionClicked: function(t, e) {
                    var i;
                    (i = this.dropdown.getDatumForSuggestion(e)) && this._select(i)
                },
                _onCursorMoved: function() {
                    var t = this.dropdown.getDatumForCursor();
                    this.input.setInputValue(t.value, !0), this.eventBus.trigger("cursorchanged", t.raw, t.datasetName)
                },
                _onCursorRemoved: function() {
                    this.input.resetInputValue(), this._updateHint()
                },
                _onDatasetRendered: function() {
                    this._updateHint()
                },
                _onOpened: function() {
                    this._updateHint(), this.eventBus.trigger("opened")
                },
                _onClosed: function() {
                    this.input.clearHint(), this.eventBus.trigger("closed")
                },
                _onFocused: function() {
                    this.isActivated = !0, this.dropdown.open()
                },
                _onBlurred: function() {
                    this.isActivated = !1, this.dropdown.empty(), this.dropdown.close()
                },
                _onEnterKeyed: function(t, e) {
                    var i, n;
                    i = this.dropdown.getDatumForCursor(), n = this.dropdown.getDatumForTopSuggestion(), i ? (this._select(i), e.preventDefault()) : this.autoselect && n && (this._select(n), e.preventDefault())
                },
                _onTabKeyed: function(t, e) {
                    var i;
                    (i = this.dropdown.getDatumForCursor()) ? (this._select(i), e.preventDefault()) : this._autocomplete(!0)
                },
                _onEscKeyed: function() {
                    this.dropdown.close(), this.input.resetInputValue()
                },
                _onUpKeyed: function() {
                    var t = this.input.getQuery();
                    this.dropdown.isEmpty && t.length >= this.minLength ? this.dropdown.update(t) : this.dropdown.moveCursorUp(), this.dropdown.open()
                },
                _onDownKeyed: function() {
                    var t = this.input.getQuery();
                    this.dropdown.isEmpty && t.length >= this.minLength ? this.dropdown.update(t) : this.dropdown.moveCursorDown(), this.dropdown.open()
                },
                _onLeftKeyed: function() {
                    "rtl" === this.dir && this._autocomplete()
                },
                _onRightKeyed: function() {
                    "ltr" === this.dir && this._autocomplete()
                },
                _onQueryChanged: function(t, e) {
                    this.input.clearHintIfInvalid(), e.length >= this.minLength ? this.dropdown.update(e) : this.dropdown.empty(), this.dropdown.open(), this._setLanguageDirection()
                },
                _onWhitespaceChanged: function() {
                    this._updateHint(), this.dropdown.open()
                },
                _setLanguageDirection: function() {
                    var t;
                    this.dir !== (t = this.input.getLanguageDirection()) && (this.dir = t, this.$node.css("direction", t), this.dropdown.setLanguageDirection(t))
                },
                _updateHint: function() {
                    var t, i, n, a, r, s;
                    t = this.dropdown.getDatumForTopSuggestion(), t && this.dropdown.isVisible() && !this.input.hasOverflow() ? (i = this.input.getInputValue(), n = h.normalizeQuery(i), a = e.escapeRegExChars(n), r = new RegExp("^(?:" + a + ")(.+$)", "i"), s = r.exec(t.value), s ? this.input.setHint(i + s[1]) : this.input.clearHint()) : this.input.clearHint()
                },
                _autocomplete: function(t) {
                    var e, i, n, a;
                    e = this.input.getHint(), i = this.input.getQuery(), n = t || this.input.isCursorAtEnd(), e && i !== e && n && (a = this.dropdown.getDatumForTopSuggestion(), a && this.input.setInputValue(a.value), this.eventBus.trigger("autocompleted", a.raw, a.datasetName))
                },
                _select: function(t) {
                    this.input.setQuery(t.value), this.input.setInputValue(t.value, !0), this._setLanguageDirection(), this.eventBus.trigger("selected", t.raw, t.datasetName), this.dropdown.close(), e.defer(e.bind(this.dropdown.empty, this.dropdown))
                },
                open: function() {
                    this.dropdown.open()
                },
                close: function() {
                    this.dropdown.close()
                },
                setVal: function(t) {
                    t = e.toStr(t), this.isActivated ? this.input.setInputValue(t) : (this.input.setQuery(t), this.input.setInputValue(t, !0)), this._setLanguageDirection()
                },
                getVal: function() {
                    return this.input.getQuery()
                },
                destroy: function() {
                    this.input.destroy(), this.dropdown.destroy(), r(this.$node), this.$node = null
                }
            }), i
        }();
    ! function() {
        var i, n, a;
        i = t.fn.typeahead, n = "ttTypeahead", a = {
            initialize: function(i, a) {
                function r() {
                    var r, s, o = t(this);
                    e.each(a, function(t) {
                        t.highlight = !!i.highlight
                    }), s = new v({
                        input: o,
                        eventBus: r = new d({
                            el: o
                        }),
                        withHint: !!e.isUndefined(i.hint) || !!i.hint,
                        minLength: i.minLength,
                        autoselect: i.autoselect,
                        datasets: a
                    }), o.data(n, s)
                }
                return a = e.isArray(a) ? a : [].slice.call(arguments, 1), i = i || {}, this.each(r)
            },
            open: function() {
                function e() {
                    var e, i = t(this);
                    (e = i.data(n)) && e.open()
                }
                return this.each(e)
            },
            close: function() {
                function e() {
                    var e, i = t(this);
                    (e = i.data(n)) && e.close()
                }
                return this.each(e)
            },
            val: function(e) {
                function i() {
                    var i, a = t(this);
                    (i = a.data(n)) && i.setVal(e)
                }

                function a(t) {
                    var e, i;
                    return (e = t.data(n)) && (i = e.getVal()), i
                }
                return arguments.length ? this.each(i) : a(this.first())
            },
            destroy: function() {
                function e() {
                    var e, i = t(this);
                    (e = i.data(n)) && (e.destroy(), i.removeData(n))
                }
                return this.each(e)
            }
        }, t.fn.typeahead = function(e) {
            var i;
            return a[e] && "initialize" !== e ? (i = this.filter(function() {
                return !!t(this).data(n)
            }), a[e].apply(i, [].slice.call(arguments, 1))) : a.initialize.apply(this, arguments)
        }, t.fn.typeahead.noConflict = function() {
            return t.fn.typeahead = i, this
        }
    }()
}(window.jQuery), define("typeahead", function() {}), ! function(t, e) {
    "function" == typeof define && define.amd ? define("passwordInput", ["jquery"], t) : t(e.jQuery)
}(function(t, e) {
    function i(e, i) {
        this.element = t(e), this.wrapperElement = t(), this.toggleElement = t(), this.init(i)
    }
    var n = "plugin_hideShowPassword",
        a = ["show", "innerToggle"],
        r = 32,
        s = 13,
        o = function() {
            var t = document.body,
                e = document.createElement("input"),
                i = !0;
            t || (t = document.createElement("body")), e = t.appendChild(e);
            try {
                e.setAttribute("type", "text")
            } catch (n) {
                i = !1
            }
            return t.removeChild(e), i
        }(),
        l = {
            show: "infer",
            innerToggle: !1,
            enable: o,
            className: "hideShowPassword-field",
            initEvent: "hideShowPasswordInit",
            changeEvent: "passwordVisibilityChange",
            props: {
                autocapitalize: "off",
                autocomplete: "off",
                autocorrect: "off",
                spellcheck: "false"
            },
            toggle: {
                element: '<button type="button">',
                className: "hideShowPassword-toggle",
                touchSupport: "undefined" != typeof Modernizr && Modernizr.touch,
                attachToEvent: "click",
                attachToTouchEvent: "touchstart mousedown",
                attachToKeyEvent: "keyup",
                attachToKeyCodes: !0,
                styles: {
                    position: "absolute"
                },
                touchStyles: {
                    pointerEvents: "none"
                },
                position: "infer",
                verticalAlign: "middle",
                offset: 0,
                attr: {
                    role: "button",
                    "aria-label": "Show Password",
                    tabIndex: 0
                }
            },
            wrapper: {
                element: "<div>",
                className: "hideShowPassword-wrapper",
                enforceWidth: !0,
                styles: {
                    position: "relative"
                },
                inheritStyles: ["display", "verticalAlign", "marginTop", "marginRight", "marginBottom", "marginLeft"],
                innerElementStyles: {
                    marginTop: 0,
                    marginRight: 0,
                    marginBottom: 0,
                    marginLeft: 0
                }
            },
            states: {
                shown: {
                    className: "hideShowPassword-shown",
                    changeEvent: "passwordShown",
                    props: {
                        type: "text"
                    },
                    toggle: {
                        className: "hideShowPassword-toggle-hide",
                        content: "Hide",
                        attr: {
                            "aria-pressed": "true"
                        }
                    }
                },
                hidden: {
                    className: "hideShowPassword-hidden",
                    changeEvent: "passwordHidden",
                    props: {
                        type: "password"
                    },
                    toggle: {
                        className: "hideShowPassword-toggle-show",
                        content: "Show",
                        attr: {
                            "aria-pressed": "false"
                        }
                    }
                }
            }
        };
    i.prototype = {
        init: function(e) {
            this.update(e, l) && (this.element.addClass(this.options.className), this.options.innerToggle && (this.wrapElement(this.options.wrapper), this.initToggle(this.options.toggle), "string" == typeof this.options.innerToggle && (this.toggleElement.hide(), this.element.one(this.options.innerToggle, t.proxy(function() {
                this.toggleElement.show()
            }, this)))), this.element.trigger(this.options.initEvent, [this]))
        },
        update: function(t, e) {
            return this.options = this.prepareOptions(t, e), this.updateElement() && this.element.trigger(this.options.changeEvent, [this]).trigger(this.state().changeEvent, [this]), this.options.enable
        },
        toggle: function(t) {
            return t = t || "toggle", this.update({
                show: t
            })
        },
        prepareOptions: function(e, i) {
            var n, a = [];
            if (i = i || this.options, e = t.extend(!0, {}, i, e), e.enable && ("toggle" === e.show ? e.show = this.isType("hidden", e.states) : "infer" === e.show && (e.show = this.isType("shown", e.states)), "infer" === e.toggle.position && (e.toggle.position = "rtl" === this.element.css("text-direction") ? "left" : "right"), !t.isArray(e.toggle.attachToKeyCodes))) {
                if (e.toggle.attachToKeyCodes === !0) switch (n = t(e.toggle.element), n.prop("tagName").toLowerCase()) {
                    case "button":
                    case "input":
                        break;
                    case "a":
                        if (n.filter("[href]").length) {
                            a.push(r);
                            break
                        }
                    default:
                        a.push(r, s)
                }
                e.toggle.attachToKeyCodes = a
            }
            return e
        },
        updateElement: function() {
            return !(!this.options.enable || this.isType()) && (this.element.prop(t.extend({}, this.options.props, this.state().props)).addClass(this.state().className).removeClass(this.otherState().className), this.updateToggle(), !0)
        },
        isType: function(t, i) {
            return i = i || this.options.states, t = t || this.state(e, e, i).props.type, i[t] && (t = i[t].props.type), this.element.prop("type") === t
        },
        state: function(t, i, n) {
            return n = n || this.options.states, t === e && (t = this.options.show), "boolean" == typeof t && (t = t ? "shown" : "hidden"), i && (t = "shown" === t ? "hidden" : "shown"), n[t]
        },
        otherState: function(t) {
            return this.state(t, !0)
        },
        wrapElement: function(e) {
            var i, n = e.enforceWidth;
            return this.wrapperElement.length || (i = this.element.outerWidth(), t.each(e.inheritStyles, t.proxy(function(t, i) {
                e.styles[i] = this.element.css(i)
            }, this)), this.element.css(e.innerElementStyles).wrap(t(e.element).addClass(e.className).css(e.styles)), this.wrapperElement = this.element.parent(), n === !0 && (n = this.wrapperElement.outerWidth() !== i && i), n !== !1 && this.wrapperElement.css("width", n)), this.wrapperElement
        },
        initToggle: function(e) {
            return this.toggleElement.length || (this.toggleElement = t(e.element).attr(e.attr).addClass(e.className).css(e.styles).appendTo(this.wrapperElement), this.updateToggle(), this.positionToggle(e.position, e.verticalAlign, e.offset), e.touchSupport ? (this.toggleElement.css(e.touchStyles), this.element.on(e.attachToTouchEvent, t.proxy(this.toggleTouchEvent, this))) : this.toggleElement.on(e.attachToEvent, t.proxy(this.toggleEvent, this)), e.attachToKeyCodes.length && this.toggleElement.on(e.attachToKeyEvent, t.proxy(this.toggleKeyEvent, this))), this.toggleElement
        },
        positionToggle: function(t, e, i) {
            var n = {};
            switch (n[t] = i, e) {
                case "top":
                case "bottom":
                    n[e] = i;
                    break;
                case "middle":
                    n.top = "50%", n.marginTop = this.toggleElement.outerHeight() / -2
            }
            return this.toggleElement.css(n)
        },
        updateToggle: function(t, e) {
            var i, n;
            return this.toggleElement.length && (i = "padding-" + this.options.toggle.position, t = t || this.state().toggle, e = e || this.otherState().toggle, this.toggleElement.attr(t.attr).addClass(t.className).removeClass(e.className).html(t.content), n = this.toggleElement.outerWidth() + 2 * this.options.toggle.offset, this.element.css(i) !== n && this.element.css(i, n)), this.toggleElement
        },
        toggleEvent: function(t) {
            t.preventDefault(), this.toggle()
        },
        toggleKeyEvent: function(e) {
            t.each(this.options.toggle.attachToKeyCodes, t.proxy(function(t, i) {
                if (e.which === i) return this.toggleEvent(e), !1
            }, this))
        },
        toggleTouchEvent: function(t) {
            var e, i, n, a = this.toggleElement.offset().left;
            a && (e = t.pageX || t.originalEvent.pageX, "left" === this.options.toggle.position ? (a += this.toggleElement.outerWidth(), i = e, n = a) : (i = a, n = e), n >= i && this.toggleEvent(t))
        }
    }, t.fn.hideShowPassword = function() {
        var e = {};
        return t.each(arguments, function(i, n) {
            var r = {};
            if ("object" == typeof n) r = n;
            else {
                if (!a[i]) return !1;
                r[a[i]] = n
            }
            t.extend(!0, e, r)
        }), this.each(function() {
            var a = t(this),
                r = a.data(n);
            r ? r.update(e) : a.data(n, new i(this, e))
        })
    }, t.each({
        show: !0,
        hide: !1,
        toggle: "toggle"
    }, function(e, i) {
        t.fn[e + "Password"] = function(t, e) {
            return this.hideShowPassword(i, t, e)
        }
    })
}, this),
    function(t, e) {
        function i(e, i) {
            if (i || (i = document.baseURI || t("html > head > base").last().attr("href") || document.location.href), !e) return i;
            if (/^[a-z][-+\.a-z0-9]*:/i.test(e)) return e;
            if ("//" === e.slice(0, 2)) return /^[^:]+:/.exec(i)[0] + e;
            var n = e.charAt(0);
            if ("/" === n) return /^file:/i.test(i) ? "file://" + e : /^[^:]+:\/*[^\/]+/i.exec(i)[0] + e;
            if ("#" === n) return i.replace(/#.*$/, "") + e;
            if ("?" === n) return i.replace(/[\?#].*$/, "") + e;
            var a;
            if (/^file:/i.test(i)) a = i.replace(/^file:\/{0,2}/i, ""), i = "file://";
            else {
                var r = /^([^:]+:\/*[^\/]+)(\/.*?)?(\?.*?)?(#.*)?$/.exec(i);
                i = r[1], a = r[2] || "/"
            }
            return a = a.split("/"), a.pop(), 0 === a.length && a.push(""), a.push(e), i + a.join("/")
        }

        function n(t) {
            t = Number(t);
            var e = "",
                i = "";
            if (0 > t && (e = "-", t = -t), 1 / 0 === t) return e + "Infinity";
            if (t > 9999 && (t /= 1e3, i = "K"), t = Math.round(t), 0 === t) return "0";
            for (var n = []; t > 0;) {
                var a = t % 1e3 + "";
                if (t = Math.floor(t / 1e3))
                    for (; 3 > a.length;) a = "0" + a;
                n.unshift(a)
            }
            return e + n.join(",") + i
        }

        function a(e, i, n) {
            var a = n && n.title;
            if ("function" == typeof a && (a = a.call(this, e, i, n)), a) return a;
            var a = t('meta[name="DC.title"]').attr("content"),
                r = t('meta[name="DC.creator"]').attr("content");
            return a && r ? a + " - " + r : a || t('meta[property="og:title"]').attr("content") || t("title").text()
        }

        function r(e, i, n) {
            var a = n && n.description;
            return "function" == typeof a && (a = a.call(this, e, i, n)), a ? a : o(t('meta[name="twitter:description"]').attr("content") || t('meta[itemprop="description"]').attr("content") || t('meta[name="description"]').attr("content") || t.trim(t("article, p").first().text()) || t.trim(t("body").text()), 3500)
        }

        function s(e, n, a) {
            var r, s = a && a.image;
            return "function" == typeof s && (s = s.call(this, e, n, a)), s || (r = t('meta[property="image"], meta[property="og:image"], meta[property="og:image:url"], meta[name="twitter:image"], link[rel="image_src"], itemscope *[itemprop="image"]').first(), r.length > 0 && (s = r.attr(x[r[0].nodeName]))), s ? i(s) : (r = t("img").filter(":visible").filter(function() {
                return 0 === t(this).parents(".social_share_privacy_area").length
            }), 0 === r.length ? (s = t('link[rel~="shortcut"][rel~="icon"]').attr("href"), s ? i(s) : "http://www.google.com/s2/favicons?" + t.param({
                domain: location.hostname
            })) : (r.sort(function(t, e) {
                return e.offsetWidth * e.offsetHeight - t.offsetWidth * t.offsetHeight
            }), r[0].src))
        }

        function o(t, e) {
            if (e >= unescape(encodeURIComponent(t)).length) return t;
            var i = t.slice(0, e - 3);
            if (!/\W/.test(t.charAt(e - 3))) {
                var n = /^(.*)\s\S*$/.exec(i);
                n && (i = n[1])
            }
            return i + "…"
        }

        function l(t) {
            return t.replace(/[<>&"']/g, function(t) {
                return k[t]
            })
        }

        function u(e, i, n) {
            var a = n && n.embed;
            if ("function" == typeof a && (a = a.call(this, e, i, n)), a) return a;
            a = ['<iframe scrolling="no" frameborder="0" style="border:none;" allowtransparency="true"'];
            var r = t('meta[name="twitter:player"]').attr("content");
            if (r) {
                var s = t('meta[name="twitter:player:width"]').attr("content"),
                    o = t('meta[name="twitter:player:height"]').attr("content");
                s && a.push(' width="', l(s), '"'), o && a.push(' height="', l(o), '"')
            } else r = i + e.referrer_track;
            return a.push(' src="', l(r), '"></iframe>'), a.join("")
        }

        function c(e) {
            var n = document.location.href,
                a = t("link[rel=canonical]").attr("href") || t('head meta[property="og:url"]').attr("content");
            return a ? n = i(a) : e && e.ignore_fragment && (n = n.replace(/#.*$/, "")), n
        }

        function d(e) {
            function i(n) {
                var a = t(this).parents("li.help_info").first(),
                    r = a.parents(".social_share_privacy_area").first().parent(),
                    s = r.data("social-share-privacy-options"),
                    o = s.services[e],
                    l = o.button_class || e,
                    u = s.uri;
                "function" == typeof u && (u = u.call(r[0], s));
                var c = a.find("span.switch");
                c.hasClass("off") ? (a.addClass("info_off"), c.addClass("on").removeClass("off").html(o.txt_on || " "), a.find("img.privacy_dummy").replaceWith("function" == typeof o.button ? o.button.call(a.parent().parent()[0], o, u, s) : o.button), r.trigger({
                    type: "socialshareprivacy:enable",
                    serviceName: e,
                    isClick: !n.isTrigger
                })) : (a.removeClass("info_off"), c.addClass("off").removeClass("on").html(o.txt_off || " "), a.find(".dummy_btn").empty().append(t("<img/>").addClass(l + "_privacy_dummy privacy_dummy").attr({
                    alt: o.dummy_alt,
                    src: o.path_prefix + ("line" === s.layout ? o.dummy_line_img : o.dummy_box_img)
                }).click(i)), r.trigger({
                    type: "socialshareprivacy:disable",
                    serviceName: e,
                    isClick: !n.isTrigger
                }))
            }
            return i
        }

        function p() {
            var e = t(this);
            if (!e.hasClass("info_off")) {
                var i = window.setTimeout(function() {
                    e.addClass("display"), e.removeData("timeout_id")
                }, 500);
                e.data("timeout_id", i)
            }
        }

        function f() {
            var i = t(this),
                n = i.data("timeout_id");
            n !== e && window.clearTimeout(n), i.removeClass("display")
        }

        function h() {
            var e = t(this),
                i = e.parents(".social_share_privacy_area").first().parent(),
                n = i.data("social-share-privacy-options");
            e.is(":checked") ? (n.set_perma_option(e.attr("data-service"), n), e.parent().addClass("checked")) : (n.del_perma_option(e.attr("data-service"), n), e.parent().removeClass("checked"))
        }

        function m() {
            var e = t(this),
                i = window.setTimeout(function() {
                    e.find(".settings_info_menu").removeClass("off").addClass("on"), e.removeData("timeout_id")
                }, 500);
            e.data("timeout_id", i)
        }

        function g() {
            var i = t(this),
                n = i.data("timeout_id");
            n !== e && window.clearTimeout(n), i.find(".settings_info_menu").removeClass("on").addClass("off")
        }

        function v(e, i) {
            t.cookie("socialSharePrivacy_" + e, "perma_on", i.cookie_expires, i.cookie_path, i.cookie_domain)
        }

        function _(e, i) {
            t.cookie("socialSharePrivacy_" + e, null, -1, i.cookie_path, i.cookie_domain)
        }

        function b(t, e) {
            return !!e.get_perma_options(e)[t]
        }

        function y() {
            var e = t.cookie(),
                i = {};
            for (var n in e) {
                var a = /^socialSharePrivacy_(.+)$/.exec(n);
                a && (i[a[1]] = "perma_on" === e[n])
            }
            return i
        }

        function w(e) {
            if ("string" == typeof e) {
                var i = e;
                if (1 === arguments.length) switch (i) {
                    case "enable":
                        this.find(".switch.off").click();
                        break;
                    case "disable":
                        this.find(".switch.on").click();
                        break;
                    case "toggle":
                        this.find(".switch").click();
                        break;
                    case "options":
                        return this.data("social-share-privacy-options");
                    case "destroy":
                        this.trigger({
                            type: "socialshareprivacy:destroy"
                        }), this.children(".social_share_privacy_area").remove(), this.removeData("social-share-privacy-options");
                        break;
                    case "enabled":
                        var n = {};
                        return this.each(function() {
                            var e = t(this),
                                i = e.data("social-share-privacy-options");
                            for (var a in i.services) n[a] = e.find("." + (i.services[a].class_name || a) + " .switch").hasClass("on")
                        }), n;
                    case "disabled":
                        var a = {};
                        return this.each(function() {
                            var e = t(this),
                                i = e.data("social-share-privacy-options");
                            for (var n in i.services) a[n] = e.find("." + (i.services[n].class_name || n) + " .switch").hasClass("off")
                        }), a;
                    default:
                        throw Error("socialSharePrivacy: unknown command: " + i)
                } else {
                    var r = arguments[1];
                    switch (i) {
                        case "enable":
                            this.each(function() {
                                var e = t(this),
                                    i = e.data("social-share-privacy-options");
                                e.find("." + (i.services[r].class_name || r) + " .switch.off").click()
                            });
                            break;
                        case "disable":
                            this.each(function() {
                                var e = t(this),
                                    i = e.data("social-share-privacy-options");
                                e.find("." + (i.services[r].class_name || r) + " .switch.on").click()
                            });
                            break;
                        case "toggle":
                            this.each(function() {
                                var e = t(this),
                                    i = e.data("social-share-privacy-options");
                                e.find("." + (i.services[r].class_name || r) + " .switch").click()
                            });
                            break;
                        case "option":
                            if (!(arguments.length > 2)) return this.data("social-share-privacy-options")[r];
                            var s = {};
                            s[r] = arguments[2], this.each(function() {
                                t.extend(!0, t(this).data("social-share-privacy-options"), s)
                            });
                            break;
                        case "options":
                            t.extend(!0, e, r);
                            break;
                        case "enabled":
                            var e = this.data("social-share-privacy-options");
                            return this.find("." + (e.services[r].class_name || r) + " .switch").hasClass("on");
                        case "disabled":
                            var e = this.data("social-share-privacy-options");
                            return this.find("." + (e.services[r].class_name || r) + " .switch").hasClass("off");
                        default:
                            throw Error("socialSharePrivacy: unknown command: " + i)
                    }
                }
                return this
            }
            return this.each(function() {
                var i = {};
                this.lang && (i.language = this.lang);
                for (var n = 0, a = this.attributes; a.length > n; ++n) {
                    var r = a[n];
                    if (/^data-./.test(r.name)) {
                        for (var s = r.name.slice(5).replace(/-/g, "_").split("."), o = i, l = 0; s.length - 1 > l; ++l) {
                            var u = s[l];
                            u in o ? (o = o[u], "string" == typeof o && (o = Function("$", "return (" + o + ");").call(this, t))) : o = o[u] = {}
                        }
                        var u = s[l];
                        o[u] = "object" == typeof o[u] ? t.extend(!0, Function("$", "return (" + r.value + ");").call(this, t), o[u]) : r.value
                    }
                }
                if ("cookie_expires" in i && (i.cookie_expires = Number(i.cookie_expires)), "perma_option" in i && (i.perma_option = "true" === t.trim(i.perma_option).toLowerCase()), "ignore_fragment" in i && (i.ignore_fragment = "true" === t.trim(i.ignore_fragment).toLowerCase()), "set_perma_option" in i && (i.set_perma_option = Function("service_name", "options", i.set_perma_option)), "del_perma_option" in i && (i.del_perma_option = Function("service_name", "options", i.del_perma_option)), "get_perma_option" in i && (i.get_perma_option = Function("service_name", "options", i.get_perma_option)), "get_perma_options" in i && (i.get_perma_options = Function("options", i.get_perma_options)), "order" in i && (i.order = t.trim(i.order), i.order ? i.order = i.order.split(/\s+/g) : delete i.order), "string" == typeof i.services && (i.services = Function("$", "return (" + i.services + ");").call(this, t)), "options" in i && (i = t.extend(i, Function("$", "return (" + i.options + ");").call(this, t)), delete i.options), "services" in i)
                    for (var c in i.services) {
                        var v = i.services[c];
                        "string" == typeof v && (i.services[c] = Function("$", "return (" + v + ");").call(this, t)), "string" == typeof v.status && (v.status = "true" === t.trim(v.status).toLowerCase()), "string" == typeof v.perma_option && (v.perma_option = "true" === t.trim(v.perma_option).toLowerCase())
                    }
                var _ = t.extend(!0, {}, w.settings, e, i),
                    b = _.order || [],
                    y = "line" === _.layout ? "dummy_line_img" : "dummy_box_img",
                    x = !1,
                    k = !1,
                    C = !1,
                    T = [];
                for (var c in _.services) {
                    var v = _.services[c];
                    v.status && (x = !0, -1 === t.inArray(c, b) && T.push(c), "safe" !== v.privacy && (C = !0, v.perma_option && (k = !0))), "language" in v || (v.language = _.language), "path_prefix" in v || (v.path_prefix = _.path_prefix), "referrer_track" in v || (v.referrer_track = "")
                }
                if (T.sort(), b = b.concat(T), x) {
                    if (_.css_path) {
                        var S = (_.path_prefix || "") + _.css_path;
                        document.createStyleSheet ? document.createStyleSheet(S) : 0 === t('link[href="' + S + '"]').length && t("<link/>", {
                            rel: "stylesheet",
                            type: "text/css",
                            href: S
                        }).appendTo(document.head)
                    }
                    var E;
                    if (_.perma_option && k)
                        if (_.get_perma_options) E = _.get_perma_options(_);
                        else {
                            E = {};
                            for (var c in _.services) E[c] = _.get_perma_option(c, _)
                        }
                    var A = _.uri;
                    "function" == typeof A && (A = A.call(this, _));
                    var P = t('<ul class="social_share_privacy_area"></ul>').addClass(_.layout),
                        O = t(this);
                    O.prepend(P).data("social-share-privacy-options", _);
                    for (var n = 0; b.length > n; ++n) {
                        var c = b[n],
                            v = _.services[c];
                        if (v && v.status) {
                            var N, D = v.class_name || c,
                                I = v.button_class || c;
                            "safe" === v.privacy ? (N = t('<li class="help_info"><!--<div class="info">' + v.txt_info + '</div>--><div class="dummy_btn"></div></li>').addClass(D), N.find(".dummy_btn").addClass(I).append(v.button.call(this, v, A, _))) : (N = t('<li class="help_info"><!--<div class="info">' + v.txt_info + '</div>--><span class="switch off">' + (v.txt_off || " ") + '</span><div class="dummy_btn"></div></li>').addClass(D), N.find(".dummy_btn").addClass(I).append(t("<img/>").addClass(I + "_privacy_dummy privacy_dummy").attr({
                                alt: v.dummy_alt,
                                src: v.path_prefix + v[y]
                            })), N.find(".dummy_btn img.privacy_dummy, span.switch").click(d(c))), P.append(N)
                        }
                    }
                    if (C && (P.find(".help_info").on("mouseenter", p).on("mouseleave", f), _.perma_option && k)) {
                        var j = P.find("li.settings_info"),
                            L = j.find(".settings_info_menu");
                        L.removeClass("perma_option_off"), L.append('<span class="settings">' + _.txt_settings + "</span><form><fieldset><legend>" + _.settings_perma + "</legend></fieldset></form>");
                        for (var H = L.find("form fieldset"), n = 0; b.length > n; ++n) {
                            var c = b[n],
                                v = _.services[c];
                            if (v && v.status && v.perma_option && "safe" !== v.privacy) {
                                var D = v.class_name || c,
                                    F = E[c],
                                    M = t('<label><input type="checkbox"' + (F ? ' checked="checked"/>' : "/>") + v.display_name + "</label>");
                                M.find("input").attr("data-service", c), H.append(M), F && (P.find("li." + D + " span.switch").click(), _.set_perma_option(c, _))
                            }
                        }
                        j.find("span.settings").css("cursor", "pointer"), j.on("mouseenter", m).on("mouseleave", g), j.find("fieldset input").on("change", h)
                    }
                    O.trigger({
                        type: "socialshareprivacy:create",
                        options: _
                    })
                }
            })
        }
        var x = {
                META: "content",
                IMG: "src",
                A: "href",
                IFRAME: "src",
                LINK: "href"
            },
            k = {
                "<": "&lt;",
                ">": "&gt;",
                "&": "&amp;",
                '"': "&quot;",
                "'": "&#39;"
            };
        w.absurl = i, w.escapeHtml = l, w.getTitle = a, w.getImage = s, w.getEmbed = u, w.getDescription = r, w.abbreviateText = o, w.formatNumber = n, w.settings = {
            services: {},
            info_link: "http://panzi.github.io/SocialSharePrivacy/",
            info_link_target: "",
            txt_settings: "Settings",
            txt_help: "If you activate these fields via click, data will be sent to a third party (Facebook, Twitter, Google, ...) and stored there. For more details click <em>i</em>.",
            settings_perma: "Permanently enable share buttons:",
            layout: "line",
            set_perma_option: v,
            del_perma_option: _,
            get_perma_options: y,
            get_perma_option: b,
            perma_option: !!t.cookie,
            cookie_path: "/",
            cookie_domain: document.location.hostname,
            cookie_expires: 365,
            path_prefix: "",
            css_path: "stylesheets/jquery.socialshareprivacy.min.css",
            uri: c,
            language: "en",
            ignore_fragment: !0
        }, t.fn.socialSharePrivacy = w
    }(jQuery),
    function(t) {
        var e = {
            af: ["ZA"],
            ar: ["AR"],
            az: ["AZ"],
            be: ["BY"],
            bg: ["BG"],
            bn: ["IN"],
            bs: ["BA"],
            ca: ["ES"],
            cs: ["CZ"],
            cy: ["GB"],
            da: ["DK"],
            de: ["DE"],
            el: ["GR"],
            en: ["GB", "PI", "UD", "US"],
            eo: ["EO"],
            es: ["ES", "LA"],
            et: ["EE"],
            eu: ["ES"],
            fa: ["IR"],
            fb: ["LT"],
            fi: ["FI"],
            fo: ["FO"],
            fr: ["CA", "FR"],
            fy: ["NL"],
            ga: ["IE"],
            gl: ["ES"],
            he: ["IL"],
            hi: ["IN"],
            hr: ["HR"],
            hu: ["HU"],
            hy: ["AM"],
            id: ["ID"],
            is: ["IS"],
            it: ["IT"],
            ja: ["JP"],
            ka: ["GE"],
            km: ["KH"],
            ko: ["KR"],
            ku: ["TR"],
            la: ["VA"],
            lt: ["LT"],
            lv: ["LV"],
            mk: ["MK"],
            ml: ["IN"],
            ms: ["MY"],
            nb: ["NO"],
            ne: ["NP"],
            nl: ["NL"],
            nn: ["NO"],
            pa: ["IN"],
            pl: ["PL"],
            ps: ["AF"],
            pt: ["BR", "PT"],
            ro: ["RO"],
            ru: ["RU"],
            sk: ["SK"],
            sl: ["SI"],
            sq: ["AL"],
            sr: ["RS"],
            sv: ["SE"],
            sw: ["KE"],
            ta: ["IN"],
            te: ["IN"],
            th: ["TH"],
            tl: ["PH"],
            tr: ["TR"],
            uk: ["UA"],
            vi: ["VN"],
            zh: ["CN", "HK", "TW"]
        };
        t.fn.socialSharePrivacy.settings.services.facebook = {
            status: !0,
            button_class: "fb_like",
            dummy_line_img: "images/dummy_facebook.png",
            dummy_box_img: "images/dummy_box_facebook.png",
            dummy_alt: 'Facebook "Like"-Dummy',
            txt_info: "Two clicks for more privacy: The Facebook Like button will be enabled once you click here. Activating the button already sends data to Facebook &ndash; see <em>i</em>.",
            txt_off: "not connected to Facebook",
            txt_on: "connected to Facebook",
            perma_option: !0,
            display_name: "Facebook Like/Recommend",
            referrer_track: "",
            action: "like",
            colorscheme: "light",
            font: "",
            button: function(i, n, a) {
                var r = /^([a-z]{2})_([A-Z]{2})$/.exec(i.language),
                    s = "en_US";
                if (r) {
                    if (r[1] in e) {
                        var o = e[r[1]];
                        s = -1 !== t.inArray(r[2], o) ? i.language : r[1] + "_" + o[0]
                    }
                } else i.language in e && (s = i.language + "_" + e[i.language][0]);
                var l = {
                    locale: s,
                    href: n + i.referrer_track,
                    send: "false",
                    show_faces: "false",
                    action: i.action,
                    colorscheme: i.colorscheme
                };
                return i.font && (l.font = i.font), "line" === a.layout ? (l.width = "120", l.height = "20", l.layout = "button_count") : (l.width = 62, l.height = 61, l.layout = "box_count"), t('<iframe scrolling="no" frameborder="0" allowtransparency="true"></iframe>').attr("src", "https://www.facebook.com/plugins/like.php?" + t.param(l))
            }
        }
    }(jQuery), jQuery(document).ready(function(t) {
    t('script[type="application/x-social-share-privacy-settings"]').each(function() {
        var e = Function("return (" + (this.textContent || this.innerText || this.text) + ");").call(this);
        "object" == typeof e && t.extend(!0, t.fn.socialSharePrivacy.settings, e)
    })
}), define("socialshare", function() {}),
    function(t) {
        function e(t, e, i) {
            return t.addEventListener ? t.addEventListener(e, i, !1) : t.attachEvent ? t.attachEvent("on" + e, i) : void 0
        }

        function i(t, e) {
            var i, n;
            for (i = 0, n = t.length; i < n; i++)
                if (t[i] === e) return !0;
            return !1
        }

        function n(t, e) {
            var i;
            t.createTextRange ? (i = t.createTextRange(), i.move("character", e), i.select()) : t.selectionStart && (t.focus(), t.setSelectionRange(e, e))
        }

        function a(t, e) {
            try {
                return t.type = e, !0
            } catch (i) {
                return !1
            }
        }
        t.Placeholders = {
            Utils: {
                addEventListener: e,
                inArray: i,
                moveCaret: n,
                changeType: a
            }
        }
    }(this),
    function(t) {
        function e() {}

        function i() {
            try {
                return document.activeElement
            } catch (t) {}
        }

        function n(t, e) {
            var i, n, a = !!e && t.value !== e,
                r = t.value === t.getAttribute(I);
            return !(!a && !r || "true" !== t.getAttribute(j)) && (t.removeAttribute(j), t.value = t.value.replace(t.getAttribute(I), ""), t.className = t.className.replace(D, ""), n = t.getAttribute(q), parseInt(n, 10) >= 0 && (t.setAttribute("maxLength", n), t.removeAttribute(q)), i = t.getAttribute(L), i && (t.type = i), !0)
        }

        function a(t) {
            var e, i, n = t.getAttribute(I);
            return !("" !== t.value || !n) && (t.setAttribute(j, "true"), t.value = n, t.className += " " + N, i = t.getAttribute(q), i || (t.setAttribute(q, t.maxLength), t.removeAttribute("maxLength")), e = t.getAttribute(L), e ? t.type = "text" : "password" === t.type && W.changeType(t, "text") && t.setAttribute(L, "password"), !0)
        }

        function r(t, e) {
            var i, n, a, r, s, o, l;
            if (t && t.getAttribute(I)) e(t);
            else
                for (a = t ? t.getElementsByTagName("input") : m, r = t ? t.getElementsByTagName("textarea") : g, i = a ? a.length : 0, n = r ? r.length : 0, l = 0, o = i + n; l < o; l++) s = l < i ? a[l] : r[l - i], e(s)
        }

        function s(t) {
            r(t, n)
        }

        function o(t) {
            r(t, a)
        }

        function l(t) {
            return function() {
                v && t.value === t.getAttribute(I) && "true" === t.getAttribute(j) ? W.moveCaret(t, 0) : n(t)
            }
        }

        function u(t) {
            return function() {
                a(t)
            }
        }

        function c(t) {
            return function(e) {
                if (b = t.value, "true" === t.getAttribute(j) && b === t.getAttribute(I) && W.inArray(P, e.keyCode)) return e.preventDefault && e.preventDefault(), !1
            }
        }

        function d(t) {
            return function() {
                n(t, b), "" === t.value && (t.blur(), W.moveCaret(t, 0))
            }
        }

        function p(t) {
            return function() {
                t === i() && t.value === t.getAttribute(I) && "true" === t.getAttribute(j) && W.moveCaret(t, 0)
            }
        }

        function f(t) {
            return function() {
                s(t)
            }
        }

        function h(t) {
            t.form && (C = t.form, "string" == typeof C && (C = document.getElementById(C)), C.getAttribute(H) || (W.addEventListener(C, "submit", f(C)), C.setAttribute(H, "true"))), W.addEventListener(t, "focus", l(t)), W.addEventListener(t, "blur", u(t)), v && (W.addEventListener(t, "keydown", c(t)), W.addEventListener(t, "keyup", d(t)), W.addEventListener(t, "click", p(t))), t.setAttribute(F, "true"), t.setAttribute(I, x), (v || t !== i()) && a(t)
        }
        var m, g, v, _, b, y, w, x, k, C, T, S, E, A = ["text", "search", "url", "tel", "email", "password", "number", "textarea"],
            P = [27, 33, 34, 35, 36, 37, 38, 39, 40, 8, 46],
            O = "#ccc",
            N = "placeholdersjs",
            D = new RegExp("(?:^|\\s)" + N + "(?!\\S)"),
            I = "data-placeholder-value",
            j = "data-placeholder-active",
            L = "data-placeholder-type",
            H = "data-placeholder-submit",
            F = "data-placeholder-bound",
            M = "data-placeholder-focus",
            z = "data-placeholder-live",
            q = "data-placeholder-maxlength",
            $ = document.createElement("input"),
            R = document.getElementsByTagName("head")[0],
            B = document.documentElement,
            U = t.Placeholders,
            W = U.Utils;
        if (U.nativeSupport = void 0 !== $.placeholder, !U.nativeSupport) {
            for (m = document.getElementsByTagName("input"), g = document.getElementsByTagName("textarea"), v = "false" === B.getAttribute(M), _ = "false" !== B.getAttribute(z), y = document.createElement("style"), y.type = "text/css", w = document.createTextNode("." + N + " { color:" + O + "; }"), y.styleSheet ? y.styleSheet.cssText = w.nodeValue : y.appendChild(w), R.insertBefore(y, R.firstChild), E = 0, S = m.length + g.length; E < S; E++) T = E < m.length ? m[E] : g[E - m.length], x = T.attributes.placeholder, x && (x = x.nodeValue, x && W.inArray(A, T.type) && h(T));
            k = setInterval(function() {
                for (E = 0, S = m.length + g.length; E < S; E++) T = E < m.length ? m[E] : g[E - m.length], x = T.attributes.placeholder, x ? (x = x.nodeValue, x && W.inArray(A, T.type) && (T.getAttribute(F) || h(T), (x !== T.getAttribute(I) || "password" === T.type && !T.getAttribute(L)) && ("password" === T.type && !T.getAttribute(L) && W.changeType(T, "text") && T.setAttribute(L, "password"), T.value === T.getAttribute(I) && (T.value = x), T.setAttribute(I, x)))) : T.getAttribute(j) && (n(T), T.removeAttribute(I));
                _ || clearInterval(k)
            }, 100)
        }
        W.addEventListener(t, "beforeunload", function() {
            U.disable()
        }), U.disable = U.nativeSupport ? e : s, U.enable = U.nativeSupport ? e : o
    }(this),
    function(t) {
        var e = t.fn.val,
            i = t.fn.prop;
        Placeholders.nativeSupport || (t.fn.val = function(t) {
            var i = e.apply(this, arguments),
                n = this.eq(0).data("placeholder-value");
            return void 0 === t && this.eq(0).data("placeholder-active") && i === n ? "" : i
        }, t.fn.prop = function(t, e) {
            return void 0 === e && this.eq(0).data("placeholder-active") && "value" === t ? "" : i.apply(this, arguments)
        })
    }(jQuery), define("placeholder", function() {}),
    function(t) {
        var e = null;
        t.modal = function(i, n) {
            t.modal.close();
            var a, r;
            if (this.$body = t("body"), this.options = t.extend({}, t.modal.defaults, n), i.is("a"))
                if (r = i.attr("href"), /^#/.test(r)) {
                    if (this.$elm = t(r), 1 !== this.$elm.length) return null;
                    this.open()
                } else this.$elm = t("<div>"), this.$body.append(this.$elm), a = function(t, e) {
                    e.elm.remove()
                }, this.showSpinner(), i.trigger(t.modal.AJAX_SEND), t.get(r).done(function(n) {
                    e && (i.trigger(t.modal.AJAX_SUCCESS), e.$elm.empty().append(n).on(t.modal.CLOSE, a), e.hideSpinner(), e.open(), i.trigger(t.modal.AJAX_COMPLETE))
                }).fail(function() {
                    i.trigger(t.modal.AJAX_FAIL), e.hideSpinner(), i.trigger(t.modal.AJAX_COMPLETE)
                });
            else this.$elm = i, this.open()
        }, t.modal.prototype = {
            constructor: t.modal,
            open: function() {
                this.block(), this.show(), this.options.escapeClose && t(document).on("keydown.modal", function(e) {
                    27 == e.which && t.modal.close()
                }), this.options.clickClose && this.blocker.click(t.modal.close)
            },
            close: function() {
                this.unblock(), this.hide(), t(document).off("keydown.modal")
            },
            block: function() {
                this.$elm.trigger(t.modal.BEFORE_BLOCK, [this._ctx()]), this.blocker = t('<div class="jquery-modal blocker"></div>').css({
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0,
                    width: "100%",
                    height: "100%",
                    position: "fixed",
                    zIndex: this.options.zIndex,
                    background: this.options.overlay,
                    opacity: this.options.opacity
                }), this.$body.append(this.blocker), this.$elm.trigger(t.modal.BLOCK, [this._ctx()]);
            },
            unblock: function() {
                this.blocker.remove()
            },
            show: function() {
                this.$elm.trigger(t.modal.BEFORE_OPEN, [this._ctx()]), this.options.showClose && (this.closeButton = t('<a href="#close-modal" rel="modal:close" class="close-modal">' + this.options.closeText + "</a>"), this.$elm.append(this.closeButton)), this.$elm.addClass(this.options.modalClass + " current"), this.center(), this.$elm.show().trigger(t.modal.OPEN, [this._ctx()])
            },
            hide: function() {
                this.$elm.trigger(t.modal.BEFORE_CLOSE, [this._ctx()]), this.closeButton && this.closeButton.remove(), this.$elm.removeClass("current").hide(), this.$elm.trigger(t.modal.CLOSE, [this._ctx()])
            },
            showSpinner: function() {
                this.options.showSpinner && (this.spinner = this.spinner || t('<div class="' + this.options.modalClass + '-spinner"></div>').append(this.options.spinnerHtml), this.$body.append(this.spinner), this.spinner.show())
            },
            hideSpinner: function() {
                this.spinner && this.spinner.remove()
            },
            center: function() {
                this.$elm.css({
                    position: "fixed",
                    top: "50%",
                    left: "50%",
                    marginTop: -(this.$elm.outerHeight() / 2),
                    marginLeft: -(this.$elm.outerWidth() / 2),
                    zIndex: this.options.zIndex + 1
                })
            },
            _ctx: function() {
                return {
                    elm: this.$elm,
                    blocker: this.blocker,
                    options: this.options
                }
            }
        }, t.modal.prototype.resize = t.modal.prototype.center, t.modal.close = function(t) {
            e && (t && t.preventDefault(), e.close(), e = null)
        }, t.modal.resize = function() {
            e && e.resize()
        }, t.modal.defaults = {
            overlay: "#000",
            opacity: .75,
            zIndex: 1,
            escapeClose: !0,
            clickClose: !0,
            closeText: "Close",
            modalClass: "modal",
            spinnerHtml: null,
            showSpinner: !0,
            showClose: !0
        }, t.modal.BEFORE_BLOCK = "modal:before-block", t.modal.BLOCK = "modal:block", t.modal.BEFORE_OPEN = "modal:before-open", t.modal.OPEN = "modal:open", t.modal.BEFORE_CLOSE = "modal:before-close", t.modal.CLOSE = "modal:close", t.modal.AJAX_SEND = "modal:ajax:send", t.modal.AJAX_SUCCESS = "modal:ajax:success", t.modal.AJAX_FAIL = "modal:ajax:fail", t.modal.AJAX_COMPLETE = "modal:ajax:complete", t.fn.modal = function(i) {
            return 1 === this.length && (e = new t.modal(this, i)), this
        }, t(document).on("click", 'a[rel="modal:close"]', t.modal.close), t(document).on("click", 'a[rel="modal:open"]', function(e) {
            e.preventDefault(), t(this).modal()
        })
    }(jQuery), define("modal", function() {}),
    function() {
        define("avtoclassika", ["jquery", "pubsub", "sparky", "Deals", "Ads", "Slider", "Car", "Basket", "News", "Users", "Comments", "UwinGoogleMap", "uwinTabs", "json2", "cookie", "hashchange", "mixitup", "bezier", "numeral", "typeahead", "passwordInput", "socialshare", "placeholder", "powertip", "modal"], function(t, e, i, n, a, r, s, o, l, u, c, d) {
            var p;
            p = t("#currencies .currencies__link").attr("data-ratio-id"), t("#add-ads__currency").val(p);
            var f;
            return f = f || function(t, p, f) {
                var h, m, g, v, _, b;
                return g = {}, h = {}, m = {}, _ = "." + t('META[property="uwin:serverName"]').attr("content"), b = {
                    basket: {
                        sum: 0,
                        sumUsd: 0,
                        count: 0,
                        products: {},
                        promocode: 0
                    },
                    meta: {},
                    debug: !1
                }, v = {}, g = {
                    index: function() {
                        var e;
                        return r.initSlider("#important-panel__slider"), n.initSlider("#hot-products__slider"), a.initBarSlider("#message-bar__slider"), a.initAddForm(), "_=_" === location.hash.substr(1) && (location.hash = ""), e = location.hash.substr(1).split("=")[0] || "all", t("#catalog-autoparts__list-wrap").mixitup({
                            transitionSpeed: 500,
                            showOnLoad: e
                        }), t(window).bind("hashchange", function(e) {
                            return "" === location.hash ? t("#catalog-autoparts__list-wrap").mixitup("filter", "all") : t("#catalog-autoparts__list-wrap").mixitup("filter", location.hash.substr(1).split("=")[0])
                        })
                    },
                    basket: function() {
                        return o.initSteps()
                    },
                    car: function() {
                        return s.init(), s.initSlider("#cars-slider__slider"), s.initGallery(".detail-info__thumbnail-link, A.detail-info__image-link"), s.initSchemaSwipebox(".detail-info__schema-link"), s._initSchema(t(".autoparts__schema")), s.positionMiniSchema(".detail-info__schema"), n.initSlider("#hot-products__slider"), o.quickBuyForm()
                    },
                    news: function() {
                        return l.init()
                    },
                    ads: function() {
                        return a.init(), a.initGallery(".ad__thumbnail-link, .ad__image"), a.initSlider("#other-adverts__slider")
                    },
                    oAuth: function() {
                        return window.close()
                    }
                }, h = {
                    init: function() {
                        return i.init(b), i.bindEvents(), i.route(g), numeral.language("ru", {
                            delimiters: {
                                thousands: " ",
                                decimal: ","
                            }
                        }), numeral.language("ru"), t(".form__input-password").hideShowPassword({
                            innerToggle: !0,
                            states: {
                                shown: {
                                    toggle: {
                                        content: t(".form__input-password").data("hide-txt")
                                    }
                                },
                                hidden: {
                                    toggle: {
                                        content: t(".form__input-password").data("show-txt")
                                    }
                                }
                            }
                        }), t.modal.defaults = {
                            overlay: "#fff",
                            opacity: .75,
                            zIndex: 1e4,
                            escapeClose: !0,
                            clickClose: !0,
                            closeText: "",
                            closeClass: "",
                            showClose: !1,
                            modalClass: "modal",
                            fadeDelay: 0,
                            fadeDuration: 100
                        }, t(document).on(t.modal.BEFORE_OPEN, function(e, i) {
                            if (0 !== t("#fileupload-single").length) return t("#fileupload-single").fileupload({
                                dataType: "json",
                                send: function(e, i) {
                                    var n, a;
                                    if (i.files[0].size > 1048576) return n = t(".form__fileinput-error"), n.text(t(e.target).attr("data-error-filesize")), n.css("opacity", "1"), a = setInterval(function() {
                                        return n.css("opacity", "0"), clearInterval(a)
                                    }, 5e3), !1
                                },
                                progressall: function(e, i) {
                                    var n;
                                    if (n = parseInt(i.loaded / i.total * 100, 10), t(".form__submit").attr("disabled", "disabled"), t("#upload-progress").css("width", n + "%"), 100 === n) return t(".form__submit").removeAttr("disabled")
                                },
                                error: function() {
                                    return t("#upload-progress").css("width", 0)
                                },
                                done: function(e, i) {
                                    var n;
                                    return n = i.files[0].name, t(".form__fileinput-files").html(n), t("#upload-progress").css("width", 0), t("#add-request #uploaded-file").val(i.result.file)
                                }
                            })
                        }), t(".share-fb").socialSharePrivacy(), t("#vk_like").length > 0 && (VK.init({
                            apiId: 4241621,
                            onlyWidgets: !0
                        }), VK.Widgets.Like("vk_like", {
                            type: "button",
                            height: 20
                        })), e.subscribe("REBIND", function() {
                            return i.bindEvents()
                        }), e.subscribe("CHANGE_BASKET", function(t, e) {
                            return h.logic.changeBasket(e)
                        }), e.subscribe("DRAW_BASKET", function(t) {
                            return h.logic._drawBasket()
                        })
                    },
                    logic: {
                        _drawBasket: function() {
                            var e, i, n, a, r, s, o, l, u;
                            return s = b.basket, e = t(".basket-bar__notifier"), e.addClass("pulse"), e.text(s.count), ~~s.count > 0 ? t(".basket-bar").removeClass("basket-bar_state_disabled") : t(".basket-bar").addClass("basket-bar_state_disabled"), a = t(".basket-bar__sum_type_int"), n = t(".basket-bar__sum_type_decimal"), l = s.sum - s.sum / 100 * (s.promocode || 0), u = s.sumUsd - s.sumUsd / 100 * (s.promocode || 0), o = numeral(l).format("0,0.0").split(","), a.text(o[0]), n.text(o[1]), t(".basket-bar__sum").attr("data-cost", l), t(".basket-bar__sum").attr("data-usd-cost", u), o = numeral(l).format("0,0.00"), o = o.replace(",00", ""), i = t(".basket-products__total-cost"), i.attr("data-cost", l), i.attr("data-usd-cost", u), r = t("#currencies LI A").first().attr("data-short-name"), "P" === r && (r = '<span class="rur">' + r + "</span>"), "грн." === r ? i.html(numeral(l).format("0,0") + "&thinsp;<small>" + r + "</small>") : i.html(r + o), t(".basket-bar__currency").html(r), "грн." === r ? (t("HEADER .layout__header-info-bar .basket-bar__currency").detach().insertAfter("HEADER .layout__header-info-bar .basket-bar__sum_type_int"), t(".layout__header-info-bar_type_sticky .basket-bar__currency").detach().insertAfter(".layout__header-info-bar_type_sticky .basket-bar__sum_type_int"), t(".basket-bar__currency").each(function() {
                                return t(this).replaceWith('<small class="basket-bar__currency" style="font-size:65%;"> ' + t(this).text() + "</small>")
                            })) : (t("HEADER .layout__header-info-bar .basket-bar__currency").detach().insertBefore("HEADER .layout__header-info-bar .basket-bar__sum_type_int"), t(".layout__header-info-bar_type_sticky .basket-bar__currency").detach().insertBefore(".layout__header-info-bar_type_sticky .basket-bar__sum_type_int"), t(".basket-bar__currency").each(function() {
                                return t(this).replaceWith('<span class="basket-bar__currency">' + t(this).text() + "</span>")
                            })), l <= 0 ? t("#basket-submit").attr("disabled", "disabled") : t("#basket-submit").removeAttr("disabled")
                        },
                        changeBasket: function(e) {
                            var i, n;
                            return i = b.basket, e.type = e.type || 1, i.sum += parseFloat(e.cost) * ~~e.count * e.type, i.sumUsd += parseFloat(e.costUsd) * ~~e.count * e.type, i.count = ~~i.count + ~~e.count * e.type, n = e.id.toString().concat("-", e.size, "-", e.color), 1 === e.type ? i.products[n] ? i.products[n].count = ~~i.products[n].count + ~~e.count : i.products[n] = {
                                id: e.id,
                                count: e.count,
                                size: e.size,
                                color: e.color
                            } : delete i.products[n], i.sum <= 0 ? t("#basket-submit").attr("disabled", "disabled") : t("#basket-submit").removeAttr("disabled"), t.cookie("basket", JSON.stringify(i), {
                                expires: 365,
                                path: "/",
                                domain: _
                            }), h.logic._drawBasket()
                        },
                        stickyHeader: function() {
                            var e;
                            return e = t(".layout__header-info-bar_type_sticky"), t(window).on("scroll", function() {
                                return t(document).scrollTop() > 60 ? e.addClass("layout__header-info-bar_type_sticky-show") : e.removeClass("layout__header-info-bar_type_sticky-show")
                            }), t(window).trigger("scroll")
                        },
                        flyToBasket: function() {
                            return t(".button-buy").unbind("click"), t(".button-buy").on("click", function(i) {
                                var n, a, r, s, o, l, u, c, d, p, f, h, m, g, v, _;
                                return n = t(i.target), h = ~~n.attr("data-id"), u = n.attr("data-cost"), c = n.attr("data-usd-cost"), g = n.closest(".detail-info").find("#size").val() || 0, l = n.closest(".detail-info").find("#color").val() || 0, t(".basket-bar__notifier").removeClass("pulse"), a = n.closest(".products-list__item, .hot-products__item, .detail-info").find(".product-image-" + h), d = 1, 0 !== n.parent().find("#__form__count").length && (d = ~~n.parent().find("#__form__count").val()), 0 !== a.length ? (p = -80, f = -37, a.hasClass("detail-info__image-link") && (p = -150, f = -77), o = a.clone(), m = a.offset(), o.css({
                                    position: "absolute"
                                }), s = "layout__header-info-bar_type_sticky-show", t(".layout__header-info-bar_type_sticky").hasClass(s) ? v = t(".layout__header-info-bar_type_sticky .basket-bar") : (f = t(document).scrollTop() * -1 + 20, v = t(".layout__header-info-bar .basket-bar")), _ = v.offset(), r = {
                                    start: {
                                        x: m.left,
                                        y: m.top,
                                        angle: -90
                                    },
                                    end: {
                                        x: _.left + p,
                                        y: _.top + f,
                                        angle: 180,
                                        length: .2
                                    }
                                }, o.appendTo("BODY"), o.css("opacity"), o.addClass("type_fly"), o.animate({
                                    path: new t.path.bezier(r)
                                }, 700, function() {
                                    return t(".basket-bar").removeClass("basket-bar_state_disabled"), e.publish("CHANGE_BASKET", {
                                        method: "add",
                                        id: h,
                                        count: d,
                                        cost: u,
                                        costUsd: c,
                                        color: l,
                                        size: g,
                                        oper: "add",
                                        basket: b.basket
                                    })
                                })) : (t(".basket-bar").removeClass("basket-bar_state_disabled"), e.publish("CHANGE_BASKET", {
                                    method: "add",
                                    id: h,
                                    count: d,
                                    cost: u,
                                    costUsd: c,
                                    color: l,
                                    size: g,
                                    basket: b.basket
                                })),detectHref(), i.preventDefault()
                            })
                        },
                        virtualFormElements: function() {
                            return t(".form__virtual-checkbox").on("click", function(e) {
                                var i, n, a, r;
                                return i = t(e.currentTarget), n = "form__virtual-checkbox_state_ckecked", i.toggleClass(n), r = !1, i.hasClass(n) && (r = !0, "all" === i.attr("data-name") ? t(".form__virtual-checkbox").not(i).attr("data-value", "false").removeClass("form__virtual-checkbox_state_ckecked") : t('.form__virtual-checkbox[data-name="all"]').attr("data-value", "false").removeClass("form__virtual-checkbox_state_ckecked")), i.attr("data-value", r), a = {
                                    name: i.attr("data-name"),
                                    value: i.attr("data-value")
                                }, h.logic.buildFilterHash(a), e.preventDefault()
                            }), t(window).bind("hashchange", function(t) {
                                return h.logic.buildFilterHash()
                            })
                        },
                        buildFilterHash: function(e) {
                            var i, n, a, r, s, o, l;
                            if (null == e && (e = null), e && "all" === e.name && "true" === e.value) return void(location.hash = "");
                            n = location.hash.substr(1), r = [], n && (r = n.split("&")), a = {};
                            for (o in r) l = r[o].split("="), a[l[0]] = l[1], i = t("[data-name=" + l[0] + "]"), i.attr("data-" + l[0], l[1]), "true" === l[1] ? i.addClass("form__virtual-checkbox_state_ckecked") : i.removeClass("form__virtual-checkbox_state_ckecked");
                            e && (a[e.name] = e.value), e && (a.page = 1), s = "";
                            for (o in a) "undefined" == typeof a[o] ? a[o] = "=true" : a[o] = "=" + a[o], s += o + a[o] + "&";
                            return s = s.substr(0, s.length - 1), "" !== s ? location.hash = s : void 0
                        },
                        formElements: function() {
                            return t(document).on("click", ".form__input-count-btn", function(i) {
                                var n, a, r, s, o, l, u;
                                return i.preventDefault(), n = t(this).parent().find("INPUT"), l = ~~t(this).attr("data-step"), u = ~~n.val(), s = u + l, s <= 1 && (s = 1), n.val(s), n.hasClass("basket-products__count") ? (r = ~~n.attr("data-id"), a = ~~n.attr("data-color-id"), o = ~~n.attr("data-size-id"), e.publish("CHANGE_PRODUCT_COUNT", {
                                    count: s,
                                    id: r,
                                    color: a,
                                    size: o
                                })) : e.publish("CHANGE_QUICK_COUNT", s)
                            }), t(document).on("change", ".basket-products__count", function(i) {
                                var n, a, r;
                                return a = ~~t(this).attr("data-id"), n = ~~t(this).attr("data-color-id"), r = ~~t(this).attr("data-size-id"), e.publish("CHANGE_PRODUCT_COUNT", {
                                    count: ~~t(this).val(),
                                    id: a,
                                    color: n,
                                    size: r
                                })
                            }), t("#quick-buy-form__count").on("change", function(i) {
                                return e.publish("CHANGE_QUICK_COUNT", t(this).val())
                            }), e.subscribe("CHANGE_QUICK_COUNT", function(e, i) {
                                var n, a, r, s;
                                return a = parseFloat(t(".detail-info__basket-button").attr("data-cost")), r = a * ~~i, r = numeral(r).format("0,0.00"), r = r.replace(",00", ""), n = t("#currencies LI A").first().attr("data-short-name"), "P" === n && (n = '<span class="rur">' + n + "</span>"), t(".quick-buy__total-cost").attr("data-cost", a * ~~i), s = t(".quick-buy__detail-cost").attr("data-usd-cost"), t(".quick-buy__total-cost").attr("data-usd-cost", s * t("#quick-buy-form__count").val()), "грн." === n ? t(".quick-buy__total-cost").html(numeral(a * ~~i).format("0,0") + "&thinsp;<small>" + n + "</small>") : t(".quick-buy__total-cost").html(n + r)
                            }), e.subscribe("CHANGE_PRODUCT_COUNT", function(i, n) {
                                var a, r;
                                return a = t("#product-cost-" + n.id), r = n.id.toString().concat("-", n.size, "-", n.color), n.count = n.count , n.cost = a.attr("data-cost"), n.costUsd = a.attr("data-usd-cost"), e.publish("CHANGE_BASKET", {
                                    method: "change",
                                    id: n.id,
                                    count: n.count,
                                    cost: n.cost,
                                    costUsd: n.costUsd,
                                    color: n.color,
                                    size: n.size,
                                    basket: b.basket
                                })
                            })
                        },
                        currenciesSelect: function() {
                            var e;
                            return e = this, t("#currencies .currencies__link").on("click", function(e) {
                                var i, n, a, r, s;
                                return r = t(this).attr("data-ratio"), n = t(this).attr("data-short-name"), s = t(this).attr("data-ratio-id"), "P" === n && (n = '<span class="rur">' + n + "</span>"), i = t(this).parent().detach(), i.prependTo("#currencies"), b.basket.sum = b.basket.sumUsd / r, t.cookie("basket", JSON.stringify(b.basket), {
                                    expires: 365,
                                    path: "/",
                                    domain: _
                                }), t("[data-cost]").each(function(e, i) {
                                    var a;
                                    if (a = t(this).attr("data-usd-cost"), a /= r, t("._print-cost-advedt").val(a), t("#add-ads__currency").val(s), t(this).attr("data-cost", a), t(this).hasClass("_print-cost")) return "грн." === n ? (a = numeral(a).format("0,0"), t(this).html(a + "&thinsp;<small>" + n + "</small>")) : (a = numeral(a).format("0,0.00"), a = a.replace(",00", ""), t(this).html(n + a))
                                }), a = t("#currencies LI:first A").attr("href").substr(1), t(".quick-buy__form INPUT[name=currency]").val(a), h.logic._drawBasket(), t.ajax({
                                    url: "/users/set-currency/" + a + "/",
                                    type: "POST"
                                }), e.preventDefault()
                            })
                        },
                        restoreBasket: function() {
                            return t.ajax({
                                url: "/json/basket/get/",
                                type: "GET",
                                data: {
                                    products: t.cookie("basket")
                                },
                                dataType: "json",
                                success: function(e) {
                                    var i;
                                    return e ? (e.count = ~~e.count, e.sum = parseFloat(e.sum), e.sumUsd = parseFloat(e.sumUsd), b.basket = e, t.cookie("basket", JSON.stringify(e), {
                                        expires: 365,
                                        path: "/",
                                        domain: _
                                    })) : (i = t.cookie("basket"), i && (b.basket = JSON.parse(i))), h.logic._drawBasket(), o.sidebar(b.basket)
                                }
                            })
                        },
                        typeaheadDetails: function() {
                            var e, i, n;
                            return e = new Bloodhound({
                                datumTokenizer: function(t) {
                                    return Bloodhound.tokenizers.whitespace(t.num + " " + t.nm + " " + t.info)
                                },
                                queryTokenizer: Bloodhound.tokenizers.whitespace,
                                limit: 7,
                                remote: "/json/details/search/%QUERY/result.html",
                                prefetch: "/json/details/search/presence.html"
                            }), e.initialize(), n = t("#search-query").attr("data-static-server"), i = t("#search-query").attr("data-not-available"), t(".search-element .typeahead").typeahead(null, {
                                autoselect: !0,
                                displayKey: "nm",
                                source: e.ttAdapter(),
                                templates: {
                                    suggestion: function(t) {
                                        var e;
                                        return "" === t.im && (t.im = "/uploads/images/noimage-sm.jpg"), e = "", "0" === t.pr && (e = '<span class="typeahead__not-presence">' + i + "</span>"), '<a class="typeahead__item" href="/car/' + t.carsy + "/" + t.aid + "/" + t.id + '/"><div class="typeahead__image"><img src="' + n + t.im + '">' + e + '</div><strong class="typeahead__caption">#' + t.num + '</strong><p class="typeahead__text">' + t.nm + '</p><span class="typeahead__car">' + t.car + "</span></a>"
                                    }
                                }
                            }), t(".search-element .typeahead").on("typeahead:selected", function(t, e) {
                                return window.location = "/car/" + e.carsy + "/" + e.aid + "/" + e.id + "/"
                            }), t(".search-form").on("submit", function(e) {
                                return "" !== t(this).closest("FORM").find("#search-query").val() && t(this).trigger("submit"), e.preventDefault()
                            })
                        }
                    }
                }, m = {
                    init: function() {
                        if (h.init(), h.logic.restoreBasket(), o.init(b), h.logic.stickyHeader(), h.logic.virtualFormElements(), h.logic.buildFilterHash(), h.logic.flyToBasket(), h.logic.formElements(), h.logic.currenciesSelect(), h.logic.typeaheadDetails(), u.initSubscriptionBar(), u.initAddReviewForm(), u.initAuthPopup(), u.initCabinet(), c.init(), t(".__tooltip").each(function() {
                                return t(this).powerTip({
                                    placement: t(this).data("placement") || "n",
                                    smartPlacement: !0
                                })
                            }), t(".filter-bar__button").on("click", function(e) {
                                var i;
                                return i = t(this).data("state"), t(this).toggleClass("filter-bar__button_state_checked"), "hide" === i ? (t(this).text(t(this).data("show-lng")), t(this).data("state", "show"), t(".autoparts__schema").removeClass("autoparts__schema_state_visible")) : (t(this).text(t(this).data("hide-lng")), t(this).data("state", "hide"), t(".autoparts__schema").addClass("autoparts__schema_state_visible")), e.preventDefault()
                            }), t(".layout__header-info-bar_state_invisible").removeClass("layout__header-info-bar_state_invisible"), e.subscribe("LOAD_PAGE_CONTENT", function(t) {
                                return h.logic.flyToBasket()
                            }), t(".tabs").uwinTabs(), t("#map-canvas").length) return d(t("#map-canvas"), {
                            lang: t("html").attr("lang").split("-")[0]
                        })
                    }
                }
            }(window.jQuery, window, document), f.init()
        })
    }.call(this), require(["avtoclassika"]);
