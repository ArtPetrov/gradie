import AttributeFields from './AttributeFields';

const AttributeSelector = 'ul.attributes-list';
const GroupSelector = '.fields-group';
const SwitcherSelector = "select[name='form[type]']";

if ($(AttributeSelector).length !== 0) {

    let attrList = new AttributeFields($(AttributeSelector));

    const showFields = (value) => 'SELECT' === value || 'CHECKBOX' === value ? $(GroupSelector).show() : $(GroupSelector).hide();

    $(".create-field-value").on('click', () => attrList.addElement());

    $(SwitcherSelector).on('change', function () {
        showFields(this.value);
    });

    showFields($(SwitcherSelector).val());
}
