require('autocomplete.js');
require('autocomplete.js/dist/autocomplete.jquery.min');

import Basket from './Basket.js';
import OrderStorage from './OrderStorage.js';

const hash = window.location.hash;
hash && $('ul.nav a[href="' + hash + '"]').tab('show');

$('.nav-tabs a').click(function (e) {
    $(this).tab('show');
    window.location.hash = this.hash;
    $('html,body').scrollTop($('body').scrollTop());
});


const BasketTable = 'table.basket-table';
const BasketSearch = '#basket-search';

if ($(BasketTable).length !== 0) {
    const serverApi = new OrderStorage($(BasketTable).data('path'), $(BasketTable).data('token'));
    const basket = new Basket($(BasketTable), serverApi);

    $(BasketSearch).autocomplete({hint: false, clearOnSelected: true}, [
        {
            source: function (query, cb) {
                $.ajax({
                    url: $(BasketTable).data('path'),
                    type: "GET",
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
        basket.adding(suggestion);
    });
}