import Basket from "./Basket/BasketStorage";
import BasketPopupRender from "./Basket/BasketPopupRender";
import Noty from "noty";

require('../css/noty.scss');

require('./FacebookPixel.js');
require('./Search/Main.js');
require('./VarDumper.js');

$('.help-open').click((e) => $(".help-popup[data-popup='" + $(e.currentTarget).data('popup') + "']").fadeIn(500));

$('div.close-help').click((e) => {
    let popup = $(e.currentTarget).find('div.modal__container');
    if (!popup.is(e.target) && 0 === popup.has(e.target).length) {
        $(e.currentTarget).closest('div.modal').fadeOut(300);
    }
});

$('button.close-help, a.close-help').click((e) => {
    $(e.currentTarget).closest('div.modal').fadeOut(300);
});

if (!window.hasOwnProperty('basket')) {
    window.basket = new Basket($('#path-basket').val(), window);
}

new BasketPopupRender(window, window.basket);

$('body').on('click', '.basket-adding', (e) => {
    let product = {};
    product.id = $(e.currentTarget).data('id');
    product.count = Number($(e.currentTarget).closest('div').find('.basket-add-count').val());
    if (isNaN(product.count)) {
        product.count = 1;
    }
    if (product.count === 0) {
        return null;
    }

    basket.add(product);

    new Noty({
        type: 'success',
        theme: 'metroui',
        timeout: '3500',
        closeWith: ['click'],
        progressBar: false,
        killer: true,
        layout: 'bottomRight',
        text: '<a href="/basket" style="color: white"><div>Товар добавлен в корзину!<br> Перейти в корзину</div></a>'
    }).show();
});

function getRandomInt(min, max) {
    return min + Math.floor(Math.random() * Math.floor(max));
}

if ($("#form_frod_result").length > 0) {
    $("#form_frod_a").val(getRandomInt(1, 100));
    $("#form_frod_b").val(getRandomInt(1, 100));
    $("#form_frod_result").val($("#form_frod_a").val() * $("#form_frod_b").val())
}
