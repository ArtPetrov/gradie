import Sortable from 'sortablejs'

export default class Images {

    constructor(images) {

        this.container = images;
        this.template = images.data('prototype').replace(/id="form_images.*?"/g, '');

        this.images = [];

        this.loadCurrentImages();

        this.sortable = Sortable.create(this.container[0], {
            handle: 'div.image',
            draggable: 'div.image',
            animation: 150,
            onEnd: () => this.loadCurrentImages(),
        });

        this.container.on('click', '.remove-image', (event) => {
            $(event.currentTarget).closest('div.image').remove();
            this.loadCurrentImages();
        });

        this.container.on('change', 'input[name$="[cover]"]', (event) => {
            const id = Number($(event.currentTarget).closest('div.image').find('input[name$="[id]"]').val());
            this.images = this.images.map((element) => {
                element.cover = Number(element.id) === id;
                return element;
            });
            this.render();
        });

    }

    loadCurrentImages() {
        this.images = [];
        this.container.find('div.image').each((index, value) => {
            let property = $(value);
            let image = {};
            image.id = property.find('input[name$="[id]"]').val();
            image.src = property.find('input[name$="[src]"]').val();
            image.cover = Boolean(property.find('input[name$="[cover]"]').prop('checked'));
            this.add(image);
        });
        this.render();
    }

    add(data) {
        this.images.push(data);
    }

    addWithRender(data) {
        this.add(data);
        this.render();
    }

    render() {
        const images = Object.keys(this.images).map((index) => {
            let image = $('<div/>').append(this.template.replace(/__name__/g, index).replace(/__src__/g, this.images[index].src));
            image.find('input[name="form[images][' + index + '][id]"]').val(this.images[index].id);
            image.find('input[name="form[images][' + index + '][src]"]').attr('value', this.images[index].src);
            image.find('input[name="form[images][' + index + '][cover]"]').attr('checked', Boolean(this.images[index].cover));
            return image.html();
        });
        this.container.html(images.join(''));
    }
}
