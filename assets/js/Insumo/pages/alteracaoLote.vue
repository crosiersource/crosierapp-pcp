<template>
  <Toast class="mt-5" />
  <CrosierBlock :loading="this.loading" />
  <ConfirmDialog />

  <div class="container">
    <div class="card" style="margin-bottom: 50px">
      <div class="card-header">
        <div class="d-flex flex-wrap align-items-center">
          <div class="mr-1">
            <h3>Insumos</h3>
            <h6>Alteração em Lote</h6>
          </div>
          <div class="d-sm-flex flex-nowrap ml-auto">
            <a
              role="button"
              class="btn btn-outline-secondary"
              href="/pcp/insumo/list"
              title="Listar"
            >
              <i class="fas fa-list"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <form @submit.prevent="this.$emit('submitForm')">
          <fieldset :disabled="this.loading">
            <div class="form-row mt-3">
              <CrosierPercent
                col="3"
                v-model="this.porcentValor"
                @input="this.changePorcentValor"
                id="porcentValor"
                label="Inc/Dec Preço Custo"
              />
            </div>
            <div class="row mt-3">
              <div class="col text-right">
                <button
                  type="submit"
                  class="btn btn-danger btn-sm"
                  style="width: 12rem"
                  @click="this.submitForm"
                >
                  <i class="fas fa-save"></i> Alterar todos
                </button>
              </div>
            </div>
          </fieldset>
        </form>

        <DataTable :value="this.registros" class="mt-4">
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
                <span
                  :style="
                    this.porcentValor &&
                    r.data?.precoCusto_novo &&
                    r.data.precoCusto_novo !== r.data.precoCusto
                      ? 'text-decoration: line-through'
                      : ''
                  "
                >
                  {{
                    r.data.precoCusto.toLocaleString("pt-BR", {
                      style: "currency",
                      currency: "BRL",
                    })
                  }}
                </span>
                <span
                  v-if="
                    this.porcentValor &&
                    r.data?.precoCusto_novo &&
                    r.data?.precoCusto_novo !== r.data.precoCusto
                  "
                  ><br /><b>
                    {{
                      r.data.precoCusto_novo.toLocaleString("pt-BR", {
                        style: "currency",
                        currency: "BRL",
                      })
                    }}</b
                  >
                </span>
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
        </DataTable>
      </div>
    </div>
  </div>
</template>

<script>
import Toast from "primevue/toast";
import DataTable from "primevue/datatable";
import ConfirmDialog from "primevue/confirmdialog";
import Column from "primevue/column";
import { mapGetters, mapMutations } from "vuex";
import moment from "moment";
import { api, CrosierPercent } from "crosier-vue";

export default {
  name: "alteracaoLote",
  components: {
    Toast,
    DataTable,
    Column,
    CrosierPercent,
    ConfirmDialog,
  },

  data() {
    return {
      registros: [],
      schemaValidator: {},
      validDate: new Date(),
      porcentValor: 0,
    };
  },

  async mounted() {
    this.setLoading(true);

    this.loadRegistros();

    // document.getElementById("nome").focus();

    document.getElementsByTagName("input").forEach(function doff(i) {
      i.autocomplete = "off";
    });

    this.setLoading(false);
  },

  methods: {
    ...mapMutations(["setLoading"]),

    moment(date) {
      return moment(date);
    },

    loadRegistros() {
      if (!localStorage.getItem("dt-state/api/pcp/insumo")) {
        window.location = "/pcp/insumo/list?err=semRegistrosAlteracaoLote";
      }

      const ls = JSON.parse(localStorage.getItem("dt-state/api/pcp/insumo"));

      this.registros = ls.selection;
    },

    changePorcentValor() {
      this.registros.forEach((r) => {
        const precisao = 1;
        const pCusto = (this.porcentValor / 100 + 1) * r.precoCusto * 10 ** precisao;
        r.precoCusto_novo = Math.round(pCusto) / 10 ** precisao;
      });
    },

    async submitForm() {
      this.$confirm.require({
        acceptLabel: "Sim",
        rejectLabel: "Não",
        message: "Confirmar alterações?",
        header: "Atenção!",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
          this.setLoading(true);
          await Promise.all(
            this.registros.map(async (r) => {
              if (r?.precoCusto_novo) {
                await api.put(
                  r["@id"],
                  JSON.stringify({
                    precoCusto: r?.precoCusto_novo,
                  })
                );
              }
            })
          );
          localStorage.removeItem("dt-state/api/pcp/insumo");
          window.location = "/pcp/insumo/list";
          this.setLoading(false);
        },
      });
    },
  },

  computed: {
    ...mapGetters({ loading: "isLoading" }),
  },
};
</script>
