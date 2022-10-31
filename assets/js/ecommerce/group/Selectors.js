import Sortable from 'sortablejs'

export default class Selectors {

    constructor(attributes, collection) {
        this.container = attributes;
        this.template = attributes.data('prototype').replace(/id="form_selectors.*?"/g, '');

        this.attributes = [];
        this.currentAttributes = [];

        this.property = collection;
        this.templateProperty = collection.data('checkbox');
        this.sourceCollection = collection.data('path');

        this.loadSource();

        this.sortable = Sortable.create(this.container[0], {
            handle: '.move-attribute',
            draggable: 'li',
            animation: 150,
            onEnd: () => this.loadCurrentAttributes(),
        });

        this.property.on('change', '.select-attribute', (event) => {
            this.loadCurrentAttributes();
            if ($(event.currentTarget).is(':checked')) {
                this.addAttribute($(event.currentTarget).data('slug'));
            } else {
                this.removeAttribute($(event.currentTarget).data('slug'));
            }
            this.loadCurrentAttributes();
        });

        this.container.on('click', '.remove-attribute', (event) => {
            let slug = $(event.currentTarget).closest('li').find('input[name$="[slug]"]').val();
            this.property.find('[data-slug="' + slug + '"]').trigger('click');
        });
    }

    getAttr(slug) {
        return this.attributes[slug];
    }

    loadSource() {
        $.ajax({
            url: this.sourceCollection,
        }).then(data => {
            data.forEach((attr) => {
                this.attributes[attr['slug']] = attr;
                this.attributes[attr['slug']].title = attr['name'];
                this.attributes[attr['slug']].type = 'SELECT';
            });
            this.renderCollection();
            this.loadCurrentAttributes();
        });
    }

    loadCurrentAttributes() {
        let firstLoad = this.currentAttributes.length === 0;
        this.currentAttributes = [];
        this.container.find('li').each((index, value) => {
            let property = $('<div/>').append(value);
            let slug = property.find('input[name$="[slug]"]').val();

            let attribute = this.getAttr(slug);
            attribute.name = property.find('input[name$="[name]"]').val();
            attribute.title = property.find('input[name$="[title]"]').val();
            attribute.type = property.find('select[name$="[type]"]').val();

            if (firstLoad) {
                this.property.find('[data-slug="' + slug + '"]').attr('checked', 'checked');
            }

            this.addAttributePayLoad(attribute);
        });
    }

    addAttribute(slug) {
        this.addAttributePayLoad(this.getAttr(slug));
    }

    addAttributePayLoad(slug) {
        this.currentAttributes.push(slug);
        this.renderAttributes();
    }

    removeAttribute(slug) {
        this.currentAttributes = Object.assign(this.currentAttributes.filter((attr) => attr.slug !== slug));
        this.renderAttributes();
    }

    renderAttributes() {
        const attributes = Object.keys(this.currentAttributes).map((index) => {
            let attr = $('<div/>').append(this.template.replace(/__name__/g, index));
            const type = this.currentAttributes[index].type;
            const slug = this.currentAttributes[index].slug;
            attr.find('input[name="form[selectors][' + index + '][slug]"]').val(slug);
            attr.find('select[name="form[selectors][' + index + '][type]"] option[value="' + type + '"]').attr('selected', 'selected');
            attr.find('input[name="form[selectors][' + index + '][name]"]').val(this.currentAttributes[index].name);
            attr.find('input.name-attribute').attr('value', this.currentAttributes[index].name);
            if (this.currentAttributes[index].hasOwnProperty('title')) {
                attr.find('input[name="form[selectors][' + index + '][title]"]').attr('value', this.currentAttributes[index].title);
            }
            return attr.html();
        });
        this.container.html(attributes.join(''));
    }

    renderCollection() {
        const itemsHtml = Object.keys(this.attributes).map((slug) => {
            return this.templateProperty
                .replace(/__name__/g, this.attributes[slug].name)
                .replace(/__slug__/g, slug);
        });
        this.property.html(itemsHtml.join(''));
    }
}
