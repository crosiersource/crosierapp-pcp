/* eslint-disable */
import Moment from "moment";

import Numeral from "numeral";
import "numeral/locales/pt-br.js";

import $ from "jquery";

import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

const listId = "#insumoList";

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
      name: "e.codigo",
      data: "e.codigo",
      title: "Código",
    },
    {
      name: "e.marca",
      data: "e.marca",
      title: "Marca",
    },
    {
      name: "e.descricao",
      data: "e.descricao",
      title: "Descrição",
    },
    {
      name: "ti.descricao",
      data: "e.tipoInsumo.descricaoMontada",
      title: "Tipo de Insumo",
    },
    {
      name: "e.jsonData.preco_custo",
      data: "e.jsonData.preco_custo",
      title: "Custo",
      render(data, type, row) {
        const val = parseFloat(data + 0.0);
        return Numeral(val).format("$ 0.0,[00]");
      },
      className: "text-right",
    },
    {
      name: "e.jsonData.dt_custo",
      data: "e.jsonData.dt_custo",
      title: "Dt Custo",
      render(data, type, row) {
        return Moment(data).format("DD/MM/YYYY");
      },
      className: "text-right",
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
