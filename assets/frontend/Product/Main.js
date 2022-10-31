import Group from "./Group";

new Group(
    $('.render-product-fields'),
    $('.render-product-fields-mob'),
    $('input[name="path-groups"]').val(),
    $('input[name="current-id"]').val(),
    $('input[name="path-redirect"]').val()
);
