export default class AttributeFields {
    constructor(link) {
        this.collection = link;
        this.templatePrototype = this.collection.data('prototype');

        this.collection.on('click', '.delete-field', (event) => this.removeElement(event));
    }

    count() {
        return this.collection.find($("li")).length - 1;
    }

    nextIndex() {
        return this.count() + 1;
    }

    prototype() {
        let newElement = this.templatePrototype.replace(/__name__/g, this.nextIndex());
        return $('<li class="form-row p-2"></li>').append(newElement);
    }

    addElement() {
        this.collection.find($("li:last")).before(this.prototype());
    }

    removeElement(event) {
        this.collection.find($(event.currentTarget)).closest('li').remove();
    }
}
