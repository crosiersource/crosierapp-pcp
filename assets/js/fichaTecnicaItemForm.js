/* eslint-disable */
import $ from "jquery";

import Numeral from "numeral";
import "numeral/locales/pt-br.js";

Numeral.locale("pt-br");

window.igualarValores = function () {
  const $campos = $('[name^="ficha_tecnica_item_qtde"]');

  $campos.sort(function (a, b) {
    return Numeral($(b).val()).value() > Numeral($(a).val()).value() ? 1 : -1;
  });

  $.each($campos, function (index, campo) {
    $(campo).val($($campos[0]).val());
  });
};

window.zerarValores = function () {
  const $campos = $('[name^="ficha_tecnica_item_qtde"]');

  $.each($campos, function (index, campo) {
    $(campo).val(0);
  });

  CrosierMasks.maskAll();
};
