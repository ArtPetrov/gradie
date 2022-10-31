$('.ordering__payment-item').click((e) => {
    let currentValue = $(e.currentTarget).data('type');
    $('select.payment-type option:selected').removeAttr('selected');
    $('select.payment-type' + ' option[value="' + currentValue + '"]').attr('selected', 'selected');
});