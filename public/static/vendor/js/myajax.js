function imgAutoSize(conf) {
    this.config = {
        id: ".autosize",
        maxwidth: '90%',
        maxheigth: '90%'
    }
    for (var k in conf) {
        this.config[k] = conf[k];
    }
    this.id = this.config["id"];
    this.maxwidth = this.config["maxwidth"];
    this.maxheigth = this.config["maxheigth"];
    this.img = [];
    var _this = this;
    this.size = function (conf) {
        for (var k in conf) {
            this.config[k] = conf[k];
        }
        this.id = this.config["id"];
        this.maxwidth = this.config["maxwidth"];
        this.maxheigth = this.config["maxheigth"];
        var imgs = $(this.id);
        for (var i = 0; i < imgs.length; i++) {
            this.img[i] = $(imgs[i]);
            var theImage = new Image();
            theImage.src = this.img[i].attr("src");
            var imageWidth = theImage.width;
            var imageHeight = theImage.height;
            var wh1 = imageWidth / imageHeight;
            var wh2 = this.img[i].parent().width() / this.img[i].parent().height();
            if (wh1 > wh2) {
                this.img[i].width(_this.maxwidth);
                this.img[i].height('auto');
            } else {
                this.img[i].height(_this.maxwidth);
                this.img[i].width('auto');
            }
            var im = this.img[i];
            this.img[i].css('marginTop', (im.parent().height() - im.height()) / 2);
            this.img[i].css('marginLeft', 'auto');
            this.img[i].css('marginRight', 'auto');
            this.img[i].on('load', function () {
                var theImage = new Image();
                theImage.src = $(this).attr("src");
                var imageWidth = theImage.width;
                var imageHeight = theImage.height;
                var wh1 = imageWidth / imageHeight;
                var wh2 = $(this).parent().width() / $(this).parent().height();
                if (wh1 > wh2) {
                    $(this).width(_this.maxwidth);
                    $(this).height('auto');
                } else {
                    $(this).height(_this.maxwidth);
                    $(this).width('auto');
                }
                var im = $(this);
                $(this).css('marginTop', (im.parent().height() - im.height()) / 2);
            });
        }
    }
    this.size();
}

function deepCopy(obj) {
    if (typeof obj != 'object') {
        return obj;
    }
    var newobj = {};
    for (var attr in obj) {
        newobj[attr] = deepCopy(obj[attr]);
    }
    return newobj;
}

if (typeof(Handlebars) != "undefined") {
    Handlebars.registerHelper('compare', function (left, operator, right, options) {
        if (arguments.length < 3) {
            throw new Error('Handlerbars Helper "compare" needs 2 parameters');
        }
        var operators = {
            '%': function (l, r) {
                if (l % r == 0) {
                    return true;
                } else {
                    return false;
                }
            },
            '![]': function (l, r) {
                var i = 0;
                for (var k in l) {
                    i++;
                }
                if (i > 0) {
                    return true;
                } else {
                    false;
                }
            },
            '%3=': function (l, r) {
                return (l % 3) == r;
            },
            '==': function (l, r) {
                return l == r;
            },
            '===': function (l, r) {
                return l === r;
            },
            '!=': function (l, r) {
                return l != r;
            },
            '!==': function (l, r) {
                return l !== r;
            },
            '<': function (l, r) {
                return l < r;
            },
            '>': function (l, r) {
                return l > r;
            },
            '<=': function (l, r) {
                return l <= r;
            },
            '>=': function (l, r) {
                return l >= r;
            },
            'typeof': function (l, r) {
                return typeof l == r;
            }
        };

        if (!operators[operator]) {
            throw new Error('Handlerbars Helper "compare" doesn\'t know the operator ' + operator);
        }

        var result = operators[operator](left, right);

        if (result) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });
    Handlebars.registerHelper('indd', function (num, options) {
        num = num + 1;
        return num;
    });
    Handlebars.registerHelper('consol', function (num, options) {
        console.log(num);
    });
    Handlebars.registerHelper('morl', function (num, options) {
        return num % options;
    });
}

function Ajaxer(config) {
    this.config = deepCopy(ajax_config);
    this.res = null;
    this.init = function (conf) {
        for (var k in conf) {
            this.config[k] = conf[k];
        }
        this.main = this.config['main'];
        this.config['main'] = null;
        this.url = this.config['url'];
        this.model = this.config['model'];
        this.box = this.config['box'];
        this.tpl = this.config['tpl'];
        this.operate = this.config['operate'];
        this.id = this.config['id'];
        this.attr = this.config['attr'];
        this.mkey = this.config['mkey'];
        this.ajaxid = this.config['ajaxid'];
        if (typeof(this.ajaxid) != "boolean" && typeof(this.ajaxid) != "object") {
            this.ajaxid = [this.ajaxid.toString()];
        } else if (typeof(this.ajaxid) == "object") {
            for (var k in this.ajaxid) {
                this.ajaxid[k] = this.ajaxid[k].toString();
            }
        }
    }
    this.init(config);
    var _this = this;
    var success = function (res) {
        Ajaxer.call(this, _this.config);
        if (res.code > 0) {
            var row = {};
            if (res.row[0] && typeof(res.row) == "object") {
                row['arr'] = {};
                for (var j in res.row) {
                    if (res.row[j][this.mkey]) {
                        row['arr'][j] = res.row[j];
                    } else {
                        row[j] = res.row[j];
                    }
                }
                this.res = row;
                _this.res = row;
            } else {
                this.res = res.row;
                _this.res = res.row;
            }
            if (typeof(this.config['success']) == "function") {
                this.fs = this.config['success'];
                this.fs();
                _this.res = this.res;
            }
            var tpl_arr = [];
            $(this.tpl).ready(function () {
                Ajaxer.call(this, _this.config);
                this.res = _this.res;
                for (var i = 0; i < $(this.tpl).length; i++) {
                    if (this.ajaxid == false || $.inArray($($(this.tpl)[i]).attr(this.attr), this.ajaxid) >= 0) {
                        var tpl = $($(this.tpl)[i]).html();
                        var template = Handlebars.compile(tpl);
                        var html = template(this.res);
                        tpl_arr[$($(this.tpl)[i]).attr(this.attr)] = html;
                    }
                }
                $(this.box).ready(function () {
                    Ajaxer.call(this, _this.config);
                    this.res = _this.res;
                    for (var i = 0; i < $(this.box).length; i++) {
                        if (this.ajaxid == false || $.inArray($($(this.box)[i]).attr(this.attr), this.ajaxid) >= 0) {
                            $($(this.box)[i]).html(tpl_arr[$($(this.box)[i]).attr(this.attr)]);
                        }
                    }
                    if (typeof(this.config['after']) == "function") {
                        this.fa = this.config['after'];
                        this.fa();
                        _this.res = this.res;
                    }
                });
            });
        } else {
            if (this.config['error']) {
                var fe = this.config['error'];
                fe(res);
            } else {
                console.log(res);
            }
        }
    };
    this.rander = function (config2) {
        this.init(config2);
        $.post(this.url,
            {
                model: this.model,
                id: this.id,
                operate: JSON.stringify(this.operate)
            },
            success,
            'json'
        )
    }
    if (this.main != null) {
        this.main();
    }
}