<template>
  <Toast class="mt-5" />
  <ConfirmDialog />
  <CrosierListS
    titulo="Insumos"
    apiResource="/api/pcp/insumo"
    ref="crosierListS"
    v-model:selection="this.selection"
    filtrosNaSidebar
  >
    <template v-slot:headerButtons>
      <a role="button" class="ml-1 btn btn-success btn-sm" href="/v/insumo/alteracaoLote"
        ><i class="fas fa-layer-group"></i> Alteração em Lote</a
      >
    </template>
    <template v-slot:filter-fields>
      <CrosierInputText label="Código" v-model="this.filters.codigo" />
      <CrosierInputText label="Descrição" v-model="this.filters.descricao" />
      <CrosierInputText label="Marca" v-model="this.filters.marca" />
      <CrosierDropdownEntity
        v-model="this.filters.tipoInsumo"
        id="tipoInsumo"
        label="Tipo de Insumo"
        optionLabel="descricaoMontada"
        entityUri="/api/pcp/tipoInsumo"
        orderBy="codigo"
      />
    </template>

    <template v-slot:columns>
      <Column selectionMode="multiple" headerStyle="width: 5em"></Column>
      <Column field="id" header="Id" :sortable="true">
        <template #body="r">
          {{ ("00000000" + r.data.id).slice(-8) }}
        </template>
      </Column>
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
              :href="'form?id=' + r.data.id"
              ><i class="fas fa-wrench" aria-hidden="true"></i
            ></a>
            <button
              type="button"
              class="btn btn-danger btn-sm ml-1"
              title="Deletar registro"
              @click="this.deletar(r.data.id)"
            >
              <i class="fas fa-trash" aria-hidden="true"></i>
            </button>
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
import { api, CrosierDropdownEntity, CrosierInputText, CrosierListS } from "crosier-vue";
import Column from "primevue/column";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import moment from "moment";

export default {
  name: "convenio_list",
  components: {
    CrosierListS,
    Column,
    CrosierInputText,
    CrosierDropdownEntity,
    ConfirmDialog,
    Toast,
  },
  data() {
    return {
      selection: [],
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

    deletar(id) {
      this.$confirm.require({
        acceptLabel: "Sim",
        rejectLabel: "Não",
        message: "Confirmar deleção?",
        header: "Atenção!",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
          this.setLoading(true);
          const rsDelete = await api.delete(`/api/pcp/insumo/${id}}`);
          if (rsDelete.status === 204) {
            this.$toast.add({
              severity: "success",
              summary: "Sucesso",
              detail: "Registro deletado com sucesso",
              life: 5000,
            });
            this.$refs.crosierListS.doClearFilters();
          } else {
            this.$toast.add({
              severity: "error",
              summary: "Erro",
              detail: "Ocorreu um erro ao tentar deletar o registro",
              life: 5000,
            });
          }
          this.setLoading(false);
        },
      });
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
