import { createApp } from "vue";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import { createStore } from "vuex";
import { api } from "crosier-vue";
import primevueOptions from "crosier-vue/src/primevue.config.js";
import ConfirmationService from "primevue/confirmationservice";
import Page from "./pages/form";
import "primeflex/primeflex.css";
import "primevue/resources/themes/bootstrap4-light-blue/theme.css"; // theme
import "primevue/resources/primevue.min.css"; // core css
import "primeicons/primeicons.css";

import "crosier-vue/src/yup.locale.pt-br.js";

const app = createApp(Page);

app.use(PrimeVue, primevueOptions);
app.use(ConfirmationService);
app.use(ToastService);

// Create a new store instance.
const store = createStore({
  state() {
    return {
      loading: 0,
      insumo: {},
      insumoErrors: [],
    };
  },
  getters: {
    isLoading(state) {
      return state.loading > 0;
    },
    getInsumo(state) {
      const { insumo } = state;
      return insumo;
    },
    getInsumoErrors(state) {
      const { insumoErrors } = state;
      return insumoErrors;
    },
  },
  mutations: {
    setLoading(state, loading) {
      if (loading) {
        state.loading++;
      } else {
        state.loading--;
      }
    },

    setInsumo(state, insumo) {
      state.insumo = insumo;
    },

    setInsumoErrors(state, formErrors) {
      state.insumoErrors = formErrors;
    },
  },

  actions: {
    async loadData(context) {
      context.commit("setLoading", true);
      const id = new URLSearchParams(window.location.search.substring(1)).get("id");
      if (id) {
        try {
          const response = await api.get({
            apiResource: `/api/clin/insumo/${id}}`,
          });

          if (response.data["@id"]) {
            context.commit("setInsumo", response.data);
          } else {
            console.error("Id não encontrado");
          }
        } catch (err) {
          console.error(err);
        }
      }
      context.commit("setLoading", false);
    },
  },
});

app.use(store);

app.mount("#app");
