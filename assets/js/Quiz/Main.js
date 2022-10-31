import Quiz from "./Quiz";

const buttonSave = $('.save-map');
const quiz = new Quiz(
    buttonSave.data('path-source'),
    buttonSave.data('path-save'),
    $(".list-quest"),
    document.getElementById('map-quest')
)

buttonSave.click(() => quiz.saveMap());

// $(".list-quest").click(function (e) {
//     let id = $(e.currentTarget).find('input').attr("name");
//     nodes.add({id: id, label: "I'm new!"});
//     console.log(id);
// });


// network.on("click", function () {
//     edges.getIds().map(id => console.log(edges.get(id)));
// });
//
// network.on("oncontext", function (params) {
//     params.event = "[original event]";
//     document.getElementById("eventSpan").innerHTML =
//         "<h2>oncontext (right click) event:</h2>" +
//         JSON.stringify(params, null, 4);
// });
//
// network.on("controlNodeDragging", function (params) {
//     params.event = "[original event]";
//     document.getElementById("eventSpan").innerHTML =
//         "<h2>control node dragging event:</h2>" +
//         JSON.stringify(params, null, 4);
// });
// network.on("controlNodeDragEnd", function (params) {
//     params.event = "[original event]";
//     document.getElementById("eventSpan").innerHTML =
//         "<h2>control node drag end event:</h2>" +
//         JSON.stringify(params, null, 4);
//     console.log("controlNodeDragEnd Event:", params);
// });
// network.on("zoom", function (params) {
//     document.getElementById("eventSpan").innerHTML =
//         "<h2>zoom event:</h2>" + JSON.stringify(params, null, 4);
// });
// network.on("showPopup", function (params) {
//     document.getElementById("eventSpan").innerHTML =
//         "<h2>showPopup event: </h2>" + JSON.stringify(params, null, 4);
// });
