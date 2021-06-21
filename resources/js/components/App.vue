<template>
  <Header />

  <main class="main">
    <Permit />
  </main>

  <Log />
</template>

<script>
import Header from "./Header";
import Permit from "./Permit";
import Log from "./Log";

export default {
  components: {
    Header,
    Permit,
    Log
  }
};
</script>

<style>
.main {
  margin: 0 auto;
  max-width: 1280px;
}
</style>
