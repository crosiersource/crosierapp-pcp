/* eslint-disable */
import Moment from "moment";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

const listId = "#tipoArtigoList";

Routing.setRoutingData(routes);

function getDatatablesColumns() {
  return [
    {
      name: "e.codigo",
      data: "e.codigo",
      title: "Código",
    },
    {
      name: "e.descricao",
      data: "e.descricao",
      title: "Descrição",
    },
    {
      name: "e.updated",
      data: "e",
      title: "",
      render(data, type, row) {
        let colHtml = "";
        colHtml += `<span class="badge badge-pill badge-info">${Moment(data.updated).format(
          "DD/MM/YYYY HH:mm:ss"
        )}</span> `;
        if ($(listId).data("routeedit")) {
          const routeedit = Routing.generate($(listId).data("routeedit"), { id: data.id });
          colHtml += DatatablesJs.makeEditButton(routeedit);
        }
        if ($(listId).data("routedelete")) {
          const deleteUrl = Routing.generate($(listId).data("routedelete"), { id: data.id });
          const csrfTokenDelete = $(listId).data("crsf-token-delete");
          colHtml += `${DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete)} `;
        }
        return colHtml;
      },
      className: "text-right",
    },
  ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());
