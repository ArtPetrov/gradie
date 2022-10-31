export default class ProductList {

    constructor(container, selectorElement, countStart, countLoading, button, filters, sortable, urlize) {
        this.urlize = urlize;
        this.button = button;
        this.filters = filters;
        this.sortable = sortable;
        this.container = container;
        this.countLoading = countLoading;
        this.path = container.data('path');
        this.properties = container.data('properties').split(',');
        this.selectorElement = selectorElement;
        this.currentCount = container.find(selectorElement).length;
        this.worked = this.currentCount === countStart;
        if (!this.worked) {
            this.button.hide();
        }
        this.loadingScroll = false;

        this.button.click(() => this.clickPatch());

        $(window).scroll((e) => {
            if (this.loadingScroll || !this.worked) return;
            if ($(e.currentTarget).scrollTop() > this.button.offset().top - $(window).height()) {
                this.loadingScroll = true;
                this.button.hide();
                this.loadSource();
            }
        });

        window.addEventListener('update-content', () => this.updateQuery())
    }

    clickPatch() {
        if (window.loadData) {
            return null;
        }
        this.loadSource();
    }

    updateQuery() {
        this.container.html('');
        this.worked = true;
        this.loadSource();
    }

    count() {
        return this.container.find(this.selectorElement).length;
    }

    loadSource() {
        $('.search-none').hide();
        if (!this.worked) {
            return null;
        }
        this.button.show();

        this.urlize
            .updateSorting(this.sortable.getSort())
            .updateFilters(this.filters.getParams())
            .changeUrl();

        $.ajax({
            url: this.path,
            type: "POST",
            dataType: "json",
            data: {
                "offset": this.count(),
                "category": this.container.data('category'),
                "sort": this.sortable.getSort(),
                "filters": this.filters.getParams(),
            }
        }).then(data => {
            if (data.length <= this.countLoading) {
                this.worked = false;
                this.button.hide();
            } else {
                this.button.show();
                data.pop();
            }
            data.forEach((element) => this.add(element));
            window.loadData = false;
            this.loadingScroll = false;
            if (0 === this.count()) {
                $('.search-none').show();
            }

        });
    }

    add(data) {
        this.container.append(this.template(data));
    }

    template(data) {
        let template = this.container.data('template');
        if (data.hasOwnProperty('cover')) {
            data.cover = data.cover === null ?
                this.container.data('nocover')
                : this.container.data('cover').replace(/__cover__/g, data.cover);
        }
        if (data.hasOwnProperty('old_cost')) {
            data.cost = Number(data.old_cost) > 0 ?
                this.container.data('old-cost').replace(/__old_cost__/g, data.old_cost).replace(/__cost__/g, data.cost)
                : this.container.data('cost').replace(/__cost__/g, data.cost)
        }
        this.properties.forEach((attr) => {
            if (data.hasOwnProperty(attr)) {
                const pattern = new RegExp('__' + attr + '__', "g");
                template = template.replace(pattern, data[attr]);
            }
        });
        return template;
    }
}