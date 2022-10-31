export default class Salons {

    constructor(conatiner) {
        this.conatiner = conatiner;
        this.currentOpen = null;
    }

    clear() {
        this.conatiner.html('');
    }

    add(salon) {
        this.conatiner.append(this.template(salon));
        if (this.currentOpen) {
            this.visibleSalon(this.currentOpen);
        }
    }

    visibleSalon(idSalon) {
        this.conatiner.find('li').hide();
        this.conatiner.find('li').each((index, e) => {
            const id = $(e).data('id');
            if (idSalon === id) {
                this.currentOpen = idSalon;
                this.conatiner.find('li[data-id="' + id + '"').show();
            }
        });
    }

    closeSalon(idSalon) {
        if (this.currentOpen === idSalon) {
            this.currentOpen = null;
        }
        this.conatiner.find('li').show();
    }

    template(salon) {
        let tpl = '';
        tpl += `<li class="addresses__item" data-id="${salon.id}">`;
        tpl += `<p class="addresses__title">${salon.type}</p><p>`;

        if (salon.name) {
            tpl += `${salon.name}<br />`;
        }

        if (salon.address) {
            tpl += `Адрес: ${salon.address}<br />`;
        }

        if (salon.phone) {
            tpl += `Тел.: ${salon.phone}<br />`;
        }

        if (salon.email) {
            tpl += `e-mail: <a href="${salon.email}">${salon.email}</a><br />`;
        }

        if (salon.timetable) {
            tpl += `Режим работы: ${salon.timetable}<br />`;
        }

        if (salon.site) {
            tpl += `<a href="${salon.site}">${salon.site}</a><br />`;
        }
        if (salon.comment) {
            tpl += salon.comment;
        }

        tpl += `</p></li>`;

        return tpl;

    }

}