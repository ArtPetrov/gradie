import RenderQuest from './RenderQuest';

export default class {

    constructor(zone) {
        this.zone = zone;
        this.pathMap = [];
        this.template = new RenderQuest();
        this.loadQuests();
        this.current = {step: 1, id: 'start', quest: {}};
        this.results = [];
        this.reversPath = [];

        this.zone.on('click', 'a.quiz-start', () => this.start());
        this.zone.on('click', 'button.quiz-next', () => this.next());
        this.zone.on('click', 'button.quiz-skip', () => this.skip());
        this.zone.on('click', 'button.quiz-back', () => this.back());
        this.zone.on('click', 'button.btn-finish', () => this.saveResult());
    }

    render(content) {
        this.zone.html(content);
    }

    start() {
        this.current.quest = this.getQuest(this.pathMap[this.current.id][0]);
        this.render(
            this.template.render(this.current.quest, this.current.step, this.getCountQuests(this.current.id))
        );
    }

    next() {
        const result = this.readValue(this.current.quest);
        if (result) {
            this.results[this.current.step] = {
                'step': this.current.step,
                'quest': this.current.quest.quest,
                'answer': result
            };
        } else {
            return alert('Укажите Вариант ответа!');
        }

        const prevId = this.current.quest.id;
        let nextId = this.pathMap[this.current.quest.id][0];

        if (this.current.quest.isVariable) {
            let valueId;
            Object.keys(this.current.quest.values).forEach(id => {
                if (this.current.quest.values[id].name === result) {
                    valueId = id;
                }
            });
            const localId = this.current.quest.id + ';' + valueId;
            if (typeof this.pathMap[localId] !== "undefined") {
                nextId = this.pathMap[localId][0];
            }
        }

        if (nextId === 'end') {
            return this.finish();
        }

        this.current.quest = this.getQuest(nextId);
        this.current.step++;
        this.reversPath[this.current.quest.id] = prevId;


        this.render(
            this.template.render(this.current.quest, this.current.step, this.getCountQuests(this.current.id))
        );
    }

    saveResult() {
        const name = this.zone.find('#name').val().trim();
        const phone = this.zone.find('#phone').val().trim();
        const email = this.zone.find('#email').val().trim();

        if (email.length < 2 && phone.length < 2) {
            return alert('Укажите номер телефона или корректный email!')
        }

        $.ajax({
            url: this.zone.data('path-save'),
            type: "POST",
            dataType: "json",
            data: {
                name: name,
                phone: phone,
                email: email,
                answers: this.results
            },
            statusCode: {
                406: (e) => {
                    console.log(e.respose.error);
                }
            }
        });

        this.render(this.template.renderThanks(this.zone.data('text-end'), this.zone.data('path-next'), this.zone.data('title-next')));
    }

    skip() {
        this.results[this.current.step] = {
            'step': this.current.step,
            'quest': this.current.quest.quest,
            'answer': 'Пропустили'
        };
        const prevId = this.current.quest.id;
        const nextId = this.pathMap[this.current.quest.id][0];
        if (nextId === 'end') {
            return this.finish();
        }

        this.current.quest = this.getQuest(nextId);
        this.current.step++;
        this.reversPath[this.current.quest.id] = prevId;

        this.render(
            this.template.render(this.current.quest, this.current.step, this.getCountQuests('start'))
        );
    }

    back() {
        this.current.quest = this.getQuest(this.reversPath[this.current.quest.id]);
        this.current.step--;
        this.render(
            this.template.render(this.current.quest, this.current.step, this.getCountQuests('start'))
        );
    }

    finish() {
        this.render(this.template.renderFinish());
    }

    afterLoad() {
        this.createMapQuiz();
        this.countQuests = this.getCountQuests('start');
        this.checkValidQuiz();
    }

    checkValidQuiz() {
        if (this.countQuests > 0 && this.countQuests !== 1024) {
            this.zone.find('a.quiz-start').show();
        } else {
            this.zone.find('p.test__start-text').html('Приносим свои извинения, опрос временно не работает!');
        }
    }

    createMapQuiz() {
        this.data.map.edges.forEach((e) => {
            if (typeof this.pathMap[String(e.from)] == 'undefined') {
                this.pathMap[String(e.from)] = [];
            }
            this.pathMap[String(e.from)].push(String(e.to));
        })
    }

    getQuest(id) {
        return this.data.questions[id];
    }

    getCountQuests(currentPosition) {
        let counter = (id, count) => {
            if (!id.includes(";")) {
                count++;
            }
            if (count > 1024) {
                return 1024;
            }

            let next = this.pathMap[String(id)][0];
            if (next === 'end') {
                return count;
            }
            return counter(next, count)
        }
        return counter(currentPosition, 0);
    }

    loadQuests() {
        $.ajax({
            url: this.zone.data('path-source'),
            type: "GET",
            dataType: "json",
            statusCode: {
                200: (e) => {
                    this.data = e;
                    this.afterLoad();
                }
            }
        })
    }


    readValue(quest) {
        let value = false;

        switch (quest.type) {
            case 'CHECKBOX':
            case 'IMAGES_OPTION':
            case 'IMAGES_CHECKBOX':
                value = this.zone.find('input[name="quest-' + quest.id + '"]:checked').map(function () {
                    return $(this).val();
                }).get().join(', ');
                value = value.length === 0 ? false : value;
                break;
            case 'SELECT':
                value = this.zone.find('select[name="quest-' + quest.id + '"]  option:selected').val();
                value = value.length === 0 ? false : value;
                break;
            case 'INPUT':
                value = this.zone.find('input.quest-' + quest.id).map(function () {
                    let v = $(this).val().trim();
                    if(v.length>0){
                        return $(this).data('title')+': '+v;
                    }
                    return '';
                }).get().join(', ');
                value = value.replace(/, /g, '').length === 0 ? false : value;
                break;

        }

        if (quest.isAnotherAnswer) {
            let manual = this.zone.find('#test-another').val();
            if (manual.length > 0) {
                value = value !== false ? value + ' Свой вариант: ' + manual : 'Свой вариант: ' + manual;
            }
        }

        return value;
    }
}