export default class RenderQuest {

    render(quest, currentStep, allSteps) {
        let html = this.procent(Math.trunc(currentStep / allSteps * 100));

        switch (quest.type) {
            case 'IMAGES_OPTION':
                html += this.renderImagesOption(quest);
                break;
            case 'IMAGES_CHECKBOX':
                html += this.checkBoxImagesOption(quest);
                break;
            case 'INPUT':
                html += this.inputGroup(quest);
                break;
            case 'CHECKBOX':
                html += this.checkboxGroup(quest);
                break;
            case 'SELECT':
                html += this.selectElement(quest);
                break;
        }

        html += this.bottom(quest, currentStep, allSteps)
        return html;
    }

    inputGroup(quest) {
        let values = Object.keys(quest.values).reduce((html, id) => html + `                   
                    <div class="test__size-field" >
                      <label class="test__size-label quest-${quest.id}-label" for="inp${quest.id}">${quest.values[id].value}</label>
                      <input class="test__size-input quest-${quest.id}" id="inp${quest.id}" type="text" data-title="${quest.values[id].name}" style="${quest.values[id].style}"/>
                    </div>
            `
            , '');

        if (quest.isAnotherAnswer) {
            values += `
                    <div class="test__size-field">
                      <label class="test__size-label" for="test-another">Другое:</label>
                      <input class="test__size-input" id="test-another" type="text" value="" />
                    </div>
            `;
        }

        return `
              <div class="test__inside  test__step-4">
                <h2 class="test__heading">${quest.quest}</h2>
                <div class="test__size">
                    <div class="test__size-fields">${values}</div>
                  <p class="test__size-text">${quest.help ?? ''}</p>
                </div>
              </div>
        `;
    }

    selectElement(quest) {
        let values = Object.keys(quest.values).reduce((html, id) => html + `
                  <option value="${quest.values[id].value}" style="${quest.values[id].style}">${quest.values[id].value}</option>            `
            , '');
        let manual = '';
        if (quest.isAnotherAnswer) {
            manual += `
                    <div class="test__another">
                      <label class="test__another-label" for="test-another">Другое:</label>
                      <input class="test__another-input" id="test-another" type="text" />
                    </div>
            `;
        }
        return `
              <div class="test__inside  test__step-5">
                <h2 class="test__heading">${quest.quest}</h2>
                <div class="test__elements">
                    <select class="select  test__select"  name="quest-${quest.id}" >
                    <option value="">Выберите вариант</option>
                    ${values}
                    </select>
                    ${manual}
                  <p class="test__elements-text">${quest.help ?? ''}</p>
                </div>
              </div>
        `;
    }

    checkboxGroup(quest) {
        let values = Object.keys(quest.values).reduce((html, id) => html + `
                    <label class="checkbox  test__option" style="${quest.values[id].style}">
                      <input class="checkbox__input  visually-hidden" type="checkbox" name="quest-${quest.id}" value="${quest.values[id].name}" />
                      <span class="checkbox__indicator"></span>
                      ${quest.values[id].name}
                    </label>
            `
            , '');

        if (quest.isAnotherAnswer) {
            values += `
                    <div class="test__another">
                      <label class="test__another-label" for="test-another">Другое:</label>
                      <input class="test__another-input" id="test-another" type="text" />
                    </div>
            `;
        }

        return `
              <div class="test__inside  test__step-3">
                <h2 class="test__heading">${quest.quest}</h2>                
                <div class="test__options">
                  <div class="test__options-section">
                  ${values}
                  </div>                    
                </div>
                <p>${quest.help ?? ''}</p>
              </div>
        `;
    }

    renderImagesOption(quest) {
        if (3 > Object.keys(quest.values).length) {
            return this.twoImagesOption(quest);
        }
        return this.moreImagesOption(quest);
    }

    twoImagesOption(quest) {
        const values = Object.keys(quest.values).reduce((html, id) => html + `
                  <div class="test__color-item" style="${quest.values[id].style}">
                    <img class="test__color-image" src="${quest.values[id].cover.big}" width="430" height="245" alt="" />
                    <label class="test__color-radio">
                      <input class="test__color-radio-input visually-hidden" type="radio" name="quest-${quest.id}" value="${quest.values[id].name}"  />
                      <span class="test__color-radio-button">${quest.values[id].name}</span>
                    </label>
                  </div>
            `
            , '');

        return `
              <div class="test__inside  test__step-1">
                <h2 class="test__heading">${quest.quest}</h2>
                <div class="test__color">
                ${values}
                </div>
              </div>
        `;

    }

