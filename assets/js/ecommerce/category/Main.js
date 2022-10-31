import Filters from './Filters.js';

const AttributeSelector = 'div.container-attributes';
const FilterSelector = "ul.container-filters";

if ($(AttributeSelector).length !== 0) {

    let filters = new Filters($(AttributeSelector), $(FilterSelector));

    // const showFields = (value) => value === 'SELECT' ? $(GroupSelector).show() : $(GroupSelector).hide();
    //
    // $(".create-field-value").on('click', () => attrList.addElement());
    //
    // $(SwitcherSelector).on('change', function () {
    //     showFields(this.value);
    // });
    //
    // showFields($(SwitcherSelector).val());
}
