<template>
  <Toast class="mt-5" />
  <CrosierFormS
    listUrl="/pcp/insumo/list"
    formUrl="/pcp/insumo/form"
    @submitForm="this.submitForm"
    titulo="Insumo"
  >
    <div class="form-row">
      <CrosierInputInt id="id" label="Id" v-model="this.insumo.id" :disabled="true" col="2" />

      <CrosierInputInt id="codigo" label="Código" v-model="this.insumo.codigo" col="2" />

      <CrosierInputText
        id="descricao"
        label="Descrição"
        col="5"
        v-model="this.insumo.descricao"
        :error="this.formErrors.descricao"
      />

      <CrosierDropdownEntity
        col="3"
        v-model="this.insumo.tipoInsumo"
        entity-uri="/api/pcp/tipoInsumo"
        optionLabel="descricaoMontada"
        :error="this.formErrors.tipoInsumo"
        :optionValue="null"
        :orderBy="{ codigo: 'ASC' }"
        label="Tipo de Insumo"
        id="tipoInsumo"
      />
    </div>

    <div class="form-row">
      <CrosierInputText
        id="marca"
        label="Marca"
        col="3"
        v-model="this.insumo.marca"
        :error="this.formErrors.marca"
      />

      <CrosierDropdownEntity
        col="3"
        v-model="this.insumo.unidadeProdutoId"
        :error="this.formErrors.unidadeProdutoId"
        entity-uri="/api/est/unidade"
        optionLabel="label"
        optionValue="id"
        :orderBy="{ codigo: 'ASC' }"
        label="Unidade"
        id="unidade"
      />

      <CrosierCurrency
        col="3"
        v-model="this.insumo.precoCusto"
        :error="this.formErrors.precoCusto"
        label="Preço Custo"
        id="precoCusto"
      />

      <CrosierCalendar col="3" v-model="this.insumo.dtCusto" label="Dt Custo" id="dtCusto" />
    </div>
  </CrosierFormS>
</template>

<script>
import Toast from "primevue/toast";
import * as yup from "yup";
import {
  CrosierCalendar,
  CrosierCurrency,
  CrosierDropdown,
  CrosierDropdownEntity,
  CrosierFormS,
  CrosierInputInt,
  CrosierInputText,
  submitForm,
} from "crosier-vue";
import { mapGetters, mapMutations } from "vuex";

export default {
  name: "insumo_form",
  components: {
    Toast,
    CrosierFormS,
    CrosierInputText,
    CrosierInputInt,
    CrosierDropdownEntity,
    CrosierCurrency,
    CrosierCalendar,
  },

  data() {
    return {
      criarVincularCarteira: false,
      schemaValidator: {},
      validDate: new Date(),
    };
  },

  async mounted() {
    this.setLoading(true);

    this.$store.dispatch("loadData");
    this.schemaValidator = yup.object().shape({
      descricao: yup.string().required().typeError(),
      tipoInsumo: yup.mixed().required().typeError(),
      unidadeProdutoId: yup.number().required().typeError(),
      marca: yup.string().required().typeError(),
      precoCusto: yup.number().required().typeError(),
    });

    document.getElementById("descricao").focus();

    document.getElementsByTagName("input").forEach(function doff(i) {
      i.autocomplete = "off";
    });

    this.setLoading(false);
  },

  methods: {
    ...mapMutations(["setLoading", "setInsumo", "setInsumoErrors"]),

    async submitForm() {
      this.setLoading(true);
      await submitForm({
        apiResource: "/api/pcp/insumo",
        schemaValidator: this.schemaValidator,
        $store: this.$store,
        formDataStateName: "insumo",
        $toast: this.$toast,
        fnBeforeSave: (formData) => {
          formData.tipoInsumo = formData?.tipoInsumo["@id"] ?? null;
          delete formData.precoAtual;
          delete formData.precos;
        },
      });
      this.setLoading(false);
    },
  },

  computed: {
    ...mapGetters({ insumo: "getInsumo", formErrors: "getInsumoErrors" }),
  },
};
</script>
