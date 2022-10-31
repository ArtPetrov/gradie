require('jquery-datetimepicker');

import('jquery-datetimepicker/build/jquery.datetimepicker.min.css');

$(document).ready(function () {

    if (typeof $.datetimepicker !== 'undefined') {

        $.datetimepicker.setLocale('ru');

        $("input.datetime").datetimepicker({
            format: 'd.m.Y H:i',
            formatDate: 'd.m.Y H:i'
        });
    }
});