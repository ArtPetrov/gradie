const windowForm = 'div.quick-order-form';
const windowThanks = 'div.quick-order-thanks';

function errorFields(name, error) {
    if (error) {
        $('div.quick-order-' + name).addClass('quick-order__field--error');
    } else {
        $('div.quick-order-' + name).removeClass('quick-order__field--error');
    }
}

function clearFields() {
    errorFields('name', false);
    errorFields('contact', false);
    $('input[name="quick-order-name"]').val('');
    $('input[name="quick-order-contact"]').val('');
}

if ($(windowForm).length !== 0) {
    $("button.quick-order-make").click(function (e) {
            let name = $('input[name="quick-order-name"]').val();
            let contact = $('input[name="quick-order-contact"]').val();
            let product = $('input[name="quick-order-id"]').val();
            let count = $('input[name="quick-order-count"]').val();

            if (name.length < 2 || name.length > 256) {
                return errorFields('name', true);
            } else {
                errorFields('name', false);
            }

            if (contact.length < 2 || contact.length > 256) {
                return errorFields('contact', true);
            } else {
                errorFields('contact', false);
            }

            $.ajax({
                url: $('input[name="quick-order-path"]').val(),
                type: "POST",
                dataType: "json",
                data: {"name": name, "product": product, "contact": contact, "count": count},
                statusCode: {
                    200: function () {
                        $(windowForm).hide();
                        clearFields();
                        $(windowThanks).show();
                    },
                    406: function (e) {
                        switch (e.responseJSON.error) {
                            case 'quick.order.name.incorrectly':
                                errorFields('name', true);
                                break;
                            case 'quick.order.contact.incorrectly':
                                errorFields('contact', true);
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