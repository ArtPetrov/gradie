import tinymce from 'tinymce'
import 'tinymce/themes/silver';

let form = document.querySelector('.tinymce-form');
let pathForUpload = form.dataset.pathUpload;

tinymce.init({
    content_css: '/css/style.css',
    type_post: 'news-page__entry',
    selector: '.tinymce',
    language: 'ru',
    forced_root_block: false,
    relative_urls: false,
    convert_urls: false,
    remove_script_host: false,
    autosave_ask_before_unload: false,
    automatic_uploads: true,
    extended_valid_elements: '*[*]',
    plugins: [
        "advlist autolink lists link image charmap preview anchor",
        "searchreplace visualblocks code",
        "insertdatetime media table paste imagetools wordcount"
    ],

    images_upload_url: pathForUpload,
    file_picker_types: 'file media image',

    file_picker_callback: function (cb, value, meta) {

        var input = document.createElement('input');
        input.setAttribute('type', 'file');

        if (meta.filetype === 'image') {

            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), {title: file.name});
                };
                reader.readAsDataURL(file);
            };
        } else {

            input.onchange = function () {

                var file = this.files[0];

                var formData = new FormData();
                formData.append("filetype", meta.filetype);
                formData.append('file', file);

                $.ajax({
                    url: pathForUpload,
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    type: 'post',
                    success: function (response) {
                        cb(response.location, {});
                    }
                });
            };
        }

        input.click();

    },

    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | imagesDiy",

    setup: (editor) => {
        editor.ui.registry.addButton('imagesDiy', {
            text: 'Слайдер сборки',
            onAction: () => {
                editor.insertContent('#SLOT#');
                let content = editor.getContent().replace('#SLIDER_DIY#','').replace('#SLOT#','#SLIDER_DIY#');
                tinyMCE.activeEditor.setContent(content);
            }
        });
    }
});