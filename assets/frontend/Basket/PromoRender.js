export default class BasketRender {

    constructor(eventBus, basket) {
        this.bus = eventBus;
        this.basket = basket;
        this.promocode = $('input[name="promocode"]');
        this.bus.addEventListener('update-basket', () => this.beforeLoad());
        $('body').on('click', '.promocode-send', (e) => this.beforeLoad());
        this.loadData();
    }

    beforeLoad() {
        if (this.promocode.val().length > 0) {
            this.loadData();
        }
    }

    clearPromocodeInfo() {
        $('.promocode-info').hide();
        $('.promocode-message').text('');
        this.promocode.text('');
    }

    renderPromocodeInfo(code, discount) {
        $('.promocode-info').show();
        $('.promocode-discount').text(this.basket.renderMoney(discount, 2));
        $('.promocode-total-price').text(this.basket.renderMoney(this.basket.getTotalPrice(Number(discount)), 2));
        $('.promocode-message').text('Промокод применен!');
        this.promocode.val(code);
    }

    errorPromocode(code, message) {
        $('.promocode-info').hide();
        this.promocode.val(code);
        $('.promocode-message').text(message);
    }

    loadData() {
        const data = {};
        if (this.promocode.val().length > 0) {
            data.promocode = this.promocode.val();
        }
        $.ajax({
            url: $('input[name="path-promocode"]').val(),
            type: "POST",
            dataType: "json",
            data: data,
            statusCode: {
                204: () => this.clearPromocodeInfo(),
                200: (e) => this.renderPromocodeInfo(e.promocode, e.discount),
                406: (e) => this.errorPromocode(e.responseJSON.promocode, e.responseJSON.message),
            }
        });
    }
}