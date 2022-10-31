export default class Sort {

    constructor(container, urlizer) {
        this.urlizer = urlizer;
        this.container = container;
        this.sort = [];
        this.container.on('click', '.sort__option', (e) => this.resort($(e.currentTarget)));
    }

    resort(sort) {
        if (window.loadData) {
            return null;
        }
        this.sort = this.sort.map(option => {
            const flagCurrent = option.slug === sort.data('slug');
            if (option.active && flagCurrent) {
                option.order = option.order === 'DESC' ? 'ASC' : 'DESC';
            }
            option.active = flagCurrent;
            return option;
        });
        this.render();
        window.loadData = true;
        window.dispatchEvent(new CustomEvent("update-content"));
    }

    addSort(sort) {
        const currentSorting = this.urlizer.currentSorting();
        console.log()
        if (sort.slug === currentSorting.slug) {
            sort.order = currentSorting.order;
            sort.active = true;
        }
        this.sort.push(sort);
        this.render();
    }

    getSort() {
        return this.sort.filter(e => true === e.active)[0];
    }

    template(sort) {
        let sortOption = ' sort__option--up';
        let sortActive = '';
        if ('DESC' === sort.order) {
            sortOption = ' sort__option--down';
        }
        if (true === sort.active) {
            sortActive = ' sort__option--active';
        }
        return `<a class="sort__option${sortOption}${sortActive}" data-slug="${sort.slug}"  data-type="${sort.order}" href="javascript:void(0)">${sort.label}</a>`
    }

    render() {
        let sort = this.sort.map((sort) => {
            return this.template(sort);
        });
        this.container.html('<p class="sort__caption">Сортировка:</p>' + sort.join(''));
    }
}