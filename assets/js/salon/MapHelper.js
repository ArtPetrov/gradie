export default class MapHelper {

    constructor(coords) {
        this.lat = $('input[name="' + coords + '[lat]"]');
        this.lon = $('input[name="' + coords + '[lon]"]');
    }

    getLat() {
        return Number(this.lat.val());
    }

    getLon() {
        return Number(this.lon.val());
    }

    setCoords(coords) {
        this.lat.val(coords[0]);
        this.lon.val(coords[1]);
    }

    isCorrectCoords() {
        return '' !== this.lat.val() && '' !== this.lon.val();
    }

    getCoords() {
        return [this.getLat(), this.getLon()];
    }

    getCenter() {
        if (this.isCorrectCoords()) {
            return this.getCoords();
        }
        return [55.753994, 37.622093];
    }

}
