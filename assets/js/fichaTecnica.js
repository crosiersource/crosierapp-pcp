/* eslint-disable */
import $ from "jquery";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

import Sortable from 'sortablejs';

import 'blueimp-file-upload';

import toastrr from "toastr";

Routing.setRoutingData(routes);

$(document).ready(function () {
  const $cliente = $("#cliente");
  const $tipoArtigo = $("#tipoArtigo");
  const $fichaTecnica = $("#fichaTecnica");
  const $divBtns = $("#divBtns");

  const $divInsumosPrecos = $("#divInsumosPrecos");

  $cliente.on("select2:select", function () {
    $.ajax({
      url: `${Routing.generate("tipoArtigo_findByClienteId")}/${$cliente.val()}`,
      dataType: "json",
      async: false,
    }).done(function (result) {
      console.log("change cliente");
      result.unshift({ id: "", text: "..." });

      $tipoArtigo.empty().trigger("change");
      $tipoArtigo.val("");
      $fichaTecnica.empty().trigger("change");
      $fichaTecnica.val("");

      $tipoArtigo.select2({
        data: result,
        width: "100%",
      });
      prepareForm();
    });
  });

  $tipoArtigo.on("select2:select", function () {
    $.ajax({
      url: `${Routing.generate(
        "fichaTecnica_findByClienteIdAndTipoArtigo"
      )}?clienteId=${$cliente.val()}&tipoArtigo=${$tipoArtigo.val()}`,
      dataType: "json",
      async: false,
    }).done(function (result) {
      result.unshift({ id: "", text: "..." });
      $fichaTecnica.empty().trigger("change");

      $fichaTecnica.empty().trigger("change");
      $fichaTecnica.val("");

      $fichaTecnica.select2({
        data: result,
        width: "100%",
      });
      prepareForm();
    });
  });

  $fichaTecnica.on("select2:select", function () {
    window.location.href = Routing.generate("fichaTecnica_builder", { id: $fichaTecnica.val() });
    // prepareForm(); não precisa, pois vai recarregar e será chamada logo abaixo
  });

  function prepareForm() {
    $tipoArtigo.prop("disabled", $cliente.val() ? "" : true);
    $fichaTecnica.prop("disabled", $tipoArtigo.val() ? "" : true);
    $divBtns.css("display", $fichaTecnica.val() ? "" : "none");

    $divInsumosPrecos.css("display", $fichaTecnica.val() ? "" : "none");
  }


  let $imageFile = $('#fichaTecnica_imagem_imageFile');

  $imageFile.on('change', function () {
    //get the file name
    let fileName = $(this).val().split('\\').pop();
    //replace the "Choose a file" label
    $(this).next('.custom-file-label').html(fileName);
  });

  console.log("oi estou aqui");
  
  $imageFile.fileupload({
    dataType: 'json',
    singleFileUploads: false,
    add: function (e, data) {
      data.submit();
    },
    success: function (result, textStatus, jqXHR) {
      $('#filesUl').html(result.filesUl);
      createUlFotosSortable();
      toastrr.success('Imagem salva com sucesso');
    },
    fail: function (result, textStatus, jqXHR) {
      toastrr.error('Erro ao salvar imagem');
    },
    done: function (e, data) {
      $.each(data.result.files, function (index, file) {
        $('<p/>').text(file.name).appendTo(document.body);
      });
    }
  });


  function createUlFotosSortable() {
    Sortable.create(ulFotosSortable,
      {
        animation: 150,
        onEnd:
          function (/**Event*/evt) {
            let ids = '';
            $('#ulFotosSortable > li').each(function () {
              ids += $(this).data('id') + ',';
            });

            $.ajax({
                dataType: "json",
                data: {'ids': ids},
                url: Routing.generate('prod_fichaTecnica_formImagemSaveOrdem'),
                type: 'POST'
              }
            ).done(function (data) {
              if (data.result === 'OK') {
                toastrr.success('Fotos ordenadas com sucesso');

                $.each(data.ids, function (id, ordem) {
                  $('#ulFotosSortable > li[data-id="' + id + '"] > div > div > label > span.ordem').html(ordem);
                });

              } else {
                toastrr.error('Erro ao ordenar itens');
              }
            });
          }

      });
  }

  if ($('#ulFotosSortable').length) {
    createUlFotosSortable();
  }


  prepareForm();
});
