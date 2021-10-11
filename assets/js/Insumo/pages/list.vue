<template>
  <CrosierListS titulo="Insumos" apiResource="/api/pcp/insumo" :formUrl="this.formUrl">
    <template v-slot:headerButtons>
      <a role="button" class="ml-1 btn btn-success btn-sm" href="/pcp/insumo/alteracaoLote"
        >Alteração em Lote</a
      >
    </template>
    <template v-slot:filter-fields>
      <div class="form-row">
        <div class="col-md-2 form-group">
          <label for="id">Código</label>
          <InputText class="form-control" id="id" type="text" v-model="this.filters.codigo" />
        </div>
        <div class="col-md-4 form-group">
          <label for="nome">Descrição</label>
          <InputText class="form-control" id="nome" type="text" v-model="this.filters.descricao" />
        </div>
        <div class="col-md-3 form-group">
          <label for="nome">Marca</label>
          <InputText class="form-control" id="nome" type="text" v-model="this.filters.marca" />
        </div>
        <CrosierDropdownEntity
          v-model="this.filters.tipoInsumo"
          id="tipoInsumo"
          label="Tipo de Insumo"
          col="3"
          optionLabel="descricaoMontada"
          entityUri="/api/pcp/tipoInsumo"
          orderBy="codigo"
        />
      </div>
    </template>

    <template v-slot:columns>
      <Column selectionMode="multiple" headerStyle="width: 5em"></Column>
      <Column field="id" header="Id" :sortable="true"></Column>
      <Column field="codigo" header="Código" :sortable="true"></Column>
      <Column field="marca" header="Marca" :sortable="true"></Column>
      <Column field="descricao" header="Descrição" :sortable="true"></Column>
      <Column field="tipoInsumo.codigo" header="Tipo" :sortable="true">
        <template #body="r">
          {{ r.data.tipoInsumo.descricaoMontada }}
        </template>
      </Column>
      <Column field="precoCusto" header="Preço Custo" :sortable="true">
        <template #body="r">
          <div class="text-right">
            {{
              r.data.precoCusto.toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL",
              })
            }}
          </div>
        </template>
      </Column>
      <Column field="dtCusto" header="Dt Custo" :sortable="true">
        <template #body="r">
          <div class="text-center">
            {{ this.moment(r.data.dtCusto).format("DD/MM/YYYY") }}
          </div>
        </template>
      </Column>
      <Column field="updated" header="" :sortable="true">
        <template class="text-right" #body="r">
          <div class="d-flex justify-content-end">
            <a
              role="button"
              class="btn btn-primary btn-sm"
              title="Editar registro"
              :href="this.formUrl + '?id=' + r.data.id"
              ><i class="fas fa-wrench" aria-hidden="true"></i
            ></a>
          </div>
          <div class="d-flex justify-content-end mt-1">
            <span
              v-if="r.data.updated"
              class="badge badge-info"
              title="Última alteração do registro"
            >
              {{ new Date(r.data.updated).toLocaleString() }}
            </span>
          </div>
        </template>
      </Column>
    </template>
  </CrosierListS>
</template>

<script>
import { mapGetters, mapMutations } from "vuex";
import { CrosierListS, CrosierDropdownEntity } from "crosier-vue";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import moment from "moment";

export default {
  name: "convenio_list",
  components: {
    CrosierListS,
    Column,
    InputText,
    CrosierDropdownEntity,
  },
  data() {
    return {
      formUrl: "/pcp/insumo/form",
      dropdownOptions: {
        statusOptions: [
          { name: "Ativo", value: true },
          { name: "Inativo", value: false },
        ],
      },
    };
  },

  methods: {
    ...mapMutations(["setLoading"]),

    moment(date) {
      return moment(date);
    },
  },

  computed: {
    ...mapGetters({ filters: "getFilters" }),
  },
};
</script>

<style>
.dt-sm-bt {
  height: 30px !important;
  width: 30px !important;
}
</style>
