import Recommend from './Recommend.js';
import Composition from './Composition.js';
import Attributes from "./Attributes";

const AttributeCollection = 'div.container-property';
const AttributeContainer = "ul.container-attributes";

require('./UploadFiles');

if ($(AttributeContainer).length !== 0) {
    new Attributes($(AttributeContainer), $(AttributeCollection));
}


const recommendedSearchInput = '#recommended-search';
const containerRecommended = $('ul.recommended-box');
const pathForProductsRecommend = containerRecommended.data('path');

if ($(recommendedSearchInput).length !== 0) {

    const RecommendProduct = new Recommend(containerRecommended);
    $(recommendedSearchInput).autocomplete({hint: false, clearOnSelected: true}, [
        {
            source: function (query, cb) {
                $.ajax({
                    url: pathForProductsRecommend,
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
        RecommendProduct.adding(suggestion);
    });

}

const compositionSearchInput = '#composition-search';
const containerComposition = $('ul.composition-box');
const pathForProducts = containerComposition.data('path');

if ($(compositionSearchInput).length !== 0) {

    const CompositionProduct = new Composition(containerComposition);
    $(compositionSearchInput).autocomplete({hint: false, clearOnSelected: true}, [
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
        CompositionProduct.adding(suggestion);
    });

}

$('input[name^="form[categories][categories]"]').click((e) => {
    const element = $(e.currentTarget);
    if (element.prop('checked')) {
        let main = $('select[name="form[categories][main]"]');
        if (Number(main.val()) === 0) {
            const label = element.closest('label').text().trim();
            $('select[name="form[categories][main]"] option').each(function(){
              const current = $(this).text().replace(/-/g,'').trim();
              if(current===label){
                  $(this).attr('selected', 'selected');
              }
            });
        }
    }
});
