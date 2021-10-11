<template>
  <div :class="'col-md-' + this.col">
    <div class="form-group">
      <label :for="this.id">{{ label }}</label>
      <div class="input-group">
        <InputNumber
          :class="'form-control ' + (this.error ? 'is-invalid' : '')"
          inputClass="text-right"
          :id="this.id"
          mode="decimal"
          :minFractionDigits="this.decimais"
          :maxFractionDigits="this.decimais"
          :modelValue="modelValue"
          @input="this.onInput"
          placeholder="0,00"
          :disabled="this.disabled"
        />
        <div class="input-group-append">
          <span class="input-group-text">%</span>
        </div>
      </div>
      <small v-if="this.helpText" :id="this.id + '_help'" class="form-text text-muted">{{
        this.helpText
      }}</small>
      <div class="invalid-feedbackk blink" v-show="this.error">
        {{ this.error }}
      </div>
    </div>
  </div>
</template>

<script>
import InputNumber from "primevue/inputnumber";

export default {
  components: {
    InputNumber,
  },

  emits: ["update:modelValue", "input"],

  props: {
    modelValue: {
      type: Number,
    },
    id: {
      type: String,
      required: true,
    },
    error: {
      type: String,
      required: false,
      default: null,
    },
    col: {
      type: String,
      required: false,
      default: "12",
    },
    label: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    helpText: {
      type: String,
      required: false,
    },
    decimais: {
      type: Number,
      required: false,
      default: 2,
    },
  },

  methods: {
    onInput($event) {
      this.$emit("update:modelValue", $event.value);
      this.$emit("input", $event);
    },
  },
};
</script>
<style scoped>
.invalid-feedbackk {
  width: 100%;
  margin-top: 0.25rem;
  font-size: 80%;
  color: #e55353;
}
</style>
