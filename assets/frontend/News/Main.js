import Content from '../Content.js';

const newsContainer = 'div.news-container';

if ($(newsContainer).length !== 0) {
    new Content($(newsContainer), 'div.item-news', 12, 4, $('.button-loading'));
}