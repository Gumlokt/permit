<template>
  <Header />

  <main class="main">
    <Permit />
  </main>

  <Log />

  <button @click="openPopup">Open Popup</button>
  <Popup />
</template>

<script>
import { mapState, mapMutations } from "vuex";

import Header from "./Header";
import Permit from "./Permit";
import Log from "./Log";
import Popup from "./Popup";

export default {
  components: {
    Header,
    Permit,
    Log,
    Popup,
  },

  methods: {
    ...mapMutations('popup', ['openPopup']),
  },

  computed: {
    ...mapState({ popupOpened: state => state.popup.popupOpened }),
  },
};
</script>

<style>
.main {
  margin: 0 auto;
  max-width: 1280px;
}

@media screen and (max-width: 1420px) {
  .main {
    padding: 0 10px;
  }
}
</style>
