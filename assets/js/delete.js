$(function () {
    $(".delete-element").click(function () {
        let message = confirm('Уверены, что хотите выполнить это действие?');
        if (!message) return null;

        let link = $(this);
        let token_csrf = $("input[name='token_csrf']").val();
        let path = link.data('path');
        $.ajax({
            url: path,
            cache: false,
            type: 'DELETE',
            dataType: "json",
            data: {token_csrf: token_csrf, rnd: Math.floor(Math.random() * 10000)}
        }).statusCode({
            401: function () {
                console.log('Error CSRF token.');
            },
            204: function () {
                link.closest(link.data('parent')).remove();
            }
        });
    });
});






