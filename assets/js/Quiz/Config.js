const locales = {
    en: {
        edit: 'Редактировать',
        del: 'Удалить',
        back: 'Назад',
        addNode: 'НЕ РАБОТАЕТ',
        addEdge: 'Добавить связь',
        editNode: 'Редактировать вершину',
        editEdge: 'Редактировать ребро',
        addDescription: 'Сделайте клик в пустой области для вопроса',
        edgeDescription: 'Кликините по вопросу(варианту ответа) и тяните к следующему вопросу',
        editEdgeDescription: 'Вы пытаетесь изменить связь! Скорее всего Вы что-то делаете не так',
        createEdgeError: 'Cannot link edges to a cluster.',
        deleteClusterError: 'Clusters cannot be deleted.',
        editClusterError: 'Clusters cannot be edited.'
    }
}

const manipulation = {
    enabled: true,
    addNode: false,
    initiallyActive: true,
};

const options = {
    edges: {
        arrows: 'to',
    },
    nodes: {
        shape: "box",
        font: {
            size: 20,
            bold: {
                color: "#0077aa",
            },
        },
    },
    layout: {
        hierarchical: {
            edgeMinimization: true,
            parentCentralization: true,
            direction: 'UD',
            sortMethod: 'directed',
        },
    },
    interaction: {hover: true},
    manipulation: manipulation,
    locales: locales,

};

export default options;