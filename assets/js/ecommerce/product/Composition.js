import Sortable from 'sortablejs'

export default class Composition {

    constructor(conatiner) {
        this.conatiner = conatiner;
        this.template = conatiner.data('prototype').replace(/id="form_composition.*?"/g, '');
        this.products = [];
        this.loadCurrentProducts();
        this.sortable = Sortable.create(this.conatiner[0], {
            handle: '.move-composition',
            draggable: 'li',
            animation: 150,
            onEnd: () => this.loadCurrentProducts(),
        });
        this.conatiner.on('click', '.remove-composition', (event) => {
            this.remove(event.currentTarget);
        });
        this.conatiner.on('change', 'input.count-composition', (event) => {
            this.parseData();
        });
    }

    parseData() {
        this.products = [];
        this.conatiner.find('li').each((index, value) => {
            let id = $(value).find('input[name$="[id]"]').val();
            let name = $(value).find('input[name$="[name]"]').val();
            let count = $(value).find('input[name$="[count]"]').val();
            this.products.push({id: id, name: name, count: count});
        });
    }

    loadCurrentProducts() {
        this.parseData();
        this.render();
    }

    adding(addingProduct) {
        if (undefined !== this.products.find(product => Number(product.id) === Number(addingProduct.id))) {
            return null;
        }
        this.loadCurrentProducts();
        addingProduct.count = 1;
        this.products.push(addingProduct);
        this.render();
    }

    remove(target) {
        const id = Number($(target).closest('li').find('input[name$="[id]"]').val());
        $(target).closest('li').remove();
        this.products = this.products.filter(product => product.id !== id);
    }

    render() {
        const products = Object.keys(this.products).map((index) => {
            let product = $('<div/>').append(this.template.replace(/__name__/g, index));
            product.find('input[name="form[composition][' + index + '][name]"]').attr('value', this.products[index].name);
            product.find('input[name="form[composition][' + index + '][count]"]').attr('value', this.products[index].count);
            product.find('input[name="form[composition][' + index + '][id]"]').val(this.products[index].id);
            return product.html();
        });
        this.conatiner.html(products.join(''));
    }

}