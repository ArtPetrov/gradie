import Images from "./Images";

let Dropzone = require('dropzone');
Dropzone.autoDiscover = false;

const container = "div.images-box";

if ($(container).length !== 0) {

    let ImageContainer = new Images($(container));

    $("#previews").hide();

    let myDropzone = new Dropzone(document.body, {
        paramName: 'file',
        myDropzone: false,
        url: $(container).data('path-upload'),
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
        response.cover = 0;
        ImageContainer.addWithRender(response);
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        formData.append("token_csrf", $("input[name='token_csrf']").val());
    });

    myDropzone.on("queuecomplete", function (file) {
        if ($("#previews div").length === 0) {
            $("#previews").hide();
        }
    });
}