require('autocomplete.js');
require('autocomplete.js/dist/autocomplete.jquery.min');

import Images from "./Images";
import Composition from './Composition.js';

const compositionSearchInput = '#composition-search';
const containerComposition = $('ul.composition-box');
const pathForProducts = containerComposition.data('path');

if ($(compositionSearchInput).length !== 0) {

    const CompositionProduct = new Composition(containerComposition);
    $(compositionSearchInput).autocomplete({hint: false, clearOnSelected: true}, [
        {
            source: function (query, cb) {
                $.ajax({
                    url: pathForProducts,
                    type: "POST",
                    dataType: "json",
                    data: {"query": query}
                }).then(function (data) {
                    cb(data)
                })
            },
            debounce: 200,
            cache: false,
            templates: {
                empty: query => '<div class="m-4">По <b>вашему запросу</b> ничего не найдено.</div>',
                suggestion: suggestion => '<div class="m-2">' + suggestion.article + ' | ' + suggestion.name + '</div>',
            }
        }
    ]).on('autocomplete:selected', function (event, suggestion, dataset, context) {
        CompositionProduct.adding(suggestion);
    });

}


let Dropzone = require('dropzone');
Dropzone.autoDiscover = false;

const container = "div.images-box";

if ($(container).length !== 0) {

    let ImageContainer = new Images($(container),'images',true);

    $("#previews").hide();

    let myDropzone = new Dropzone("div#images", {
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

const containerDiy = "div.images-diy";
if ($(containerDiy).length !== 0) {

    let ImageContainerDiy = new Images($(containerDiy),'diy',false);
    $("#previews-diy").hide();

    let myDropzoneDiy = new Dropzone("div#diy", {
        paramName: 'file',
        myDropzone: false,
        url: $(containerDiy).data('path-upload'),
        previewTemplate: $("#template").html(),
        previewsContainer: "#previews-diy",
        clickable: ".upload-file-diy"
    });

    myDropzoneDiy
        .on("addedfiles", function (event) {
            if ($("#previews-diy div").length !== 0) {
                $("#previews-diy").show();
            }
        })
        .on("success", function (file, response) {
            myDropzoneDiy.removeFile(file);
            ImageContainerDiy.addWithRender(response);
        })
        .on("sending", function (file, xhr, formData) {
            formData.append("token_csrf", $("input[name='token_csrf']").val());
        })
        .on("queuecomplete", function (file) {
            if ($("#previews-diy div").length === 0) {
                $("#previews-diy").hide();
            }
        });
}