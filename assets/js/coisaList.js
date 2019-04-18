'use strict';

let listId = "#coisaList";

import DatatablesJs from './crosier/DatatablesJs';

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

import Moment from 'moment';

Routing.setRoutingData(routes)

function getDatatablesColumns() {
    return [
        {
            name: 'e.ordem',
            data: 'e.ordem',
            title: 'Ordem'
        },
        {
            name: 'e.nome',
            data: 'e.nome',
            title: 'Nome'
        },
        {
            name: 'e.obs',
            data: 'e.obs',
            title: 'Obs'
        },
        {
            name: 'e.dtCoisa',
            data: 'e.dtCoisa',
            title: 'Data',
            render: function (data, type, row) {
                return Moment.unix(data.timestamp).format('DD/MM/YYYY');
            },
            className: 'text-center'
        },
        {
            name: 'e.importante',
            data: 'e.importante',
            title: 'Importante',
            render: function (data, type, row) {
                return data ? 'Sim' : 'Não';
            },
            className: 'text-center'
        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                if ($(listId).data('routeedit')) {
                    let routeedit = $(listId).data('routeedit');
                    let editUrl = routeedit + '/' + data.id;
                    colHtml += DatatablesJs.makeEditButton(editUrl);
                }
                if ($(listId).data('routedelete')) {
                    let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
                    let csrfTokenDelete = $(listId).data('crsf-token-delete');
                    colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
                }
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());