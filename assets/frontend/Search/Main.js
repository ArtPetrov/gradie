import Search from './Search.js';

const searchSelector = '.search-products';
const boxResult = '.search__inner';
const containerResult = 'ul.search__items';
const queryString = '.search__text';
if ($(searchSelector).length !== 0) {
    new Search($(searchSelector), $(boxResult), $(queryString), $(containerResult));
}