require('autocomplete.js');
require('autocomplete.js/dist/autocomplete.jquery.min');

const DangerError = 'div.alert-danger-valid';

if ($(DangerError).length !== 0) {
    let idErrorTab = '#' + $(DangerError).eq(0).closest("div.tab-pane").attr('aria-labelledby');
    if ($(idErrorTab).length !== 0) {
        $(idErrorTab).tab('show');
    }
}

require('./attribute/Main.js');
require('./category/Main.js');
require('./product/Main.js');
require('./group/Main.js');