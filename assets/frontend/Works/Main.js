import Content from '../Content.js';

const workContainer = 'ul.projects-container';

if (0 !== $(workContainer).length) {
    new Content($(workContainer), 'li', 6, 3, $('.button-loading'));
    window.dispatchEvent(new Event("ViewContent"));
}

if ( 0 !== $("div.view-works").length) {
    window.dispatchEvent(new Event("ViewContent"));
}