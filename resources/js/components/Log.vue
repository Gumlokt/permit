<template>
  <section class="log">
    <h3 class="log__title">Журнал пропусков</h3>

    <table class="log__table">
      <th class="log__th">№</th>
      <th class="log__th">Фамилия</th>
      <th class="log__th">Имя</th>
      <th class="log__th">Отчество</th>
      <th class="log__th">Компания</th>
      <th class="log__th">Должность</th>
      <th class="log__th">Действует с</th>
      <th class="log__th">Действует по</th>

      <tr class="log__tr" v-for="permit in permits" v-bind:key="permit.id">
        <td class="log__td">{{ permit.number }}</td>
        <td class="log__td">{{ permit.surname }}</td>
        <td class="log__td">{{ permit.forename }}</td>
        <td class="log__td">{{ permit.patronymic }}</td>
        <td class="log__td">{{ permit.name }}</td>
        <td class="log__td">{{ permit.position }}</td>
        <td class="log__td">{{ permit.start }}</td>
        <td class="log__td">{{ permit.end }}</td>
      </tr>
    </table>
  </section>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  methods: {
    ...mapMutations(['populatePermits']),
  },

  computed: {
    ...mapState(['permits'])
  },

  mounted() {
    this.populatePermits(); // get all permits
  }
};
</script>

<style>
.log {
  margin: 0 auto;
  max-width: 1280px;
  width: 100%;
  display: grid;
  grid-template-columns: 1fr;
}

.log__title {
  padding: 10px 0;
  width: 100%;
  max-width: 1280px;
  text-align: center;
}

.log__table {
  padding: 10px;
  width: 100%;
  max-width: 1280px;
  border-collapse: collapse;
}

.log__th {
  padding: 10px 0;
  background-color: honeydew;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 400;
}

.log__tr {
  padding: 10px 0;
  background-color: lightyellow;
}

.log__td {
  padding: 8px;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;
}
</style>
