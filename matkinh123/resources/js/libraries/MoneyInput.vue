<template>
  <input type="string" v-model="model" class="form-control" :id="id" :name="name" :placeholder="placeholder">
</template>

<script>

export default {
  name: 'input-number',
  props: {
      value: {
        type: [String, Number],
        required: true,
      },
      string: {
        type: String,
        default: 'text'
      },
      id: {
        type: [String, Number],
        required: false
      },
      name: {
        type: String,
        required: false
      },
      placeholder: {
        type: String,
        required: false
      },
    },

    computed: {
      model: {
        get() {
          // If is not number then return ''
          if (isNaN(this.value) || this.value == '0') {
            return 0;
          }

          let value = this.value.split(".");
          let decimal = typeof value[1] !== "undefined"
            ? "." + value[1]
            : "";
          let num = Number(value[0]).toLocaleString("en-GB");

          if (num == 0 && decimal == '') {
            return 0;
          }

          return num + decimal;
        },

        set(newValue) {
          this.$emit("input", newValue.replace(/,/g, ""));
        }
      }
    }
}
</script>
