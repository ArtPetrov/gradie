import Sortable from 'sortablejs'

export default class Recommend {

    constructor(conatiner) {

        this.conatiner = conatiner;
        this.template = conatiner.data('prototype').replace(/id="form_recommended.*?"/g, '');
        this.products = [];
        this.loadCurrentProducts();
        this.sortable = Sortable.create(this.conatiner[0], {
            handle: '.move-recommended',
            draggable: 'li',
            animation: 150,
            onEnd: () => this.loadCurrentProducts(),
        });

        this.conatiner.on('click', '.remove-recommended', (event) => {
            this.remove(event.currentTarget);
        });

    }

    loadCurrentProducts() {
        this.products = [];
        this.conatiner.find('li').each((index, value) => {
             let id = $(value).find('input[name$="[id]"]').val();
             let name = $(value).find('input[name$="[name]"]').val();
             this.products.push({id: id, name: name});
        });
        this.render();
    }

    adding(addingProduct) {

        if (undefined !== this.products.find(product => Number(product.id) === Number(addingProduct.id))) {
            return null;
        }
        this.loadCurrentProducts();
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
            product.find('input[name="form[recommended][' + index + '][name]"]').attr('value', this.products[index].name);
            product.find('input[name="form[recommended][' + index + '][id]"]').val(this.products[index].id);
            return product.html();
        });
        this.conatiner.html(products.join(''));
    }

}