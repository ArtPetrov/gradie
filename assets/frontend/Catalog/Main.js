import Products from './ProductList';
import Filters from './Filters';
import Sort from './Sort';
import Url from './Url'

const urlize = new Url({
    sorting: {
        order: 'ASC',
        slug: 'popular'
    },
    style: 'cards'
});

const filters = new Filters($('div.picked-filter'));
filters.insertParamsFromUrl(urlize.parseFilters());
filters.addNumberFilter($('.field-value-number'));
filters.addSelectFilterColor($('.field-value-select-color'));

const sortable = new Sort($('div.sort'), urlize);
sortable.addSort({label: 'По популярности', slug: 'popular', order: 'ASC', active: false});
sortable.addSort({label: 'По цене', slug: 'cost', order: 'ASC', active: false});

$('button.layout__button--cards').click(() => urlize.updateStyleVisible('cards').changeUrl());
$('button.layout__button--list').click(() => urlize.updateStyleVisible('list').changeUrl());

const productContainer = 'div.product-container';

if ($(productContainer).length !== 0) {
    new Products(
        $(productContainer),
        'div.product',
        $(productContainer).data('start-count'),
        $(productContainer).data('load-step'),
        $('.button-loading'),
        filters,
        sortable,
        urlize
    );
}