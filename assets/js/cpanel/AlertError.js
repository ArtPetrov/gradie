const DangerError = 'div.alert-danger-valid';

if ($(DangerError).length !== 0) {
    let idErrorTab = '#' + $(DangerError).eq(0).closest("div.tab-pane").attr('aria-labelledby');
    if ($(idErrorTab).length !== 0) {
        $(idErrorTab).tab('show');
    }
}
