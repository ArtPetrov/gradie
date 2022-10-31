export default class BasketPopupRender {

    constructor(eventBus, basket) {
        this.bus = eventBus;
        this.container = $('.basket-table-items');
        this.basket = basket;
        this.bus.addEventListener('update-basket', () => this.render())
    }

    template(data) {
        let template = this.container.data('template');
        if (data.hasOwnProperty('cover')) {
            data.cover = data.cover === null ? 'https://imgholder.ru/48/EEE/' : data.cover;
        } else {
            data.cover = 'https://imgholder.ru/48/EEE/';
        }
        template = template.replace(new RegExp('__link__', "g"), data.link);
        template = template.replace(new RegExp('__count__', "g"), data.count);
        template = template.replace(new RegExp('__price__', "g"), this.basket.renderMoney(data.price));
        template = template.replace(new RegExp('__cover__', "g"), data.cover);
        template = template.replace(new RegExp('__name__', "g"), data.name);
        return template;
    }

    render() {
        const total = this.basket.getCountProducts();

        if (0 < total) {
            $('.basket-has-items').show();
            $('.basket-none-items').hide();
        } else {
            $('.basket-has-items').hide();
            $('.basket-none-items').show();
        }

        $('span.basket-count-items').html(total);
        $('span.basket-total-sum').html(this.basket.renderMoney(this.basket.getTotalPrice(),2));

        this.container.html(
            this.basket.getProducts().reduce(
                (content, product) => content + this.template(product)
                , '')
        )
    }
}