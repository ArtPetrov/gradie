export default class Reference {
    constructor(currentElement, linkIds) {
        this.linkIds = linkIds;
        this.element = currentElement;
        this.files = [];
        this.render();

        this.element.on('click', '.delete-file', (event) => {
            this.handleReferenceDelete(event);
        });

        this.element.on('blur', '.rename-file', (event) => {
            this.handleReferenceEditFilename(event);
        });

        if (this.linkIds.val().length > 0) {
            $.ajax({
                url: this.element.data('url'),
                method: 'POST',
                data: "ids=" + this.linkIds.val()
            }).then(data => {
                this.files = data;
                this.render();
            })
        }
    }

    addReference(file) {
        this.files.unshift(file);
        this.linkIds.val(this.linkIds.val() + ',' + file.id);
        this.render();
    }

    handleReferenceDelete(event) {
        const $li = $(event.currentTarget).closest('.file-group');
        const id = $li.data('id');
        $li.addClass('disabled');

        this.linkIds.val(this.linkIds.val().replace(id, ''));

        $.ajax({
            url: '/cpanel/mailer/file/' + id + '/delete',
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
                url: '/cpanel/mailer/file/' + id + '/rename',
                method: 'POST',
                data: 'token_csrf=' + $("input[name='token_csrf']").val() + '&name=' + newName
            });
        }
    }

    render() {
        const itemsHtml = this.files.map(file => {
            return `
<li class="mb-2 d-flex justify-content-between flex-nowrap file-group" data-id="${file.id}">

    <div class="flex-grow-1">
        <input type="text" value="${file.originalFilename}" class="w-100 form-control rename-file"> 
    </div>
    
    <div class="flex-nowrap text-right ">     
        <button type="button" class="ml-2 btn btn-danger btn-rounded btn-icon delete-file"><i class="mdi mdi-delete-forever"></i></button>
    </div>
    
</li>
`
        });

        this.element.html(itemsHtml.join(''));
    }
}
