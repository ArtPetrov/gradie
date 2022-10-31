import Files from "./Files";

let Dropzone = require('dropzone');
Dropzone.autoDiscover = false;

const container = "ul.d3-project__files-list";
const errors = "p.files-upload-error";

if ($(container).length !== 0) {

    let FilesContainer = new Files($(container),$(errors));

    let myDropzone = new Dropzone(document.body, {
        paramName: 'file',
        myDropzone: false,
        previewTemplate: false,
        previewsContainer: false,
        url: $(container).data('path-upload'),
        clickable: ".upload-file"
    });

    myDropzone.on("success", function (file, response) {
        FilesContainer.addWithRender(response);
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        formData.append("token_csrf", $("input[name='token_csrf']").val());
    });
}

if ($("p.view-completed-lead").length !== 0) {
    window.dispatchEvent(new Event("Lead"));
}
