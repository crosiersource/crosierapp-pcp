/* eslint-disable */
import Moment from "moment";
import routes from "../static/fos_js_routes.json";
import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";

const listId = "#fichaTecnicaList";

Routing.setRoutingData(routes);

function getDatatablesColumns() {
  return [
    {
      name: "e.id",
      data: "e.id",
      title: "Id",
    },
    {
      name: "e.instituicao.nome",
      data: "e.instituicao.nome",
      title: "Instituição",
    },
    {
      name: "e.descricao",
      data: "e.descricao",
      title: "Descrição",
    },
    {
      name: "ta.descricao",
      data: "e.tipoArtigo.descricaoMontada",
      title: "Tipo de Artigo",
    },
    {
      name: "e.updated",
      data: "e",
      title: "",
      render(data, type, row) {
        let colHtml = "";
        colHtml +=
          `<a class="btn btn-info btn-sm" title="Planilha de Insumos" href="${Routing.generate(
            "fichaTecnica_builder",
            { id: data.id }
          )}" role="button">` +
          `<i class="fas fa-th-list"></i>` +
          `</a> `;
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
