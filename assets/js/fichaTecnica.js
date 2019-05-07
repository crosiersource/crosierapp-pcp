'use strict';

import $ from "jquery";

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';


Routing.setRoutingData(routes);

$(document).ready(function () {

    let $instituicao = $('#instituicao');
    let $tipoArtigo = $('#tipoArtigo');
    let $fichaTecnica = $('#fichaTecnica');
    let $divBtns = $('#divBtns');

    let $divInsumosPrecos = $('#divInsumosPrecos');

    $instituicao.on('select2:select', function () {

        $.ajax({
            url: Routing.generate('tipoArtigo_findByInstituicaoId') + '/' + $instituicao.val(),
            dataType: 'json',
            async: false
        }).done(function (result) {
            console.log('change instituicao');
            result.unshift({"id": '', "text": '...'});

            $tipoArtigo.empty().trigger("change");
            $tipoArtigo.val('');
            $fichaTecnica.empty().trigger("change");
            $fichaTecnica.val('');

            $tipoArtigo.select2({
                data: result,
                width: '100%'
            });
            prepareForm();
        });

    });


    $tipoArtigo.on('select2:select', function () {

        $.ajax({
            url: Routing.generate('fichaTecnica_findByInstituicaoIdAndTipoArtigo') + '?instituicaoId=' + $instituicao.val() + '&tipoArtigo=' + $tipoArtigo.val(),
            dataType: 'json',
            async: false
        }).done(function (result) {
            result.unshift({"id": '', "text": '...'})
            $fichaTecnica.empty().trigger("change");

            $fichaTecnica.empty().trigger("change");
            $fichaTecnica.val('');

            $fichaTecnica.select2({
                data: result,
                width: '100%'
            });
            prepareForm();
        });

    });


    $fichaTecnica.on('select2:select', function () {
        window.location.href = Routing.generate('fichaTecnica_builder', {'id': $fichaTecnica.val()});
        // prepareForm(); não precisa, pois vai recarregar e será chamada logo abaixo
    });


    function prepareForm() {
        $tipoArtigo.prop('disabled', $instituicao.val() ? '' : true);
        $fichaTecnica.prop('disabled', $tipoArtigo.val() ? '' : true);
        $divBtns.css('display', $fichaTecnica.val() ? '' : 'none');

        $divInsumosPrecos.css('display', $fichaTecnica.val() ? '' : 'none');
    }

    prepareForm();


});