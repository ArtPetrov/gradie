export default class BasketRender {

    constructor(eventBus, basket) {
        this.bus = eventBus;
        this.container = $('div.basket-items');
        this.basket = basket;
        this.bus.addEventListener('update-basket', () => this.render())
    }

    template(data) {
        let template = this.container.data('template');
        let price = this.container.data('template-price');

        if (data.hasOwnProperty('cover')) {
            data.cover = data.cover === null ? 'https://imgholder.ru/90/EEE/' : data.cover;
        } else {
            data.cover = 'https://imgholder.ru/90/EEE/';
        }

        if (data.hasOwnProperty('old_price') && Number(data.old_price) > 0) {
            price = this.container.data('template-price-old');
            price = price.replace(new RegExp('__cost-old__', "g"), this.basket.renderMoney(data.old_price));
        }

        price = price.replace(new RegExp('__cost__', "g"), this.basket.renderMoney(data.price,0));
        price = price.replace(new RegExp('__cost-total__', "g"), this.basket.renderMoney(Number(data.price) * Number(data.count)));

        template = template.replace(new RegExp('__id__', "g"), data.id);
        template = template.replace(new RegExp('__link__', "g"), data.link);
        template = template.replace(new RegExp('__count__', "g"), data.count);
        template = template.replace(new RegExp('__price__', "g"), price);
        template = template.replace(new RegExp('__cover__', "g"), data.cover);
        template = template.replace(new RegExp('__name__', "g"), data.name);

        return template;
    }

    render() {
        const total = this.basket.getCountProducts();

        if (0 < total) {
            $('.basket-content').show();
            $('.basket-empty').hide();
        } else {
            $('.basket-content').hide();
            $('.basket-empty').show();
        }

        $('p.total-price').html(this.basket.renderMoney(this.basket.getTotalPrice(),2));

        this.container.html(
            this.basket.getProducts().reduce(
                (content, product) => content + this.template(product)
                , '')
        )
    }
}