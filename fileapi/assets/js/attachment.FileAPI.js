(function ($) {
    'use strict';

    $.fn.yiiAttachmentFileAPI = function (method, options) {
        var slice = [].slice;

        if (methods[method]) {
            return methods[method].apply(this, slice.call(arguments, 1));
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.yiiAttachmentFileAPI');
        }
    };


    var _dataAttr = 'data-attachment',
        _optDataAttr = function (name){
            return '['+_dataAttr+'="'+name+'"]';
        };

    var defaults = {
        file: {
            elements: {
                ctrl: {
                    upload: _optDataAttr('ctrl.upload'),
                    reset: _optDataAttr('ctrl.reset'),
                    abort: _optDataAttr('ctrl.abort')
                },
                empty: {
                    show: _optDataAttr('empty.show'),
                    hide: _optDataAttr('empty.hide')
                },
                emptyQueue: {
                    show: _optDataAttr('emptyQueue.show'),
                    hide: _optDataAttr('emptyQueue.hide')
                },
                active: {
                    show: _optDataAttr('active.show'),
                    hide: _optDataAttr('active.hide')
                },
                size: _optDataAttr('size'),
                name: _optDataAttr('name'),
                progress: _optDataAttr('progress'),
                list: _optDataAttr('list'),
                file: {
                    tpl: _optDataAttr('file.tpl'),
                    progress: _optDataAttr('file.progress'),
                    active: {
                        show: _optDataAttr('file.active.show'),
                        hide: _optDataAttr('file.active.hide')
                    },
                    preview: {
                        el: _optDataAttr('file.preview'),
                        get: 0,
                        width: 80,
                        height: 80,
                        processing: 0
                    },
                    abort: _optDataAttr('file.abort'),
                    remove: _optDataAttr('file.remove'),
                    rotate: _optDataAttr('file.rotate')
                },
                dnd: {
                    el: _optDataAttr('dnd'),
                    hover: 'dnd_hover',
                    fallback: _optDataAttr('dnd.fallback')
                }
            }
        },
        image: {
            accept: 'image/*',
            imageSize: { minWidth: 200, minHeight: 200 },
            preview: {
                el: _optDataAttr('preview'),
                width: 80,
                height: 80
            }
        }
    };

    var events = {
        image: {
            onSelect: function ($instance, settings) {
                return function (e, ui) {
                    var file = ui.files[0],
                        selector = settings.selector,
                        modalSelector = methods.getModalSelector(selector);

                    if (file) {
                        methods.openModal(modalSelector, function (overlay) {
                            $(overlay).on('click', '.js-upload', function () {
                                $.modal().close();
                                $('#userpic').fileapi('upload');
                            });
                            $('.js-img', overlay).cropper({
                                file: file,
                                bgColor: '#fff',
                                maxSize: [$(window).width() - 100, $(window).height() - 100],
                                minSize: [200, 200],
                                selection: '90%',
                                onSelect: function (coords) {
                                    $('#userpic').fileapi('crop', file, coords);
                                }
                            });
                        });
                    }
                };
            }
        },
        file: {
            onFileComplete: function ($instance, settings) {
                return function (e, ui) {
                    var file;

                    console.log('file.onFileComplete', arguments);

                    if (ui.result) {
                        file = $.extend({}, ui.file, ui.result);

                        if (settings.multiple === true) {
                            if (ui.result.status) {
                                methods.fillFileIDs([file]);
                            } else {
                                methods.showErrors([file]);
                            }
                        } else if (settings.multiple === false) {
                            if (ui.result.status) {
                                methods.fillFileID($instance, file);
                                methods.updatePreview(file);
                            } else {
                                methods.showError($instance, file);
                            }
                        }
                    }
                };
            }
        }
    };

    var methods = {
        file: function (options) {
            return this.each(function () {
                var settings = $.extend({}, defaults.file, options || {}),
                    fileapi,
                    $this = $(this);

                settings.onFileComplete = events.file.onFileComplete($this, settings);

                fileapi = $this.fileapi(settings).data('fileapi');

                console.log('file', settings);

                $(document).on('click', _optDataAttr('remove'), function(evt) {
                    evt.preventDefault();
                    $this.fileapi('clear');
                    $this.find(_optDataAttr('input')).val('');
                    $(_optDataAttr('preview')).find('img').detach();
                });

                if (fileapi && fileapi.files.length) {
                    if (settings.multiple === true) {
                        methods.fillFileIDs(fileapi.files);
                    } else if (settings.multiple === false) {
                        methods.fillFileID($this, fileapi.files[0]);
                    }
                }
            });
        },

        image: function (options) {
            return this.each(function () {
                var settings = $.extend({}, defaults.file, defaults.image, options || {}),
                    selector = settings.selector,
                    modalSelector = methods.getModalSelector(selector),
                    fileapi,
                    $this = $(this);

                $(document).on('click', modalSelector + ' [data-attachment="crop.save"]', function() {
                    $this.fileapi('upload');
                    $(modalSelector).modal('hide');
                });

                settings.onFileComplete = events.image.onFileComplete($this, settings);
                settings.onSelect = events.image.onSelect($this, settings);

                fileapi = $this.fileapi(settings).data('fileapi');

                if (fileapi && fileapi.files) {
                    methods.fillFileIDs(fileapi.files);
                }
            });
        },

        showErrors: function(files) {
            var $error;

            if (files && files.length) {
                files.forEach(function(file) {
                    if (file.$el && file.errors) {
                        $error = file.$el.find(_optDataAttr('file.error'));

                        if ($error && $error.length) {
                            $error.show().html(methods.fileErrors(file.errors));
                        }
                    }
                }, this);
            }
        },

        fileErrors: function(errors) {
            var text = '';

            if (errors) {
                $.each(errors, function(idx, value) {
                    if (Array.isArray(value)) {
                        text += value.join() + "\n";
                    } else {
                        text += value + "\n";
                    }
                });
            }

            return text;
        },

        showError: function($el, file) {
            var $error;

            if (file && file.errors) {
                $error = $el.find(_optDataAttr('error'));

                if ($error && $error.length) {
                    $error.show().html(methods.fileErrors(file.errors));
                }
            }
        },

        fillFileIDs: function(files) {
            var $input;

            if (files && files.length) {
                files.forEach(function(file) {
                    if (file.$el) {
                        $input = file.$el.find(_optDataAttr('file.input'));

                        if ($input && $input.length) {
                            $input.val(file.id);
                        }
                    }
                });
            }
        },

        fillFileID: function($el, file) {
            var $input;

            if (file && file.id) {
                $input = $el.find(_optDataAttr('input'));

                if ($input && $input.length) {
                    $input.val(file.id);
                }
            }
        },

        updatePreview: function(file) {
            var $preview;

            if (file && file.id) {
                $preview = $(_optDataAttr('preview'));

                if ($preview && $preview.length) {
                    $preview.html($('<img />', {src: file.src}));
                }
            }
        },

        getModalSelector: function (selector) {
            return '#' + selector + '-crop-modal';
        },

        openModal: function (selector, onOpen) {
            return $(selector).modal({
                closeOnEsc: true,
                closeOnOverlayClick: false,
                onOpen: onOpen
            }).open();
        }
    };

})(window.jQuery);
