/* eslint-disable */
import $ from "jquery";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

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

  prepareForm();
});
