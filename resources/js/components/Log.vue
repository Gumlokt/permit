<template>
  <section class="log">
    <h3 class="log__title">Журнал пропусков</h3>

    <table class="log__table">
      <tr class="log__trh">
        <th class="log__th">№ пропуска</th>
        <th class="log__th">Фамилия</th>
        <th class="log__th">Имя</th>
        <th class="log__th">Отчество</th>
        <th class="log__th">Должность</th>
        <th class="log__th">Компания</th>
        <th class="log__th">Действует с</th>
        <th class="log__th">Действует по</th>
      </tr>

      <tr class="log__tr" v-for="permit in storedPermits" v-bind:key="permit.id" v-bind:class="{ 'log__tr_upcoming': permit.dateStart > curTime, 'log__tr_expired': curTime > permit.dateEnd }">
        <td class="log__td">{{ permit.number }}</td>
        <td class="log__td">{{ permit.surname }}</td>
        <td class="log__td">{{ permit.forename }}</td>
        <td class="log__td">{{ permit.patronymic }}</td>
        <td class="log__td">{{ permit.position }}</td>
        <td class="log__td">{{ permit.company }}</td>
        <td class="log__td">{{ formatDate(permit.dateStart) }}</td>
        <td class="log__td">
          {{ formatDate(permit.dateEnd) }}
          <Toolbar v-bind:permit="permit" v-bind:disabled="curTime > permit.dateEnd"/>
        </td>
      </tr>
    </table>

    <h4>{{ curTime }}</h4>
  </section>
</template>

<script>
import { mapState } from "vuex";
import Toolbar from "./Toolbar";

export default {
  components: {
    Toolbar,
  },

  data() {
    return {
      curTime: null
    }
  },

  methods: {
    formatDate(dateString) {
      let date = new Date(dateString);
      let day = String(date.getDate()).padStart(2, '0');
      let month = String(date.getMonth() + 1).padStart(2, '0'); // month number is an index number which is zero based, that's why +1 needed
      let year = date.getFullYear();

      return `${day}.${month}.${year}`;
    },

    getCurrentDateTime() {
      let date = new Date();

      let day = String(date.getDate()).padStart(2, '0');
      let month = String(date.getMonth() + 1).padStart(2, '0'); // month number is an index number which is zero based, that's why +1 needed
      let year = date.getFullYear();

      let hour = String(date.getHours()).padStart(2, '0');
      let minute = String(date.getMinutes()).padStart(2, '0');
      let second = String(date.getSeconds()).padStart(2, '0');

      return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
    }
  },

  computed: {
    ...mapState({ storedPermits: state => state.permit.storedPermits }),
  },

  created() {
    setInterval(() => { this.curTime = this.getCurrentDateTime(); }, 1000);
  }

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
  padding: 11px 10px;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 400;
  border-left: 0;
  border-right: 0;
  text-align: left;
}

.log__tr {
  padding: 10px 0;
  color: #009c24;
  background-color: lightyellow;
}

.log__tr .toolbar {
  opacity: 0;
  visibility: hidden;
}

.log__tr:hover .toolbar {
  opacity: 1;
  visibility: visible;
}

.log__tr:hover {
  background-color: lemonchiffon;
}

.log__tr_upcoming {
  color: rgba(255, 102, 14, 0.822);
}

.log__tr_expired {
  background-color: rgb(248, 248, 248, .3);
  color: #da261d5b;
}

.log__tr_expired:hover {
  background-color: rgb(248, 248, 248);
}

.log__td {
  position: relative;
  padding: 10px;
  border: 1px solid #ccc;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;
  border-left: 0;
  border-right: 0;
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
