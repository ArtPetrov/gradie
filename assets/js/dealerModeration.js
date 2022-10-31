$(function () {

    function sendRequest(path, params = {}) {
        let data = Object.assign(params,{
            token_csrf: $("input[name='token_csrf']").val(),
            rnd: Math.floor(Math.random() * 10000)
        });
        $.ajax({
            url: path,
            cache: false,
            type: 'POST',
            dataType: "json",
            data: data
        }).statusCode({
            401: function () {
                console.log('Error CSRF token.');
            },
            204: function () {
                $('#moderation').modal('hide');
                location.reload();
            }
        });
    }

    function path(nameInput) {
        let id = $("input[name='dealer_id']").val();
        return $("input[name='" + nameInput + "']").val().replace('_id_', id);
    }

    $(".dealer-moderation").click(function () {
        $("input[name='dealer_id']").val($(this).data('id'));
    });

    $(".dealer-moderation-active").click(function () {
        let params = {};
        params.category = $("select[name='form[categories]']").val();
        params.manager = $("select[name='form[managers]']").val();
        sendRequest(path('path_for_active'), params);
    });
    $(".dealer-moderation-block").click(function () {
        sendRequest(path('path_for_blocked'));
    });

});






