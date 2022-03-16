<template>
  <page class="page">
    <h1>{{ storedPermits[0].surname }}</h1>
    <h3>{{ test }}</h3>
  </page>
</template>

<script>
import { mapState } from "vuex";

export default {
  data() {
    return {
      test: 'printer page...',
    }
  },

  computed: {
    ...mapState({ storedPermits: state => state.permit.storedPermits }),
  },
};
</script>

<style>
.page {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
</style>
