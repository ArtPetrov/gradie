import ymaps from "ymaps";
import Salons from "./Salons";

const SalonsHelper = new Salons($("ul.cities-list"));

ymaps.load('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=0bf42d44-9de2-411f-b992-b4dfe8c3bcb7').then(maps => {

    maps.geolocation.get({
        provider: 'yandex',
        mapStateAutoApply: true
    }).then(result => setCenter(result));

    const suggest = new maps.SuggestView('search_city');
    suggest.events.add('select', (e) => {
        maps.geocode(e.originalEvent.item.value, {results: 1}).then(
            res => setCenter(res))
    });

    const map = new maps.Map('map', {
        center: [55.753215, 37.622504],
        zoom: 9,
        controls: ['zoomControl']
    }, {
        suppressMapOpenBlock: true,
    });

    const SalonsManager = new maps.ObjectManager({
        clusterize: true,
        gridSize: 32,
        clusterDisableClickZoom: true
    });

    SalonsManager.objects.options.set('preset', 'islands#redDotIcon');
    SalonsManager.clusters.options.set('preset', 'islands#redClusterIcons');
    map.geoObjects.add(SalonsManager);

    SalonsManager.objects.events.add('balloonopen', (e) => {
        const idSalon = e.get('objectId');
        SalonsHelper.visibleSalon(idSalon);
    });
    SalonsManager.objects.events.add('balloonclose', (e) => {
        const idSalon = e.get('objectId');
        SalonsHelper.closeSalon(idSalon);
    });


    $.ajax({
        url: $(".path-for-salons").val()
    }).done(data => SalonsManager.add(data) && readVisibleSalon());

    function setCenter(res) {
        map.setBounds(res.geoObjects.get(0).properties.get('boundedBy'), {checkZoomRange: true});
    }

    function readVisibleSalon() {
        SalonsHelper.clear();
        maps.geoQuery(SalonsManager.objects).searchInside(map).each((object) => SalonsHelper.add(object.properties.get('card')));
    }

    map.events.add(['boundschange', 'datachange', 'objecttypeschange'], (e) => readVisibleSalon());

}).catch(error => console.log('Failed to load Yandex Maps', error));