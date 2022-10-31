export default class Filters {

    constructor(pickedContainer) {
        this.container = pickedContainer;
        this.filters = [];
        $(".action-clear-filters").on('click', (e) => this.clear());
        $("div.picked-filter").on('click', '.action-clear-filters', (e) => this.clear());
        $("div.picked-filter").on('click', '.action-clear-current', (e) => this.clearCurrent($(e.currentTarget)));
    }

    getParams() {
        return this.filters;
    }

    insertParamsFromUrl(filters) {
        this.filters = filters.map((filter) => {
            const labelFilter = $('[data-type="' + filter.type + '"][data-slug="' + filter.slug + '"]').data('label');
            if ('SELECT' === filter.type) {
                filter.label = labelFilter + ': ' + $('[name="' + filter.slug + '"][value="' + filter.value + '"]').data('label');
            }
            if ('NUMBER' === filter.type) {
                filter.label = labelFilter + ': ' + this.labelForNumber(filter.from, filter.to);
            }
            return filter;
        });
        this.renderPicked();
    }

    addNumberFilter(selector) {
        selector.change((e) => this.addNumberFiled($(e.currentTarget).closest('filedset')));
        window.addEventListener('update-slug', (e) => this.changeBySlugNumber(e.detail['slug']));
    }

    addSelectFilterColor(selector) {
        selector.change((e) => this.addSelectFiledColor($(e.currentTarget).closest('.filter__color'), $(e.currentTarget)));
    }

    addSelectFiledColor(filedset, option) {
        const slug = filedset.data('slug');
        const type = filedset.data('type');
        const label = filedset.data('label') + ': ' + option.data('label');

        this.filters = this.remove(slug, type);
        this.filters.push({slug: slug, type: type, value: option.val(), label: label});

        const derevo = $(filedset).closest('div.filter').find("div.filter__color[data-slug=\"tsvet-dereva\"]");
        const metall = $(filedset).closest('div.filter').find("div.filter__color[data-slug=\"tsvet-metalla\"]");

        if ('tsvet-metalla' === slug && derevo.length > 0) {
            derevo.find('input[value="bereza"]').prop('checked', false);
            derevo.find('input[value="venge"]').prop('checked', false);
            this.filters = this.remove('tsvet-dereva', type);
        }

        if ('tsvet-dereva' === slug && metall.length > 0) {
            if ('bereza' === option.val()) {
                metall.find('input[value="white"]').prop('checked', true);
            }
            if ('venge' === option.val()) {
                metall.find('input[value="grey"]').prop('checked', true);
            }
            this.filters = this.remove('tsvet-metalla', type);
            this.filters.push({
                slug: 'tsvet-metalla',
                type: type,
                value: metall.find('input:checked').val(),
                label: metall.data('label') + ': ' + metall.find('input:checked').data('label')
            });
        }
        this.render();
    }

    labelForNumber(from, to) {
        let label = '';
        if (0 < from) {
            label += ' от ' + from;
        }
        if (to > from) {
            label += ' до ' + to;
        }
        return label;
    }

    addNumberFiled(filedset) {
        const slug = filedset.data('slug');
        const type = filedset.data('type');
        const from = Number(filedset.find('input.range__from').val());
        const to = Number(filedset.find('input.range__to').val());
        let label = this.labelForNumber(from, to);
        if (label === '') {
            this.filters = this.remove(slug, type);
            return this.render();
        }
        label = filedset.data('label') + label;
        this.filters = this.remove(slug, type);
        this.filters.push({slug: slug, type: type, from: from, to: to, label: label});

        this.render();
    }

    remove(slug, type) {
        return this.filters = this.filters.filter((e) => !(e.slug === slug && type === e.type));
    }

    changeBySlugNumber(slug) {
        this.addNumberFiled($('[data-slug="' + slug + '"]'));
    }

    clear() {
        this.filters.forEach((filter) => {
            if ('SELECT' === filter.type) {
                this.unCheckedSelectColor(filter.slug);
            }
        });
        this.filters = [];
        this.render();
    }

    unCheckedSelectColor(slug) {
        $('input[name="' + slug + '"]').each((i, e) => {
            $(e).prop('checked', false);
        });
    }

    clearCurrent(button) {
        if ('SELECT' === button.data('type')) {
            this.unCheckedSelectColor(button.data('slug'));
        }
        this.remove(button.data('slug'), button.data('type'));
        this.render();
    }

    templateLabel(label, slug, type) {
        return `<button class="picked-filter__item action-clear-current" type="button" data-slug="${slug}"  data-type="${type}">${label}</button>`;
    }

    templateButtonClear() {
        return '<button class="picked-filter__clear  action-clear-filters" type="button">Очистить</button>';
    }

    renderPicked() {
        let filters = this.filters.map((f) => {
            return this.templateLabel(f.label, f.slug, f.type);
        });

        if (this.filters.length > 0) {
            filters.push(this.templateButtonClear());
        }

        this.container.html(filters.join(''));
    }

    render() {
        this.renderPicked();
        window.dispatchEvent(new CustomEvent("update-content"));
    }
}