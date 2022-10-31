import tinymce from 'tinymce'
import 'tinymce/themes/silver';

$(function () {

    tinymce.init({
        selector: '.tinymce',
        language: 'ru',
        forced_root_block: false,
        relative_urls: false,
        convert_urls: false,
        menubar: false,
        statusbar: false,
        paste_as_text: true,
        plugins: [
            "lists visualblocks code paste link"
        ],
        toolbar: "undo redo | bold italic underline strikethrough | bullist numlist | code blockquote | link"
    });

});