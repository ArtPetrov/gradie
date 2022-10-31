import ReferenceList from './ReferenceList';

let Dropzone = require('dropzone');
Dropzone.autoDiscover = false;

$(document).ready(function () {

    let referenceList = new ReferenceList($('div.tab-pane.active ul.js-reference-list'));

    let cache = [];
    let currentCategory = Number($("a.select-category.active").data('id'));
    cache[currentCategory] = referenceList;

    $("li .select-category:not(.active)").click(function () {

        currentCategory = Number($(this).data('id'));
        if (typeof cache[currentCategory] === 'undefined') {
            cache[currentCategory] = new ReferenceList($('#list-file-' + currentCategory + ' ul.js-reference-list'));
        }
        referenceList = cache[currentCategory];
        referenceList.render();
    });

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
        if (response.category === currentCategory) {
            referenceList.addReference(response.file);
        } else {
            cache[response.category].addReference(response.file);
        }
    });

    myDropzone.on("sending", function (file, xhr, formData) {
        formData.append("token_csrf", $("input[name='token_csrf']").val());
        formData.append("category", $("ul.category-dealer").find('a.active').data('id'));
    });

    myDropzone.on("queuecomplete", function (file) {
        if ($("#previews div").length === 0) {
            $("#previews").hide();
        }
    });
});
