'use strict';

import $ from "jquery";

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

$(document).ready(function () {

    let $selTodosLoteItens = $('#selTodosLoteItens');

    let $btnModalOpcoesRelatorios = $('#btnModalOpcoesRelatorios');
    // hidden dentro do modal
    let $loteItens = $('#loteItens');

    $selTodosLoteItens.click(function () {
        $('[id^="loteItem"]').not(this).prop('checked', this.checked);
    });

    $btnModalOpcoesRelatorios.click(function () {
        let ids = '';
        $.each($('[id^="loteItem"]'), function () {
            ids += $(this).prop('checked') ? $(this).val() + ',' : '';
        });
        $loteItens.val(ids.substr(0, ids.length - 1));
    });

});
