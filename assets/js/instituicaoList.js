/* eslint-disable */
import Moment from "moment";

import Numeral from "numeral";
import "numeral/locales/pt-br.js";
import $ from "jquery";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

const listId = "#instituicaoList";

Numeral.locale("pt-br");

Routing.setRoutingData(routes);

function getDatatablesColumns() {
  return [
    {
      name: "e.id",
      data: "e.id",
      title: "Id",
    },
    {
      name: "e.documento",
      data: "e.documento",
      title: "CPF/CNPJ",
      render(data, type, row) {
        return data
          ? data.length === 14
            ? data.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "$1.$2.$3/$4-$5")
            : data.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "$1.$2.$3-$4")
          : "";
      },
    },
    {
      name: "e.nome",
      data: "e.nome",
      title: "Nome",
    },
    {
      name: "e.updated",
      data: "e",
      title: "",
      render(data, type, row) {
        let colHtml = "";
        if ($(listId).data("routeedit")) {
          const routeedit = Routing.generate($(listId).data("routeedit"), { id: data.id });
          colHtml += DatatablesJs.makeEditButton(routeedit);
        }
        if ($(listId).data("routedelete")) {
          const deleteUrl = Routing.generate($(listId).data("routedelete"), { id: data.id });
          const csrfTokenDelete = $(listId).data("crsf-token-delete");
          colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
        }
        colHtml += `<br /><span class="badge badge-pill badge-info">${Moment(data.updated).format(
          "DD/MM/YYYY HH:mm:ss"
        )}</span> `;
        return colHtml;
      },
      className: "text-right",
    },
  ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());
