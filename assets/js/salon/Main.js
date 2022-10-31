require('autocomplete.js');
require('autocomplete.js/dist/autocomplete.jquery.min');

import ymaps from "ymaps";
import MapHelper from "./MapHelper";

let searchDealer = $('#form_dealer_name');
let hideDealerId = $('#form_dealer_id');

searchDealer.autocomplete({hint: false, clearOnSelected: true}, [
    {
        source: function (query, cb) {
            $.ajax({
                url: searchDealer.data('path'),
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
            suggestion: suggestion => '<div class="m-2">' + suggestion.name + '</div>',
        }
    }
]).on('autocomplete:selected', function (event, suggestion, dataset, context) {
    hideDealerId.val(suggestion.id);
    searchDealer.autocomplete('val', suggestion.name);
});




const Helper = new MapHelper('form[coords]');

ymaps.load('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=0bf42d44-9de2-411f-b992-b4dfe8c3bcb7').then(maps => {

    let myPlacemark;

    const suggest = new maps.SuggestView('form_info_address');
    suggest.events.add('select', (e) => {
        maps.geocode(e.originalEvent.item.value, {results: 1}).then(res => {
            const firstGeoObject = res.geoObjects.get(0);
            const coords = firstGeoObject.geometry.getCoordinates();
            const bounds = firstGeoObject.properties.get('boundedBy');
            assignDot(coords);
            map.setBounds(bounds, {checkZoomRange: true});
        })
    });

    const map = new maps.Map('map', {
        center: Helper.getCenter(),
        zoom: 9,
        controls: ['zoomControl']
    }, {
        suppressMapOpenBlock: true,
    });

    if(Helper.isCorrectCoords()){
        assignDot(Helper.getCoords())
    }

    map.events.add('click', function (e) {
        assignDot(e.get('coords'));
    });

    function setCoords(coords) {
        Helper.setCoords(coords);
    }

    function assignDot(coords) {

        setCoords(coords);

        if (myPlacemark) {
            return myPlacemark.geometry.setCoordinates(coords);
        }

        myPlacemark = createPlacemark(coords);
        map.geoObjects.add(myPlacemark);

        myPlacemark.events.add('dragend', () => setCoords(myPlacemark.geometry.getCoordinates()));
    }

    function createPlacemark(coords) {
        return new maps.Placemark(coords, {}, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: true
        });
    }


}).catch(error => console.log('Failed to load Yandex Maps', error));