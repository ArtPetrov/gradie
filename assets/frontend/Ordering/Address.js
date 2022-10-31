$('.choices-type-shipping').click((e) => {
    let currentValue = $(e.currentTarget).data('city');

    if (currentValue === 'regions') {
        $('.region-note').show();
    } else {
        $('.region-note').hide();
    }

    $('select.choise-type option:selected').removeAttr('selected');
    $('select.choise-type' + ' option[value="' + currentValue + '"]').attr('selected', 'selected');
    updatePrice();
});

$('input[name="form[moscow][deliveryTo]"]').click((e) => {
    updatePrice();
});

$('input[name="form[moscow][howManyKm]"]').change((e) => {
    let positiveNumber = Math.abs($(e.currentTarget).val());
    $(e.currentTarget).val(positiveNumber);
    $('input[name="form[moscow][deliveryTo]"][value="abroad"]').attr('checked', 'checked');
    updatePrice();
});

const totalPrice = Number($('.total-price').val());
const totalPriceWithPromo = Number($('.total-price-with-promo').val());

const shippingPrice = () => {
    const selectCity = $('select.choise-type option:selected').val();

    const baseCost = Number($('input[name="form[' + selectCity + '][baseCostShipping]"]').val());
    const currentCost = Number($('input[name="form[' + selectCity + '][freeShippingLimit]"]').val()) < totalPrice ? 0 : baseCost;

    if ($('input[name="form[moscow][deliveryTo]"]:checked').val() === 'abroad' && selectCity === 'moscow') {
        let priceKm = Number($('input[name="form[' + selectCity + '][costKmShipping]"]').val());
        let countKm = Number($('.count-km').val());
        let finish = currentCost + priceKm * countKm;
        $(".finish-shipped").html(finish);
        return finish;
    }
    return currentCost;
};

function updatePrice() {
    const price = shippingPrice();
    $('.shipping-cost').html(price);
    $('.total-price-with-shipping').html(totalPriceWithPromo + price);
}

updatePrice();