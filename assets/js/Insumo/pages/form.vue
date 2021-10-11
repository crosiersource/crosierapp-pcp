<template>
  <Toast class="mt-5" />
  <CrosierFormS
    listUrl="/clin/insumo/list"
    formUrl="/clin/insumo/form"
    @submitForm="this.submitForm"
    titulo="Convênio"
  >
    <div class="form-row">
      <div class="col-md-3">
        <label for="id">ID</label>
        <InputText class="form-control" id="id" type="text" v-model="this.insumo.id" disabled />
      </div>
      <div class="col-md-9">
        <label for="name">Descrição</label>
        <InputText
          :class="'form-control ' + (this.formErrors['descricao'] ? 'is-invalid' : '')"
          id="nome"
          type="text"
          v-model="this.insumo.descricao"
        />
        <div class="invalid-feedback">
          {{ this.formErrors["descricao"] }}
        </div>
      </div>
    </div>
  </CrosierFormS>
</template>

<script>
import Toast from "primevue/toast";
import InputText from "primevue/inputtext";
import * as yup from "yup";
import { CrosierFormS, submitForm } from "crosier-vue";
import { mapGetters, mapMutations } from "vuex";

export default {
  name: "insumo_form",
  components: {
    Toast,
    CrosierFormS,
    InputText,
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
      nome: yup.string().required().typeError(),
      prefixo: yup.string().required().typeError(),
      ativo: yup.boolean().required().typeError(),
    });

    document.getElementById("nome").focus();

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
        apiResource: "/api/clin/insumo",
        schemaValidator: this.schemaValidator,
        $store: this.$store,
        formDataStateName: "insumo",
        $toast: this.$toast,
        fnBeforeSave: (formData) => {
          const jsonData = { ...formData.jsonData };
          jsonData.criarVincularCarteira = this.criarVincularCarteira;
          formData.jsonData = jsonData;
          if (formData?.carteira) {
            formData.carteira = formData?.carteira["@id"] ?? null;
          }
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
