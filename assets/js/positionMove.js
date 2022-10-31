$(function () {
    $(".move-position").click(function () {
        let link = $(this);
        let token_csrf = $("input[name='token_csrf']").val();
        let path = link.data('path');
        let tagParent = link.data('parent');
        let direction = link.data('direction');
        $.ajax({
            url: path,
            cache: false,
            type: 'POST',
            dataType: "json",
            data: {token_csrf: token_csrf, direction: direction, rnd: Math.floor(Math.random() * 10000)}
        }).statusCode({
            401: function () {
                console.log('Error CSRF token.');
            },
            200: function () {
                let row = link.closest(tagParent);

                if (direction === 'up') {
                    if (row.prev().length > 0) {
                        row.insertBefore(row.prev());
                    } else {
                        location.reload();
                    }
                }

                if (direction === 'down') {
                    if (row.next().length > 0) {
                        row.insertAfter(row.next());
                    } else {
                        location.reload();
                    }
                }

            }
        });
    });
});


