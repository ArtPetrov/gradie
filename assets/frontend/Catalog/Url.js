export default class Url {

    constructor(defaultParams) {
        this.sort = '';
        this.filters = '';
        this.style = '';
        this.defaultParams = defaultParams;
        this.baseUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        this.readRequestStyleVisible();
    }

    searchParams() {
        return new URLSearchParams(window.location.search);
    }

    generateParams() {
        const params = [];
        if (0 < this.sort.length) {
            params.push(this.sort)
        }
        if (0 < this.style.length) {
            params.push(this.style)
        }
        if (0 < this.filters.length) {
            params.push(this.filters)
        }
        return params.join('&');
    }

    changeUrl() {
        let params = this.generateParams();
        params = 0 === params.length ? '' : '?' + params;
        history.pushState(null, null, this.baseUrl + params);
    }

    updateFilters(filters) {
        this.filters = filters.map(
            filter => filter.slug + '_' + filter.type + '_' + this.filterGenerateQueryValue(filter)
        ).join('%2C');

        if (0 !== this.filters.length) {
            this.filters = 'filters=' + this.filters;
        }

        return this;
    }

    parseFilters() {
        if (this.searchParams().has('filters')) {
            this.filters = 'filters=' + this.searchParams().get('filters')
                .replace(';', '%3B')
                .replace(',', '%2C')
            ;
            return this.searchParams().get('filters').split(',').map(filter => this.filterReadQueryValue(filter));
        }
        return []
    }

    filterGenerateQueryValue(filter) {
        switch (filter.type) {
            case 'NUMBER':
                return filter.from + '%3B' + filter.to;
            case 'SELECT':
                return filter.value;
        }
        return '';
    }

    filterReadQueryValue(query) {
        let params = query.split('_');
        let filter = {};
        filter.slug = params[0];
        filter.type = params[1];

        switch (filter.type) {
            case 'NUMBER':
                const range = params[2].split(';');
                filter.from = Number(range[0]);
                filter.to = Number(range[1]);
                break;

            case 'SELECT':
                filter.value = params[2];
                break;
        }
        return filter;
    }

    updateStyleVisible(style) {
        if (this.defaultParams.style === style) {
            this.style = '';
            return this;
        }
        this.style = 'style=' + style;
        return this;
    }

    readRequestStyleVisible() {
        if (this.searchParams().has('style')) {
            this.style = 'style=' + this.searchParams().get('style');
        }
    }

    updateSorting(sort) {
        if (this.defaultParams.sorting.slug === sort.slug && this.defaultParams.sorting.order === sort.order) {
            this.sort = '';
            return this;
        }
        this.sort = 'sorting=' + sort.slug + '_' + sort.order;
        return this;
    }

    currentSorting() {
        if (!this.searchParams().has('sorting')) {
            return this.defaultParams.sorting;
        }
        const sorting = this.searchParams().get('sorting').split('_');
        this.sort = 'sorting=' + sorting[0] + '_' + sorting[1];
        return {'slug': sorting[0], 'order': sorting[1]};
    }
}
