<template>
  <Header />

  <main class="main">
    <Permit />
    <Log />
    <Popup />
  </main>

  <Footer />
</template>

<script>
import { mapState, mapMutations } from "vuex";

import Header from "./Header";
import Footer from "./Footer";
import Permit from "./Permit";
import Log from "./Log";
import Popup from "./Popup";

export default {
  components: {
    Header,
    Footer,
    Permit,
    Log,
    Popup,
  },

  computed: {
    ...mapState({ popupOpened: state => state.popup.popupOpened }),
  },
};
</script>

<style>
#app {
  box-sizing: border-box;
  margin: 0;
  padding: 0;

  height:100vh;
  display:flex;
  flex-direction:column;
}

.main {
  box-sizing: border-box;
  margin: 0;
  padding: 0 0 20px;
}
</style>
