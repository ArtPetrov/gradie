export default class OrderStorage {

    constructor(path, token) {
        this.path = path;
        this.token = token;
    }

    removeProduct(id) {
        $.ajax({
            url: this.path,
            type: "DELETE",
            dataType: "json",
            data: {id: id, token: this.token},
            statusCode: {
                406: (e)=> {
                    console.log(e.respose.error);
                }
            }
        });
    }

    updateCountProduct(id, count) {
        $.ajax({
            url: this.path,
            type: "PUT",
            dataType: "json",
            data: {id: id, count: count, token: this.token},
            statusCode: {
                406: (e)=> {
                    console.log(e.respose.error);
                }
            }
        });
    }

    addProduct(id) {
        $.ajax({
            url: this.path,
            type: "POST",
            dataType: "json",
            data: {id: id, token: this.token},
            statusCode: {
                406: (e)=> {
                    console.log(e.respose.error);
                }
            }
        });
    }
}
