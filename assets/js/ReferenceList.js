import Sortable from 'sortablejs'

export default class ReferenceList {
    constructor(currentElement) {
        this.element = currentElement;
        this.sortable = Sortable.create(this.element[0], {
            handle: '.drag-handle',
            animation: 150,
            onEnd: (event) => {
                if (event.oldIndex === event.newIndex) {
                    return;
                }
                $.ajax({
                    url: this.element.data('url') + '/position',
                    method: 'POST',
                    data: 'token_csrf=' + $("input[name='token_csrf']").val() + '&positions=' + this.sortable.toArray()
                });
            }
        });
        this.files = [];
        this.render();

        this.element.on('click', '.delete-file', (event) => {
            this.handleReferenceDelete(event);
        });

        this.element.on('blur', '.rename-file', (event) => {
            this.handleReferenceEditFilename(event);
        });

        $.ajax({
            url: this.element.data('url')
        }).then(data => {
            this.files = data;
            this.render();
        })
    }

    addReference(file) {
        this.files.unshift(file);
        this.render();
    }

    handleReferenceDelete(event) {
        const $li = $(event.currentTarget).closest('.file-group');
        const id = $li.data('id');
        $li.addClass('disabled');

        $.ajax({
            url: '/cpanel/file/' + id + '/delete',
            method: 'POST',
            data: "token_csrf=" + $("input[name='token_csrf']").val()
        }).then(() => {
            this.files = this.files.filter(file => {
                return file.id !== id;
            });
            this.render();
        });
    }

    handleReferenceEditFilename(event) {
        const $li = $(event.currentTarget).closest('.file-group');
        const id = $li.data('id');
        const file = this.files.find(file => {
            return file.id === id;
        });

        let newName = $(event.currentTarget).val();

        if (newName !== file.originalFilename) {
            file.originalFilename = newName;
            $.ajax({
                url: '/cpanel/file/' + id + '/rename',
                method: 'POST',
                data: 'token_csrf=' + $("input[name='token_csrf']").val() + '&name=' + newName
            });
        }
    }

    formatDate(createdAt) {

        function addingZero(number) {
            if (number < 10) {
                number = '0' + number;
            }
            return number;
        }

        let date = new Date(createdAt);

        let dd = addingZero(date.getDate());
        let mm = addingZero(date.getMonth() + 1);
        let yy = date.getFullYear();

        let h = addingZero(date.getHours());
        let m = addingZero(date.getMinutes());

        return dd + '.' + mm + '.' + yy + ' ' + h + ':' + m;
    }

    render() {
        const itemsHtml = this.files.map(file => {
            return `
<li class="mb-2 d-flex justify-content-between flex-nowrap file-group" data-id="${file.id}">

    <div class="flex-grow-1">
        <input type="text" value="${file.originalFilename}" class="w-100 form-control rename-file"> 
    </div>
    
    <div class="flex-nowrap text-right ">
        <label class="badge badge-success ml-3">${this.formatDate(file.createdAt)}</label>   
        <button type="button" class="ml-2 btn btn-info btn-rounded btn-icon drag-handle"><i class="mdi mdi-cursor-pointer"></i></button>       
        <a href="/cpanel/file/${file.id}" class="ml-2" style="text-decoration: none;">
        <button type="button"  class="btn btn-primary btn-rounded btn-icon"><i class="mdi mdi-download"></i></button>
        </a>
        <button type="button" class="ml-2 btn btn-danger btn-rounded btn-icon delete-file"><i class="mdi mdi-delete-forever"></i></button>
    </div>
    
</li>
`
        });

        this.element.html(itemsHtml.join(''));
    }
}
