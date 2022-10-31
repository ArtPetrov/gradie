import Selectors from './Selectors.js';
import Products from './Products.js';

const SelectorsCollection = 'div.container-property-attrs';
const SelectorsContainer = "ul.container-filters";

if ($(SelectorsContainer).length !== 0) {
    new Selectors($(SelectorsContainer), $(SelectorsCollection));
}

const productsSearchInput = '#products-group-search';
const containerProducts = $('ul.products-group-box');
const pathForProducts = containerProducts.data('path');

if ($(productsSearchInput).length !== 0) {

    const GroupProducts = new Products(containerProducts);
    $(productsSearchInput).autocomplete({hint: false, clearOnSelected: true}, [
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
        GroupProducts.adding(suggestion);
    });

}

