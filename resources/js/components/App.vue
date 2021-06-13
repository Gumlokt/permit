<template>
  <Header />

  <main class="main">
    <Permit />
  </main>
</template>

<script>
import Header from "./Header";
import Permit from "./Permit";

export default {
  components: {
    Header,
    Permit
  }
};
</script>

<style>
.main {
  margin: 0 auto;
  max-width: 1280px;
}
</style>
