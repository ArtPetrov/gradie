export default class Search {

    constructor(inputSearch, box, queryString, resultContainer) {
        this.flagRequest = false;
        this.queueString = '';
        this.input = inputSearch;
        this.path = inputSearch.data('path');
        this.box = box;
        this.queryString = queryString;
        this.resultContainer = resultContainer;
        this.results = [];
        this.input.on('input', (e) => this.inputSearch(e))
    }

    inputSearch(e) {
        const query = $(e.currentTarget).val().trim();
        this.visibleQuery(query);
        if (query.length > 1) {
            this.loadSource(query);
        }
    }

    add(data) {
        this.results.push(data);
    }

    queue(query) {
        this.queueString = query;
    }

    loadSource(query) {
        if (this.flagRequest) {
            return this.queue(query)
        }
        this.flagRequest = true;
        $.ajax({
            url: this.path,
            type: "POST",
            dataType: "json",
            data: {"query": query}
        }).then(data => {
            this.results = [];
            data.forEach((product) => this.add(product));
            this.render();
            this.flagRequest = false;
            if (this.queueString.length > 1) {
                const q = this.queueString;
                this.queueString = '';
                this.visibleQuery(q);
                return this.loadSource(q);
            }

            window.dispatchEvent(new Event("Search"));
        });
    }

    visibleQuery(string) {
        if (string.length === 0) {
            this.queryString.html('Введите поисковый запрос');
            return;
        }
        this.queryString.html('Искать “' + string + '”');
    }

    template(result) {
        const template = `
                                <li class="search__item">
                                    <a href="${result.link}"><img class="search__item-image" src="${result.cover}" width="48"
                                         height="48" alt="${result.name}"/></a>
                                    <div class="search__item-inner">
                                        <a class="search__item-title" href="${result.link}">${result.name}</a>
                                        <div class="search__item-price  search__item-price--red">${result.cost} ₽</div>
                                        <div class="search__item-in-stock">в наличии</div>
                                    </div>
                                </li>
`;
        return template.trim();
    }

    render() {
        if (this.results.length === 0) {
            this.resultContainer.empty();
            return this.queryString.html('По Вашему запросу ничего не найдено.');

        }
        this.resultContainer.html(this.results.map((product) => {
            return this.template(product);
        }).join(''));
    }
}