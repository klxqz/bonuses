(function($) {
    "use strict";
    $.storage = new $.store();
    $.bonuses = {
        options: {},
        // last list view user has visited: {title: "...", hash: "..."}
        lastView: null,
        init: function(options) {
            var that = this;
            that.options = options;
            if (typeof ($.History) != "undefined") {
                $.History.bind(function() {
                    that.dispatch();
                });
            }
            $.wa.errorHandler = function(xhr) {
                if ((xhr.status === 403) || (xhr.status === 404)) {
                    var text = $(xhr.responseText);
                    if (text.find('.dialog-content').length) {
                        text = $('<div class="block double-padded"></div>').append(text.find('.dialog-content *'));

                    } else {
                        text = $('<div class="block double-padded"></div>').append(text.find(':not(style)'));
                    }
                    $("#bunuses-content").empty().append(text);
                    that.onPageNotFound();
                    return false;
                }
                return true;
            };
            var hash = this.getHash();
            if (hash === '#/' || !hash) {
                this.dispatch();
            } else {
                $.wa.setHash(hash);
            }

        },
        // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
        // *   Dispatch-related
        // * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

        // if this is > 0 then this.dispatch() decrements it and ignores a call
        skipDispatch: 0,
        /** Cancel the next n automatic dispatches when window.location.hash changes */
        stopDispatch: function(n) {
            this.skipDispatch = n;
        },
        /** Force reload current hash-based 'page'. */
        redispatch: function() {
            this.currentHash = null;
            this.dispatch();
        },
        dispatch: function(hash) {
            if (this.skipDispatch > 0) {
                this.skipDispatch--;
                return false;
            }

            if (hash === undefined || hash === null) {
                hash = this.getHash();
            }
            if (this.currentHash == hash) {
                return;
            }

            this.currentHash = hash;
            hash = hash.replace('#/', '');

            if (hash) {
                hash = hash.split('/');
                if (hash[0]) {
                    var actionName = "";
                    var attrMarker = hash.length;
                    for (var i = 0; i < hash.length; i++) {
                        var h = hash[i];
                        if (i < 2) {
                            if (i === 0) {
                                actionName = h;
                            } else if (parseInt(h, 10) != h && h.indexOf('=') == -1) {
                                actionName += h.substr(0, 1).toUpperCase() + h.substr(1);
                            } else {
                                attrMarker = i;
                                break;
                            }
                        } else {
                            attrMarker = i;
                            break;
                        }
                    }
                    var attr = hash.slice(attrMarker);
                    this.preExecute(actionName);
                    if (typeof (this[actionName + 'Action']) == 'function') {
                        $.shop.trace('$.bonuses.dispatch', [actionName + 'Action', attr]);
                        this[actionName + 'Action'].apply(this, attr);
                    } else {
                        $.shop.error('Invalid action name:', actionName + 'Action');
                    }
                    this.postExecute(actionName);
                } else {
                    this.preExecute();
                    this.defaultAction();
                    this.postExecute();
                }
            } else {
                this.preExecute();
                this.defaultAction();
                this.postExecute();
            }


        },
        preExecute: function(actionName, attr) {
        },
        postExecute: function(actionName, attr) {
            this.actionName = actionName;
        },
        defaultAction: function() {
            this.load('?plugin=bonuses&action=bonuses', function() {
                $.bonuses.initButtons();
                $.bonuses.selectAllInit();
            });
        },
        selectAllInit: function() {
            $('.bonuses-select-all').click(function() {
                if ($(this).attr('checked')) {
                    $('.select-bonuses-checkbox').attr('checked', 'checked');
                } else {
                    $('.select-bonuses-checkbox').removeAttr('checked');
                }
            });
        },
        initButtons: function() {
            var _csrf = $('input[name="_csrf"]').val();
            $('#bonuses-list').on('click', '.edit', function() {
                var $tr = $(this).closest('tr');
                $tr.find('.date_text').hide();
                $tr.find('.date_input').show();
                $tr.find('.bonus_val').hide();
                $tr.find('.bonus_input').show();
                $tr.find('.edit').hide();
                $tr.find('.delete').hide();
                $tr.find('.cancel').show();
                $tr.find('.save').show();

            });
            $('#bonuses-list').on('click', '.cancel', function() {
                var $tr = $(this).closest('tr');
                $tr.find('.date_text').show();
                $tr.find('.date_input').hide();
                $tr.find('.bonus_val').show();
                $tr.find('.bonus_input').hide();
                $tr.find('.edit').show();
                $tr.find('.delete').show();
                $tr.find('.cancel').hide();
                $tr.find('.save').hide();
            });

            $('#bonuses-list').on('click', '.save', function() {
                var $tr = $(this).closest('tr');
                $tr.find('i.loading').css('display', 'inline-block');
                $tr.find('.cancel').hide();
                $.ajax({
                    type: 'POST',
                    url: '?plugin=bonuses&action=save',
                    dataType: 'json',
                    data: {
                        id: $tr.attr('data-bonuses-id'),
                        bonus: $tr.find('.bonus_input').val(),
                        date: $tr.find('.date_input').val(),
                        _csrf: _csrf
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $tr.find('.bonus_val').text(data.data.bonus);
                            $tr.find('.date_text').text($tr.find('.date_input').val());
                            $tr.find('.cancel').click();
                        } else {
                            alert(data.errors);
                            $tr.find('.cancel').click();
                        }
                        $tr.find('i.loading').hide();
                    }
                });

            });
            $('#bonuses-list').on('click', '.delete', function() {
                if (!confirm('Вы уверены')) {
                    return false;
                }
                var $tr = $(this).closest('tr');
                $tr.find('i.loading').css('display', 'inline-block');
                $tr.find('.save').hide();
                $.ajax({
                    type: 'POST',
                    url: '?plugin=bonuses&action=delete',
                    dataType: 'json',
                    data: {
                        id: $tr.attr('data-bonuses-id'),
                        _csrf: _csrf
                    },
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $tr.remove();
                        } else {
                            alert(data.errors);
                            $tr.find('.cancel').click();
                        }
                    }
                });

            });
            $('.delete-selected-but').click(function() {
                var $form = $('.bonuses-form');
                $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serializeArray(),
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $.bonuses.defaultAction();
                        }
                    },
                    error: function(jqXHR, errorText) {
                    }
                });
            });

        },
        addbonusesAction: function() {
            this.load('?plugin=bonuses&action=addbonuses', function() {
                $.bonuses.initAddHandler();
            });
        },
        initLazyLoad: function(options) {

            var count = options.count;
            var offset = count;
            var total_count = options.total_count;
            var url = options.url || '?plugin=bonuses&action=bonuses';
            var target = $(options.target || '#bonuses-list');

            $(window).lazyLoad('stop');  // stop previous lazy-load implementation

            if (offset < total_count) {
                $(window).lazyLoad({
                    container: target,
                    state: (typeof options.auto === 'undefined' ? true : options.auto) ? 'wake' : 'stop',
                    load: function() {
                        $(window).lazyLoad('sleep');
                        $('.lazyloading-link').hide();
                        $('.lazyloading-progress').show();
                        $.get(
                                url + '&lazy=1&offset=' + offset + '&total_count=' + total_count,
                                function(html) {
                                    var data = $('<div></div>').html(html);
                                    var children = data.find('#bonuses-list').children();
                                    offset += count;
                                    target.append(children);
                                    $('.lazyloading-progress-string').replaceWith(data.find('.lazyloading-progress-string'));
                                    $('.lazyloading-progress').replaceWith(data.find('.lazyloading-progress'));
                                    if (offset >= total_count) {
                                        $(window).lazyLoad('stop');
                                        $('.lazyloading-link').hide();
                                    } else {
                                        $('.lazyloading-link').show();
                                        $(window).lazyLoad('wake');
                                    }
                                    data.remove();
                                },
                                "html"
                                );
                    }
                });
                $('.lazyloading-link').unbind('click').bind('click', function() {
                    $(window).lazyLoad('force');
                    return false;
                });
            }
        },
        initAddHandler: function() {
            var autocompete_input = $("#customer-autocomplete");
            autocompete_input.autocomplete({
                source: function(request, response) {
                    var term = request.term;
                    $.getJSON('?action=autocomplete&type=contact', request, function(r) {

                        response(r);
                    });
                },
                delay: 300,
                minLength: 3,
                select: function(event, ui) {
                    var item = ui.item;
                    if (item.value) {
                        $('#s-customer-id').val(item.value);
                        $('.field-group').html('<i class="icon16 loading"></i>');
                        $.ajax({
                            type: 'POST',
                            url: '?plugin=bonuses&action=contactForm',
                            dataType: 'json',
                            data: {
                                id: item.value
                            },
                            success: function(data, textStatus, jqXHR) {
                                if (data.status == 'ok') {
                                    $('.field-group').html(data.data.html_form);
                                    $('.bonus_input').val(data.data.bonus);
                                    $('.date_input').val(data.data.date);
                                } else {
                                    alert(data.errors);
                                }
                            }
                        });
                    }
                    return false;
                },
                focus: function(event, ui) {
                    this.value = ui.item.name;
                    return false;
                }
            });
            $('form.add-bonuses-form').submit(function() {
                $('#response-status').html('<i style="vertical-align:middle" class="icon16 loading"></i>');
                $('#response-status').show();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data, textStatus, jqXHR) {
                        if (data.status == 'ok') {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 yes"></i>Сохранено');
                            $('#response-status').css('color', '#008727');
                        } else {
                            $('#response-status').html('<i style="vertical-align:middle" class="icon16 no"></i>' + data.errors);
                            $('#response-status').css('color', '#FF0000');
                        }
                        setTimeout(function() {
                            $('#response-status').hide();
                        }, 3000);
                    }
                });
                return false;
            });
        },
        /** Current hash */
        getHash: function() {
            return this.cleanHash();
        },
        /** Make sure hash has a # in the begining and exactly one / at the end.
         * For empty hashes (including #, #/, #// etc.) return an empty string.
         * Otherwise, return the cleaned hash.
         * When hash is not specified, current hash is used. */
        cleanHash: function(hash) {
            if (typeof hash == 'undefined') {
                hash = window.location.hash.toString();
            }

            if (!hash.length) {
                hash = '' + hash;
            }
            while (hash.length > 0 && hash[hash.length - 1] === '/') {
                hash = hash.substr(0, hash.length - 1);
            }
            hash += '/';

            if (hash[0] != '#') {
                if (hash[0] != '/') {
                    hash = '/' + hash;
                }
                hash = '#' + hash;
            } else if (hash[1] && hash[1] != '/') {
                hash = '#/' + hash.substr(1);
            }

            if (hash == '#/') {
                return '';
            }

            return hash;
        },
        load: function(url, options, fn) {
            if (typeof options === 'function') {
                fn = options;
                options = {};
            } else {
                options = options || {};
            }
            var r = Math.random();
            this.random = r;
            var self = this;



            (options.content || $("#bonuses-content")).html('<div class="block triple-padded"><i class="icon16 loading"></i>Loading...</div>');
            return  $.get(url, function(result) {
                if ((typeof options.check === 'undefined' || options.check) && self.random != r) {
                    // too late: user clicked something else.
                    return;
                }

                (options.content || $("#bonuses-content")).removeClass('bordered-left').html(result);
                if (typeof fn === 'function') {
                    fn.call(this);
                }

            });
        },
        onPageNotFound: function() {
            //this.defaultAction();
        }
    };



})(jQuery);