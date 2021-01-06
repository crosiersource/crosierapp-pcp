'use strict';

let listId = "#insumoList";

import Moment from 'moment';

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
Numeral.locale('pt-br');

import $ from "jquery";

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigo',
            data: 'e.codigo',
            title: 'Código'
        },
        {
            name: 'e.descricao',
            data: 'e.descricao',
            title: 'Descrição'
        },
        {
            name: 'ti.descricao',
            data: 'e.tipoInsumo.descricaoMontada',
            title: 'Tipo de Insumo'
        },
        {
            name: 'e.precoCusto',
            data: 'e.precoCusto',
            title: 'Custo',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return 'R$ ' + Numeral(val).format('0.0,[00]');
            },
            className: 'text-right'
        },
        {
            name: 'e.jsonData.dt_custo',
            data: 'e.jsonData.dt_custo',
            title: 'Dt Custo',
            render: function (data, type, row) {
                return Moment(data).format('DD/MM/YYYY');
            },
            className: 'text-right'
        },
        {
            name: 'e.updated',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                if ($(listId).data('routeedit')) {
                    let routeedit = Routing.generate($(listId).data('routeedit'), {id: data.id});
                    colHtml += DatatablesJs.makeEditButton(routeedit);
                }
                if ($(listId).data('routedelete')) {
                    let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
                    let csrfTokenDelete = $(listId).data('crsf-token-delete');
                    colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
                }
                colHtml += '<br /><span class="badge badge-pill badge-info">' + Moment(data.updated).format('DD/MM/YYYY HH:mm:ss') + '</span> ';
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());
