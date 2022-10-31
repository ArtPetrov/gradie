export default class Files {
    constructor(filesLink, errorContainer) {

        this.errorContainer = errorContainer;
        this.container = filesLink;
        this.template = filesLink.data('prototype').replace(/id="form_files.*?"/g, '');

        this.files = [];

        this.loadCurrentFiles();

        this.container.on('click', '.remove-file', (event) => {
            $(event.currentTarget).closest('li').remove();
            this.loadCurrentFiles();
        });

    }

    loadCurrentFiles() {
        this.files = [];
        this.container.find('li').each((index, value) => {
            let property = $(value);
            let file = {};
            file.id = property.find('input[name$="[id]"]').val();
            this.add(file);
        });
        this.render();
    }

    add(data) {
        this.files.push(data);
    }

    count() {
        return this.files.length
    }

    addWithRender(data) {
        if (4 < this.count()) {
            this.errorContainer.text('Нельзя загружать более 5 файлов.');
            return null;
        }
        if (data.hasOwnProperty('id')) {
            this.add(data);
            this.errorContainer.text('');
            return this.render();
        }
        this.errorContainer.text('Ошибка загрузки файла. Можно загружать файлы до 5 мегабайт и офисных форматов.(картинки, документы)');
    }

    render() {
        const files = Object.keys(this.files).map((index) => {
            let file = $('<div/>').append(this.template.replace(/__name__/g, index).replace(/__src__/g, this.files[index].src));
            file.find('input[name="form[files][' + index + '][id]"]').val(this.files[index].id);
            return file.html();
        });
        this.container.html(files.join(''));
    }
}
