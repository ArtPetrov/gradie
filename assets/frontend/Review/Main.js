const windowForm = 'div.review-form';
const windowThanks = 'div.review-thanks';

function errorFields(name, error) {
    if (error) {
        $('div.review-field-' + name).addClass('add-review__field--error');
    } else {
        $('div.review-field-' + name).removeClass('add-review__field--error');
    }
}

function clearFields() {
    errorFields('name', false);
    errorFields('message', false);
    $('input[name="review-name"]').val('');
    $('input[name="review-rating"][value="5"]').prop("checked", true);
    $('textarea[name="review-message"]').val('');
}

if ($(windowForm).length !== 0) {
    $("button.review-send").click(function (e) {
            let name = $('input[name="review-name"]').val();
            let rating = $('input[name="review-rating"]:checked').val();
            let message = $('textarea[name="review-message"]').val();
            let product = $('input[name="product-id"]').val();
            if (name.length < 2 || name.length > 256) {
                return errorFields('name', true);
            } else {
                errorFields('name', false);
            }

            if (message.length < 10 || message.length > 256) {
                return errorFields('message', true);
            } else {
                errorFields('message', false);
            }

            $.ajax({
                url: $('input[name="review-path"]').val(),
                type: "POST",
                dataType: "json",
                data: {"name": name, "product": product, "message": message, "rating": rating},
                statusCode: {
                    200: function () {
                        $(windowForm).hide();
                        clearFields();
                        $(windowThanks).show();
                    },
                    406: function (e) {
                        switch (e.responseJSON.error) {
                            case 'review.name.incorrectly':
                                errorFields('name', true);
                                break;
                            case 'review.message.incorrectly':
                                errorFields('message', true);
                                break;
                            default:
                                console.log(e);
                        }
                    },
                }
            });

        }
    )
}