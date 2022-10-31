$(function () {

    chooseRecipient();

    function chooseRecipient() {
        if ($(".recipient-type").length === 0) {
            return;
        }
        $(".recipient-method").hide();
        $(`[data-type='${$(".recipient-type").val()}']`).show();

    }

    $(".recipient-type").change(chooseRecipient);

});