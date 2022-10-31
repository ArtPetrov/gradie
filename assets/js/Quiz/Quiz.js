import {DataSet, Network} from "vis-network/standalone";
import options from "./Config";

export default class Quiz {

    constructor(pathLoad, pathSave, CheckboxList, canvas) {
        this.pathSave = pathSave;
        this.canvas = canvas;
        this.pathLoad = pathLoad;
        this.checkboxList = CheckboxList;

        this.loadQuests();

        this.checkboxList.click((e) =>
            this.actionCheck(
                String($(e.currentTarget).find('input').attr("name"))
                    .split('-')[1]
            )
        );
    }

    actionCheck(id) {
        let quest = this.data.questions[id];
        for (let node of this.nodes.getIds()) {
            if (String(node) === String(id)) {
                return this.removeQuest(quest)
            }
        }
        return this.addQuest(quest);
    }

    addQuest(quest) {
        this.nodes.add({id: String(quest.id), label: quest.name});
        if (Boolean(quest.isVariable)) {
            Object.keys(quest.values).forEach(e => {
                let item = quest.values[e];
                let id = quest.id + ';' + item.id;
                this.nodes.add({id: String(id), label: item.name});
                this.edges.add({from: String(quest.id), to: String(id)})
            });
        }
    }

    removeEdge(id) {
        Object.keys(this.edges.getIds()).forEach(i => {
            let edge = this.edges.get(this.edges.getIds()[i]);
            if (edge.from == id || edge.to == id) {
                this.edges.remove(edge.id);
            }
        });
    }

    removeQuest(quest) {
        this.nodes.remove(String(quest.id));
        this.removeEdge(quest.id);
        if (Boolean(quest.isVariable)) {
            Object.keys(quest.values).forEach(e => {
                let item = quest.values[e];
                let id = quest.id + ';' + item.id;
                this.nodes.remove(String(id));
                this.removeEdge(id);
            });
        }
    }

    afterLoad() {
        this.nodes = this.getNodes();
        this.edges = this.getEdges();

        this.network = new Network(
            this.canvas,
            {nodes: this.nodes, edges: this.edges},
            options);
    }

    getNodes() {
        let data = [
            {id: "start", label: 'Вступление', fixed: true},
            {id: "end", label: 'Результат'}
        ];

        if (this.data.map !== null) {
            let arrIds = this.data.map.nodes.filter(e => !['start', 'end'].includes(e));
            arrIds.forEach(id => {
                if (id.includes(";")) {
                    let info = id.split(";");
                    let quest = this.data.questions[info[0]];
                    data.push({id: String(id), label: quest.values[info[1]].name});
                } else {
                    let quest = this.data.questions[id];
                    this.checkboxList.find('input[name="quest-' + id + '"]').attr('checked', 'checked')
                    data.push({id: String(id), label: quest.name});
                }
            })
        }
        return new DataSet(data);
    }

    getEdges() {
        let data = [];
        if (this.data.map === null) {
            data = [{from: "start", to: "end"}]
        } else {
            data = this.data.map.edges;
        }
        return new DataSet(data);
    }

    loadQuests() {
        $.ajax({
            url: this.pathLoad,
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

    generateMap() {
        return {
            nodes: this.nodes.getIds(),
            edges: this.edges.getIds().map(id => this.edges.get(id))
        };
    }

    saveMap() {
        $.ajax({
            url: this.pathSave,
            type: "POST",
            dataType: "json",
            data: {map: this.generateMap()},
            statusCode: {
                406: (e) => {
                    console.log(e.respose.error);
                }
            }
        });
    }
}