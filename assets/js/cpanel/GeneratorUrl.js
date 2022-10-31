const SelectorValue = '.url-generator-name';
const SelectorField = '.url-generator-field';
const SelectorAction = '.url-generator-actor';
const pathToGenerator = $(SelectorAction).data('path');

if ($(SelectorValue).length !== 0) {
    $(SelectorAction).click(function () {
        let value = $(SelectorValue).val();
        if (value.length === 0) {
            return null;
        }
        $.ajax({
            url: pathToGenerator,
            cache: false,
            type: 'POST',
            dataType: "json",
            data: {value: value, rnd: Math.floor(Math.random() * 10000)}
        }).statusCode({
            200: function (data) {
                $(SelectorField).val(data.slug);
            }
        });
    })
}

