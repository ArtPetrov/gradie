import Sortable from 'sortablejs'

export default class Filters {
    constructor(attributes, filters) {

        this.containerFilters = filters;
        this.tplFilters = filters.data('prototype').replace(/id="form_filters.*?"/g, '');
        this.filters = [];

        this.currentFilters = [];

        this.attributes = attributes;
        this.tplCheckbox = attributes.data('checkbox');
        this.sourcePath = attributes.data('path');

        this.loadSource();

        this.sortable = Sortable.create(this.containerFilters[0], {
            handle: '.move-filter',
            draggable: 'li',
            animation: 150,
            onEnd: () => this.loadCurrentFilters(),
        });

        this.attributes.on('change', '.select-attribute', (event) => {
            this.loadCurrentFilters();
            if ($(event.currentTarget).is(':checked')) {
                this.addFilter($(event.currentTarget).data('slug'));
            } else {
                this.removeFilter($(event.currentTarget).data('slug'));
            }
            this.loadCurrentFilters();
        });

        this.containerFilters.on('click', '.remove-filter', (event) => {
            let slug = $(event.currentTarget).closest('li').find('input[name$="[slug]"]').val();
            this.attributes.find('[data-slug="' + slug + '"]').trigger('click');
        });

    }

    getAttr(slug) {
        return this.filters[slug];
    }

    loadSource() {
        $.ajax({
            url: this.sourcePath,
        }).then(data => {
            data.forEach((attr) => {
                this.filters[attr['slug']] = attr;
            });
            this.renderAttributes();
            this.loadCurrentFilters();
        });
    }

    loadCurrentFilters() {
        let firstLoad = this.currentFilters.length === 0;
        this.currentFilters = [];
        this.containerFilters.find('li').each((index, value) => {
            let element = $('<div/>').append(value);

            let slug = element.find('input[name$="[slug]"]').val();
            let label = element.find('input[name$="[label]"]').val();

            let filter = this.getAttr(slug);
            filter.label = label;

            if(firstLoad){
                this.attributes.find('[data-slug="' + slug + '"]').attr('checked','checked');
            }
            this.addFilterPayLoad(filter);
        });
    }

    addFilter(slug) {
        this.addFilterPayLoad(this.getAttr(slug));
    }

    addFilterPayLoad(slug) {
        this.currentFilters.push(slug);
        this.renderFilters();
    }

    removeFilter(slug) {
        this.currentFilters = Object.assign(this.currentFilters.filter((attr) => attr.slug !== slug));
        this.renderFilters();
    }

    renderFilters() {
        const filters = Object.keys(this.currentFilters).map((index) => {
            let filter = $('<div/>').append(this.tplFilters.replace(/__name__/g, index));
            filter.find('input[name="form[filters]['+index+'][slug]"]').val(this.currentFilters[index].slug);
            filter.find('input[name="form[filters]['+index+'][type]"]').val(this.currentFilters[index].field_type);
            filter.find('input.name-attribute').attr('value', this.currentFilters[index].name);
            filter.find('input.name-type-field').attr('value', this.currentFilters[index].field_name);
            if (this.currentFilters[index].hasOwnProperty('label')) {
                filter.find('input[name="form[filters]['+index+'][label]"]').attr('value', this.currentFilters[index].label);
            }
            return filter.html();
        });
        this.containerFilters.html(filters.join(''));
    }

    renderAttributes() {
        const itemsHtml = Object.keys(this.filters).map((slug) => {
            return this.tplCheckbox
                .replace(/__name__/g, this.filters[slug].name)
                .replace(/__slug__/g, slug);
        });

        this.attributes.html(itemsHtml.join(''));
    }
}
