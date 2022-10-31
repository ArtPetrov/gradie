export default class Container {

    constructor(container, selectorElement, countStart, countLoading, button) {
        this.button = button;
        this.container = container;
        this.countLoading = countLoading;
        this.path = container.data('path');
        this.properties = container.data('properties').split(',');
        this.selectorElement = selectorElement;
        this.currentCount = container.find(selectorElement).length;
        this.worked = this.currentCount === countStart;
        if(!this.worked){
            this.button.hide();
        }
        this.loadingScroll = false;

        this.button.click(() => this.loadSource());

        $(window).scroll((e)=> {
            if (this.loadingScroll || !this.worked) return;
            if ($(e.currentTarget).scrollTop() > this.button.offset().top - $(window).height()) {
                this.loadingScroll = true;
                this.button.hide();
                this.loadSource();
            }
        });
    }




    count() {
        return this.container.find(this.selectorElement).length;
    }

    loadSource() {
        if (!this.worked) {
            return null;
        }

        $.ajax({
            url: this.path,
            type: "POST",
            dataType: "json",
            data: {"offset": this.count()}
        }).then(data => {
            if (data.length <= this.countLoading) {
                this.worked = false;
            } else {
                this.button.show();
                data.pop();
            }
            data.forEach((element) => this.add(element));

            this.loadingScroll = false;

        });
    }

    add(data) {
        this.container.append(this.template(data));
    }

    template(data) {
        let template = this.container.data('template');
        if (data.hasOwnProperty('cover')) {
            data.cover = data.cover === null ?
                this.container.data('nocover')
                : this.container.data('cover').replace(/__cover__/g, data.cover);
        }
        this.properties.forEach((attr) => {
            if (data.hasOwnProperty(attr)) {
                const pattern = new RegExp('__' + attr + '__', "g");
                template = template.replace(pattern, data[attr]);
            }
        });
        return template;
    }
}