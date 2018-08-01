function Uploader(json) {
    this.config = uper_config;
    for (var k in json) {
        this.config[k] = json[k];
    }
    this.url = this.config['url'];
    var uper_form = this.config['form'];
    var uper_btn = this.config['btn'];
    var uper_cancel = this.config['cancel'];
    var uper_input = this.config['input'];
    var uper_img = this.config['img'];
    var uper_progress = this.config['progress'];
    var uper_src = this.config['src'];
    this.form = $(uper_form);
    this.btn = $(uper_form + ' ' + uper_btn);
    this.btn.after("<input class='uper-file' type='file' style='display:none'>");
    this.btn.after("<input class='uper-src' type='hidden' name='avatar'>");
    var uper_name = this.btn.attr('name');
    this.btn.attr('name', '');
    this.cancel = $(uper_form + ' ' + uper_cancel);
    this.input = $(uper_form + ' ' + uper_input);
    this.img = $(uper_form + ' ' + uper_img);
    this.progress = $(uper_form + ' ' + uper_progress);
    this.src = $(uper_form + ' ' + uper_src);
    this.src.attr('name', uper_name);
    this.subscription = null;
    if (!this.img[0]) {
        this.img = false;
        this.default = '';
    } else {
        this.src.val(this.img.attr('src'));
        this.default = this.img.attr('src');
    }
    if (!this.progress[0]) {
        this.progress = false;
    }
    var _this = this;
    this.form.find("[type = 'reset']").on('click', function () {
        if (_this.subscription != null) {
            _this.subscription.unsubscribe();
        }
        if (_this.img) {
            _this.img.attr('src', _this.default);
        }
        if (_this.progress) {
            _this.progress.html('');
        }
        _this.src.val(_this.default);
    });

    this.bind = function () {
        this.cancel.on('click', function () {
            if (_this.subscription != null) {
                _this.subscription.unsubscribe();
            }
            if (_this.img) {
                _this.img.attr('src', _this.default);
            }
            _this.src.val(_this.default);
            if (_this.progress) {
                _this.progress.html('');
            }
        });
        this.btn.on('click', function () {
            _this.input.trigger("click");
        });
        this.input.on('change', function () {
            var uper_file = _this.input.get(0).files[0];
            if ((typeof(uper_file) == "undefined")) {
                return false;
            }
            if (_this.subscription != null) {
                _this.subscription.unsubscribe();
            }
            var config = {
                useCdnDomain: true
            };
            var putExtra = {};
            $.ajax({
                url: _this.url,
                type: 'POST',
                dataType: 'json',
                data: {
                    fkey: uper_file.name
                },
                success: function (response) {
                    var observable = qiniu.upload(uper_file, response.fname, response.token, putExtra, config);
                    var observer = {
                        next: function (res) {
                            if (_this.progress) {
                                _this.progress.html(res.total.percent);
                            }
                        },
                        error: function (err) {
                            var msg = err.code + '上传错误';
                            switch (err.code) {
                                case 413:
                                    msg = "文件超出大小";
                                    break;
                                case 403:
                                    msg = "文件格式错误";
                            }
                            if (_this.progress) {
                                _this.progress.html(msg);
                            }
                        },
                        complete: function (res) {
                            if (_this.progress) {
                                _this.progress.html(_this.config['done']);
                            }
                            if (_this.img) {
                                _this.img.attr('src', response.fileurl);
                            }
                            _this.src.val(response.fileurl);
                            _this.subscription = null;
                        }
                    }
                    if (_this.img) {
                        _this.img.attr('src', '');
                    }
                    _this.src.val('');
                    _this.subscription = observable.subscribe(observer);
                }
            });
        })
    };
    return this;
}