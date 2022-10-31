import Basket from "./BasketStorage";
import BasketPage from "./BasketRender";
import Promo from "./PromoRender";

if (!window.hasOwnProperty('basket')) {
    window.basket = new Basket($('#path-basket').val(), window);
}

new BasketPage(window, window.basket);
new Promo(window, window.basket);

$('div.basket-items').on('click', '.basket-remove', (e) => {
    const id = $(e.currentTarget).closest('div').find('input[name="product-id"]').val();
    $(e.currentTarget).closest('.cart-item').remove();
    basket.remove(id);
});

$('div.basket-items').on('change', '.basket-update-count', (e) => {
    const product = {};
    product.id = $(e.currentTarget).closest('div').find('input[name="product-id"]').val();
    product.count = $(e.currentTarget).val();
    if (Number(product.count) < 1) {
        product.count = 1;
    }
    basket.update(product);
});

