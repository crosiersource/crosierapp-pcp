/* eslint-disable */
import Moment from "moment";
import "moment/locale/pt-br";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

const listId = "#loteProducaoList";

Routing.setRoutingData(routes);

function getDatatablesColumns() {
  return [
    {
      name: "e.codigo",
      data: "e.codigo",
      title: "Código",
      render(data, type, row) {
        return new String(data).padStart(8, "0");
      },
    },
    {
      name: "e.descricao",
      data: "e.descricao",
      title: "Descrição",
    },
    {
      name: "e.dtLote",
      data: "e.dtLote",
      title: "Dt Lote",
      render(data, type, row) {
        return Moment.utc(data, Moment.ISO_8601, true).format("DD/MM/YYYY");
      },
      className: "text-center",
    },
    {
      name: "e.id",
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