    moreImagesOption(quest) {
        const values = Object.keys(quest.values).reduce((html, id) => html + `
                    <label class="test__type-item"  style="${quest.values[id].style}">
                      <input class="test__type-input  visually-hidden" type="radio" name="quest-${quest.id}" value="${quest.values[id].name}" />
                      <span class="test__type-inner">
                        <img class="test__type-image" src="${quest.values[id].cover.small}" width="168" height="168" alt="" />
                        <span class="test__type-caption">${quest.values[id].name}</span>
                      </span>
                    </label>`, '');
        return `
              <div class="test__inside  test__step-5">
                <h2 class="test__heading">${quest.quest}</h2>
                <div class="test__elements">
                  <div class="test__type">${values}</div>
                  <p class="test__elements-text">${quest.help ?? ''}</p>
                </div>
              </div>
        `;
    }

    checkBoxImagesOption(quest) {
        const values = Object.keys(quest.values).reduce((html, id) => html + `
                    <label class="test__type-item"  style="${quest.values[id].style}">
                      <input class="test__type-input  visually-hidden" type="checkbox" name="quest-${quest.id}" value="${quest.values[id].name}" />
                      <span class="test__type-inner">
                        <img class="test__type-image" src="${quest.values[id].cover.small}" width="168" height="168" alt="" />
                        <span class="test__type-caption">${quest.values[id].name}</span>
                      </span>
                    </label>`, '');
        return `
              <div class="test__inside  test__step-5">
                <h2 class="test__heading">${quest.quest}</h2>
                <div class="test__elements">
                  <div class="test__type">${values}</div>
                  <p class="test__elements-text">${quest.help ?? ''}</p>
                </div>
              </div>
        `;
    }

    renderFinish() {
        return this.procent(100) + ` <div class="test__inside  test__finish">

                <h2 class="test__heading">Финиш!</h2>

                <p class="test__finish-text">Оставьте ваши контактные данные, чтобы мы могли направить вам результат!</p>

                <div class="test__finish-fields">

                  <div class="test__finish-field">
                    <label class="test__finish-label" for="name">Ф. И. О</label>
                    <input class="test__finish-input" id="name" type="text" value="" />
                  </div>

                  <div class="test__finish-field">
                    <label class="test__finish-label" for="phone">Номер телефона</label>
                    <input class="test__finish-input" id="phone" type="tel" value="" />
                  </div>

                  <div class="test__finish-field">
                    <label class="test__finish-label" for="email">E-mail</label>
                    <input class="test__finish-input" id="email" type="email" value="" />
                  </div>

                </div>

                <div class="test__agree">
                  <label class="radio">
                    <input class="radio__input  visually-hidden" type="radio" checked />
                    <span class="radio__indicator"></span>
                    Я даю согласие на обработку своих персональных данных.
                  </label>
                </div>

              </div>
              <div class="test__bottom">
                <button class="test__next btn-finish" type="button">Готово</button>
              </div>
`
    }

    renderThanks(text, url, title) {
        return `
              <div class="test__end">
                <p class="test__end-text">Спасибо!<br /> ${text}</p>
                <a class="test__end-button" href="${url}">${title}</a>
              </div>
        `
    }

    procent(procent) {
        return `<div class="test__scale">
<div class="test__scale-value" style="width: ${procent}%;">${procent}%</div>
</div>`
    }

    bottom(quest, current, all) {
        let back = current === 1 ? 'style="display:none;"' : '';
        let skip = quest.isSkip ? '<button class="test__skip quiz-skip" type="button">Можно пропустить</button>' : '';
        return `
              <div class="test__bottom">
                <button class="test__back quiz-back" type="button" ${back}>Назад</button>
                <p class="test__current-step">Шаг ${current} из ${all}</p>
                ${skip}
                <button class="test__next quiz-next" type="button">Дальше</button>
              </div>
        `;
    }

}