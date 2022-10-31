import ReferenceList from './mailer.reference';

let Dropzone = require('dropzone');
Dropzone.autoDiscover = false;

$(document).ready(function () {

    let referenceList = new ReferenceList($('ul.js-reference-list'),$("input[name='form[mail][files]']"));

    $("#previews").hide();

    let myDropzone = new Dropzone(document.body, {
        paramName: 'file',
        myDropzone: false,
        url: $("input[name='url_upload']").val(),
        previewTemplate: $("#template").html(),
        previewsContainer: "#previews",
        clickable: ".upload-file"
    });

    myDropzone.on("addedfiles", function (event) {
        if ($("#previews div").length !== 0) {
            $("#previews").show();
        }
    });

    myDropzone.on("success", function (file, response) {
        myDropzone.removeFile(file);
        referenceList.addReference(response.file);

    });

    myDropzone.on("sending", function (file, xhr, formData) {
        formData.append("token_csrf", $("input[name='token_csrf']").val());
    });

    myDropzone.on("queuecomplete", function (file) {
        if ($("#previews div").length === 0) {
            $("#previews").hide();
        }
    });
});
