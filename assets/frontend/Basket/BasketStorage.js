export default class BasketStorage {

    constructor(path, busEvent) {
        this.products = [];
        this.path = path;
        this.loadProducts();
        this.bus = busEvent;
    }

    renderMoney(number, FractionDigits = 0) {
        return (number).toLocaleString('ru-RU', {
            style: 'currency',
            currency: 'RUB',
            minimumFractionDigits: FractionDigits,
            maximumFractionDigits: FractionDigits,
        })
    }

    getProducts() {
        return this.products;
    }

    getCountProducts() {
        return this.products.reduce((count, product) => count + Number(product.count), 0);
    }

    getTotalPrice(discount = 0) {
        const total = this.products.reduce((total, product) => total + Number(product.count) * Number(product.price), 0) - discount;
        return 0 > total ? 0 : total;
    }

    has(product) {
        return undefined !== this.products.find(
            (item, index, products) => Number(item.id) === Number(product.id)
        );
    }

    add(product) {

        if (this.has(product)) {
            return this.addingUpdate(product);
        }

        $.ajax({
            url: this.path,
            type: "POST",
            dataType: "json",
            data: product
        }).then(data => {
            if (this.has(data.item)) {
                this.updateCountForItem(data.item);
            } else {
                this.products.push(data.item);
            }
            this.bus.dispatchEvent(new CustomEvent("update-basket"));

        });
    }

    updateCountForItem(product) {
        this.products = this.products.map(
            (item, index, products) => {
                if (Number(product.id) === Number(item.id)) {
                    item.count = product.count = Number(item.count) + Number(product.count);
                }
                return item;
            }
        );
    }

    addingUpdate(product) {
        this.updateCountForItem(product);
        this.hookUpdate(product);
    }

    update(product) {
        this.products = this.products.map(
            (item, index, products) => {
                if (Number(product.id) === Number(item.id)) {
                    item.count = Number(product.count);
                }
                return item;
            }
        );
        this.hookUpdate(product);
    }

    hookUpdate(product) {
        $.ajax({
            url: this.path,
            type: "PUT",
            dataType: "json",
            data: product
        }).then(data => {
            this.bus.dispatchEvent(new CustomEvent("update-basket"));
        });
    }

    remove(id) {
        this.products = this.products.filter(
            product => Number(product.id) !== Number(id)
        );

        $.ajax({
            url: this.path,
            type: "DELETE",
            dataType: "json",
            data: {id: id},
        }).then(data => {
            this.bus.dispatchEvent(new CustomEvent("update-basket"));
        });
    }

    loadProducts() {
        $.ajax({
            url: this.path,
            type: "GET",
            dataType: "json"
        }).then(data => {
            this.products = data.items;
            if (this.getCountProducts() > 0) {
                this.bus.dispatchEvent(new CustomEvent("update-basket"));
            }
        });
    }
}