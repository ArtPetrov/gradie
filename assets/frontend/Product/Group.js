export default class Group {

    constructor(containerPc, containerMob, path, currentId, pathRedirect) {
        this.containerMob = containerMob;
        this.containerPc = containerPc;
        this.path = path;
        this.pathRedirect = pathRedirect;
        this.currentIdProduct = Number(currentId);
        this.productsForGroup = [];
        $('body').on('change', '.choose-selector', (e) => this.redirect($(e.currentTarget).val()));
        this.loadProducts();
    }

    redirect(id) {
        document.location.href = this.pathRedirect.replace(new RegExp('-1', "g"), id);
    }

    loadProducts() {
        $.ajax({
            url: this.path,
            type: "GET",
            dataType: "json",
            statusCode: {
                200: (e) => {
                    this.addData(e);
                }
            }
        })
    }

    addData(data) {
        this.selectors = [];
        for (let slug in data.selectors) {
            if (!data.selectors.hasOwnProperty(slug)) {
                continue;
            }
            let selector = data.selectors[slug];
            this.selectors[Number(selector.position)] = selector;
        }
        this.productsForGroup = Object.assign({}, data.products);
        this.handler();
    }

    createDtoProduct(selector, product) {
        return {
            checked: this.currentIdProduct === Number(product.id),
            product: product,
            selector: selector,
        }
    }

    handler() {
        let products = Object.assign({}, this.productsForGroup);
        let actualSelectors = [];
        let currentProduct = Object.assign({id: Number(this.currentIdProduct)}, products[this.currentIdProduct]);
        let countSelectors = this.selectors.length;
        this.selectors.reverse().forEach((selector, index, all) => {
            let parent = index < countSelectors - 1 ? all[index + 1] : null;

            for (let id in products) {
                let product = Object.assign({id: Number(id)}, products[id]);
                let productSupportParentSelector = false;
                let currentSupportSelector = currentProduct.hasOwnProperty(selector.slug);
                if (parent) {
                    let productSupportParentSelector = product.hasOwnProperty(parent.slug) && currentProduct[parent.slug] === product[parent.slug];
                    if (productSupportParentSelector && currentSupportSelector) {
                        actualSelectors[index] = typeof actualSelectors[index] === 'undefined' ? [] : actualSelectors[index];
                        actualSelectors[index].push(this.createDtoProduct(selector, product));
                    }
                } else {
                    if (currentSupportSelector) {
                        actualSelectors[index] = typeof actualSelectors[index] === 'undefined' ? [] : actualSelectors[index];
                        actualSelectors[index].push(this.createDtoProduct(selector, product));
                    }
                }

                let productHasActualSelector = product.hasOwnProperty(selector.slug) && currentProduct[selector.slug] === product[selector.slug];

                if ((!productHasActualSelector && !productSupportParentSelector) || !currentSupportSelector) {
                    delete products[id];
                }
            }
        });

        this.render(actualSelectors.reverse());
    }

    getSelectorFromGroup(group) {
        return group[0].selector;
    }

    render(groups) {
        this.containerMob.html('');
        this.containerPc.html('');
        groups.forEach((group, index, all) => {
            let selector = this.getSelectorFromGroup(group);
            let groupWithSort = [];
            if (selector!==undefined && selector.values.length > 0) {
                selector.values.forEach(attr => {
                    let findForSort = group.find(child => child.product[selector.slug] === attr.label);
                    if (findForSort !== undefined) {
                        groupWithSort.push(findForSort);
                    }
                })
            } else {
                groupWithSort = group;
            }
            // Fix bug...
            if (this.containerPc.is(':hidden')) {
                this.containerPc.append(this.templateForGroup(groupWithSort));
                this.containerMob.append(this.templateForGroup(groupWithSort)); // Rewrite for mobile RADIO
            }else{
                this.containerMob.append(this.templateForGroup(groupWithSort));
                this.containerPc.append(this.templateForGroup(groupWithSort)); // Rewrite for pc RADIO
            }


        });
    }

    templateForGroup(group) {
        let selector = this.getSelectorFromGroup(group);
        if (selector.type === "RADIO") {
            return this.templateForRadioGroup(group, selector);
        }
        if (selector.type === "SELECT") {
            return this.templateForSelectGroup(group, selector);
        }
    }

    templateForSelectGroup(group, selector) {
        let options = '';
        group.forEach((info) => {
            if (info === undefined || info.product[info.selector.slug]===undefined) {
                return;
            }
            options += `<option value="${info.product.id}" ${info.checked ? 'selected' : ''}>${info.product[info.selector.slug]}</option>`
        });
        return `<div class="product__row">
    <label class="product__label" for="size">${selector.name}</label>
    <select class="select choose-selector product__select">${options}</select>
</div>`;
    }

    templateForRadioGroup(group, selector) {
        let radios = '';
        group.forEach((info) => {
            if (info === undefined || info.product[info.selector.slug]===undefined) {
                return;
            }
            radios += `<label class="radio product__option">
    <input class="radio__input choose-selector visually-hidden" type="radio" value="${info.product.id}" name="${info.selector.slug}" ${info.checked ? 'checked' : ''}/>
    <span class="radio__indicator"></span>${info.product[info.selector.slug]}</label>`
        });

        return `<div class="product__row">
    <label class="product__label">${selector.name}</label>
    <div class="product__options">${radios}</div>
</div>`;
    }
}