export default class Basket {

    constructor(table, server) {
        this.table = table;
        this.template = table.data('prototype');
        this.products = [];
        this.server = server;

        this.readTable();

        this.table.on('click', '.remove-product', (event) => {
            this.remove(event.currentTarget);
        });
        this.table.on('change', 'input.product-count', (event) => {
            this.updateCount(event.currentTarget);
        });
    }

    readTable() {
        this.table.find('tr.product-table').each((index, element) => {
            this.products[this.getId(element)] = this.getInformation(element);
        });
        this.renderTotalSum();
    }

    updateCount(target) {
        const id = this.getId(target);
        const count = Math.round(Number($(target).val()));
        if (count < 1 || isNaN(count)) {
            return this.remove(target);
        }
        $(target).val(count);

        const price = Number($(target).closest('tr').find('.price').html()
            .replace(/ /g, "")
            .replace(/&nbsp;/g, ""));

        $(target).closest('tr').find('.total-price').html(this.renderMoney(price * count, 2));

        if (this.products[id].count !== count) {
            this.products[id].count = count;
            this.server.updateCountProduct(id, this.products[id].count);
            this.renderTotalSum();
        }
    }

    adding(product) {
        if (this.products[Number(product.id)] !== undefined) {
            this.products[Number(product.id)].count++;
            this.server.updateCountProduct(product.id, this.products[Number(product.id)].count);
            this.table.find('[data-id="' + product.id + '"]').find('.product-count').val(this.products[Number(product.id)].count);
            this.table.find('[data-id="' + product.id + '"]').find('.total-price').html(this.renderMoney(this.products[Number(product.id)].price * this.products[Number(product.id)].count, 2));

            return this;
        }
        const dto = {};
        dto.id = product.id;
        dto.article = product.article;
        dto.name = product.name;
        dto.count = 1;
        dto.price = Number(product.cost);
        this.products[Number(product.id)] = dto;
        this.renderProduct(dto);
        this.server.addProduct(Number(product.id));
        this.renderTotalSum();
    }

    getId(target) {
        return Number($(target).closest('tr').find('td:first').find('a:first').html());
    }

    getInformation(target) {
        const info = {};
        info.id = Number($(target).closest('tr').find('td:first').find('a:first').html());
        info.article = $(target).closest('tr').find('td:eq(1)').find('b:first').html();
        info.name = $(target).closest('tr').find('td:eq(2)').html();
        info.count = Number($(target).closest('tr').find('input.product-count').val());
        info.price = Number($(target).closest('tr').find('.price').html().replace(/ /g, "").replace(/&nbsp;/g, ""));
        return info;
    }

    remove(target) {
        const id = this.getId(target);
        $(target).closest('tr').remove();
        delete this.products[id];
        this.server.removeProduct(id);
        this.renderTotalSum();
    }

    renderProduct(product) {
        this.table.append(this.getHtml(product));
    }

    getHtml(data) {
        let template = this.template;
        template = template.replace(new RegExp('__id__', "g"), data.id);
        template = template.replace(new RegExp('-100', "g"), data.id);
        template = template.replace(new RegExp('__count__', "g"), data.count);
        template = template.replace(new RegExp('__price__', "g"), this.renderMoney(data.price, 2));
        template = template.replace(new RegExp('__total__', "g"), this.renderMoney(Number(data.price) * Number(data.count), 2));
        template = template.replace(new RegExp('__name__', "g"), data.name);
        template = template.replace(new RegExp('__article__', "g"), data.article);
        return template;
    }

    renderMoney(number, FractionDigits = 0) {
        return (number).toLocaleString('ru-RU', {
            style: 'decimal',
            currency: 'RUB',
            minimumFractionDigits: FractionDigits,
            maximumFractionDigits: FractionDigits,
        }).replace(',', '.')
    }

    renderTotalSum() {
        const totalSum = this.products.reduce((sum, current) => sum + current.count * current.price, 0);
        if(totalSum>0){
            $(".totalPriceRow").show();
            $(".totalPriceSum").html(this.renderMoney(totalSum, 2))
        }else{
            $(".totalPriceRow").hide();
        }
    }
}