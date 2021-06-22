<template>
  <section class="log">
    <h3 class="log__title">Журнал пропусков</h3>

    <table class="log__table">
      <tr class="log__trh">
        <th class="log__th">№ пропуска</th>
        <th class="log__th">Фамилия</th>
        <th class="log__th">Имя</th>
        <th class="log__th">Отчество</th>
        <th class="log__th">Компания</th>
        <th class="log__th">Должность</th>
        <th class="log__th">Действует с</th>
        <th class="log__th">Действует по</th>
      </tr>

      <tr class="log__tr" v-for="permit in storedPermits" v-bind:key="permit.id">
        <td class="log__td">{{ permit.number }}</td>
        <td class="log__td">{{ permit.surname }}</td>
        <td class="log__td">{{ permit.forename }}</td>
        <td class="log__td">{{ permit.patronymic }}</td>
        <td class="log__td">{{ permit.company }}</td>
        <td class="log__td">{{ permit.position }}</td>
        <td class="log__td">{{ formatDate(permit.dateStart) }}</td>
        <td class="log__td">{{ formatDate(permit.dateEnd) }}</td>
      </tr>
    </table>
  </section>
</template>

<script>
import { mapState } from "vuex";

export default {
  methods: {
    formatDate(dateString) {
      let date = new Date(dateString);
      let day = String(date.getDate()).padStart(2, '0');
      let month = String(date.getMonth() + 1).padStart(2, '0'); // month number is an index number which is zere based, that's why +1 needed
      let year = date.getFullYear();

      return `${day}.${month}.${year}`;
    },
  },

  computed: {
    ...mapState(['storedPermits'])
  },
};
</script>

<style>
.log {
  display: grid;
  grid-template-columns: 1fr;
  justify-items: center;
}

.log__title {
  padding: 10px 0;
}

.log__table {
  width: 100%;
  max-width: 90%;
  border-collapse: collapse;
}

.log__trh {
  background-color: honeydew;
}

.log__trh:hover {
  background-color: rgb(227, 255, 227);
}

.log__th {
  padding: 10px 0;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 400;
}

.log__tr {
  padding: 10px 0;
  background-color: lightyellow;
}

.log__tr:hover {
  background-color: lemonchiffon;
}

.log__td {
  padding: 8px;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;
}

@media screen and (max-width: 1420px) {
  .log__table {
    max-width: 98%;
  }
}

@media screen and (min-width: 2000px) {
  .log__table {
    max-width: 70%;
  }
}
</style>
