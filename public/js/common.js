function show_message(msg, status) {
    var cls = "errorbox";
    if (status == 'true')
        cls = "allokbox";

    $('#message-layer-text').html(msg);
    $('#message-layer-text').attr('class', cls);
    // появление и исчезание блока
    $('#message-layer').hide()
        .clearQueue()
        .click(function () {
            $(this).hide();
            $(this).clearQueue();
        })
        .toggle(200);
    if (status == 'true')
        $('#message-layer').delay(3000).toggle(200);
}

function show_message_permanent(msg, status) {
    var cls = "errorbox";
    if (status === "true")
        cls = "allokbox";

    $('#message-layer-text').html(msg);
    $('#message-layer-text').attr('class', cls);
    // появление и исчезание блока
    $('#message-layer').hide()
        .clearQueue()
        .click(function () {
            $(this).hide();
            $(this).clearQueue();
        })
        .toggle(200);
}

$(document).ready(function (e) {
    $.ajaxSetup({
        dataType: 'json',
        beforeSend: function (jqXHR, settings) {
            $('#loading-layer').show();
        },
        complete: function (jqXHR, settings) {
            $('#loading-layer').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            let errorMessage = textStatus;
            if (jqXHR.responseJSON && jqXHR.responseJSON.error)
                errorMessage = jqXHR.responseJSON.error;
            if (jqXHR.responseText) {
                let data = JSON.parse(jqXHR.responseText);
                if (typeof data !== undefined)
                    errorMessage = data.error;
            }
            show_message('Ошибка: ' + errorMessage + '<br> Код возврата: ' + jqXHR.statusText + ' (' + jqXHR.status + ')', 'false');
        }
    });
});

String.prototype.escape = function () {
    let tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;'
    };
    return this.replace(/[&<>"]/g, function (tag) {
        return tagsToReplace[tag] || tag;
    });
};

function makeSpinner(selector, options) {
    $(selector).each(function (index, el) {
        let mySpinner = {
            maxValue: null,
            minValue: null,
            currentValue: 0,
            step: 1,
            target: null,
            updateHandler: null,
            setValue: function (value) {
                this.currentValue = value;
                this.target.val(value);
                if (this.updateHandler) {
                    this.updateHandler.call(this.target, value);
                }
            },
            valueUp: function () {
                if (this.maxValue !== null) {
                    if ((this.currentValue + this.step) > this.maxValue) {
                        return;
                    }
                }
                this.setValue(this.currentValue + this.step);
            },
            valueDown: function () {
                if (this.minValue !== null) {
                    if ((this.currentValue - this.step) < this.minValue) {
                        return;
                    }
                }
                this.setValue(this.currentValue - this.step);
            }
        };

        let btnUp = $('<a class="spinner-button spinner-button-up fa fa-angle-up rounded-top" href="#" tabindex="-1"></a>')
            .on('tap', function (event) {
                event.preventDefault();
                return mySpinner.valueUp();
            })
            .on('click', function (event) {
                event.preventDefault();
            });
        let btnDown = $('<a class="spinner-button spinner-button-down fa fa-angle-down rounded-bottom" href="#" tabindex="-1"></a>')
            .on('tap', function (event) {
                event.preventDefault();
                return mySpinner.valueDown();
            })
            .on('click', function (event) {
                event.preventDefault();
            });
        mySpinner.target = $(el);
        mySpinner.target
            .on('keydown', function (event) {
                if (event.key === 'ArrowUp' || event.code === 'ArrowUp') {
                    return mySpinner.valueUp();
                }
                if (event.key === 'ArrowDown' || event.code === 'ArrowDown') {
                    return mySpinner.valueDown();
                }
            })
            .on('change', function (event) {
                mySpinner.setValue(parseInt(mySpinner.target.val()));
            });
        if (options !== undefined) {
            mySpinner = $.extend(mySpinner, options);
        }
        let settings = mySpinner.target.data('options');
        if (settings !== undefined) {
            mySpinner = $.extend(mySpinner, settings);
        }
        // var options = mySpinner.target.data('options') || options;
        mySpinner.currentValue = parseInt(mySpinner.target.val());
        mySpinner.target
            .addClass('spinner-input')
            .wrap('<span class="spinner-container rounded"></span>')
            .parent()
            .append(btnUp)
            .append(btnDown);
    });
}
