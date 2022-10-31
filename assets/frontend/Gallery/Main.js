import Content from '../Content.js';
const galleryContainer = 'ul.gallery-container';

if ($(galleryContainer).length !== 0) {
    new Content($(galleryContainer), 'li', 12, 4,$('.button-loading'));
}