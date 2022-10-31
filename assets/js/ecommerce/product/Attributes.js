import Sortable from 'sortablejs'

export default class Attributes {

    constructor(attributes, collection) {

        this.container = attributes;
        this.template = attributes.data('prototype').replace(/id="form_attributes.*?"/g, '');

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

        this.container.on('change', '.value-groups', (event) => {
            this.saveHideData();
            //this.loadCurrentAttributes();
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
                this.attributes[attr['slug']].label = attr['name'];
                this.attributes[attr['slug']].values = JSON.parse(this.attributes[attr['slug']].values);
            });
            this.renderCollection();
            this.loadCurrentAttributes();
        });
    }

    saveHideData() {
        this.container.find('li').each((index, value) => {
            let property = $(value);
            let type = property.find('input[name$="[type]"]').val();
            if (type === 'BOOL') {
                property.find('input[name$="[value]"]').val(String(property.find('.value-' + type).prop('checked')));
            } else {
                property.find('input[name$="[value]"]').val(String(property.find('.value-' + type).val()));
            }
        });
    }

    loadCurrentAttributes() {
        let firstLoad = this.currentAttributes.length === 0;
        this.currentAttributes = [];
        this.container.find('li').each((index, value) => {
            let property = $('<div/>').append(value);
            let slug = property.find('input[name$="[slug]"]').val();
            let attribute = this.getAttr(slug);

            attribute.label = property.find('input[name$="[label]"]').val();
            attribute.value = property.find('input[name$="[value]"]').val();
            attribute.field_type = property.find('input[name$="[type]"]').val();
            attribute.visible = property.find('input[name$="[visible]"]').prop('checked');
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
            const type = this.currentAttributes[index].field_type;
            const slug = this.currentAttributes[index].slug;
            attr.find('input[name="form[attributes][' + index + '][slug]"]').val(slug);
            attr.find('input[name="form[attributes][' + index + '][type]"]').val(type);
            attr.find('input[name="form[attributes][' + index + '][label]"]').val(this.currentAttributes[index].label);
            attr.find('input[name="form[attributes][' + index + '][value]"]').val(this.currentAttributes[index].value);
            attr.find('input[name$="[visible]"]').attr("checked", this.currentAttributes[index].visible);
            attr.find('input.name-type-field').attr('value', this.currentAttributes[index].field_name);
            attr.find('input.name-attribute').attr('value', this.currentAttributes[index].name);

            attr.find('.visible-' + type).removeClass('d-none');

            switch (type) {
                case 'BOOL':
                    if ('true' === this.currentAttributes[index].value || '1' === this.currentAttributes[index].value ) {
                        attr.find('.value-' + type).attr('checked', 'checked');
                    }
                    break;

                case 'SELECT':
                case 'CHECKBOX':
                    this.attributes[slug].values.forEach((el) => {
                        attr.find('.value-' + type).append($('<option>', {value: el.value}).text(el.label));
                    });

                    if (this.currentAttributes[index].value) {
                        attr.find('.value-' + type + ' option[value=' + this.currentAttributes[index].value + ']').attr('selected', 'selected');
                    }else{
                        this.currentAttributes[index].value = attr.find('.value-' + type + ' option:eq(0)').val();
                        attr.find('.value-' + type + ' option:eq(0)').attr('selected', 'selected');
                    }

                    break;

                default:
                    attr.find('.value-' + type).attr('value', this.currentAttributes[index].value);
                    break;
            }

            if (this.currentAttributes[index].hasOwnProperty('label')) {
                attr.find('input[name="form[attributes][' + index + '][label]"]').attr('value', this.currentAttributes[index].label);
            }
            if (this.currentAttributes[index].hasOwnProperty('value')) {
                attr.find('input[name="form[attributes][' + index + '][value]"]').attr('value', this.currentAttributes[index].value);
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
