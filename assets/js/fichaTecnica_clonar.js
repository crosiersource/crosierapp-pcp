'use strict';

import $ from "jquery";

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


Routing.setRoutingData(routes);

$(document).ready(function () {

    let $instituicao = $('#instituicao');
    let $tipoArtigo = $('#tipoArtigo');
    let $descricao = $('#descricao');

    $instituicao.on('select2:select', function () {
        $descricao.val($tipoArtigo.val() + ' - ' + $instituicao.select2('data')[0]['text'])
    });




});