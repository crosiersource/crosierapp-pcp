/* eslint-disable */
import $ from "jquery";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

Routing.setRoutingData(routes);

$(document).ready(function () {
  const $selTodosLoteItens = $("#selTodosLoteItens");

  const $btnModalOpcoesRelatorios = $("#btnModalOpcoesRelatorios");
  // hidden dentro do modal
  const $loteItens = $("#loteItens");

  $selTodosLoteItens.click(function () {
    $('[id^="loteItem"]').not(this).prop("checked", this.checked);
  });

  $btnModalOpcoesRelatorios.click(function () {
    let ids = "";
    $.each($('[id^="loteItem"]'), function () {
      ids += $(this).prop("checked") ? `${$(this).val()},` : "";
    });
    $loteItens.val(ids.substr(0, ids.length - 1));
  });
});
