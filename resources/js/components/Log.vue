<template>
  <section class="log">
    <div class="log__panel">
      <form action="/permits/print" method="POST" class="log__form" target="_blank">
        <input type="hidden" name="_token" v-bind:value="token">
        <input type="hidden" name="permits" v-bind:value="printBag">

        <button type="submit" class="log__print-btn log__print-btn_all" :disabled="printBag.length ? false : true">
          <div class="log__print-icon">
            <span class="material-icons-outlined">print</span>
          </div>
          <span class="log__print-btn-title">На печать</span>
          <span class="log__print-badge" v-if="printBag.length">{{ printBag.length }}</span>
        </button>
      </form>

      <Paginate
        v-model="currentPage"

        :page-count="pages"
        :page-range="5"
        :margin-pages="2"

        :click-handler="displayNextItems"

        :page-class="'page-item'"
        :first-button-text="'В начало'"
        :last-button-text="'В конец'"
        :prev-text="'пред.'"
        :next-text="'след.'"
        :first-last-button="true"
      >
      </Paginate>
    </div>

    <table class="log__table">
      <tr class="log__trh">
        <th class="log__th">
          <div class="log__th_print">
            <p class="log__th_title">№</p>
            <button type="button" class="log__print-btn log__print-btn_one log__print-btn_success" @click="selectAllPermitsToPrint()">
              <span class="material-icons-outlined">print</span>
            </button>

            <button type="button" class="log__print-btn log__print-btn_one" @click="deselectAllPermitsToPrint()">
              <span class="material-icons-outlined">print</span>
            </button>
          </div>
        </th>
        <th class="log__th">Фамилия</th>
        <th class="log__th">Имя</th>
        <th class="log__th">Отчество</th>
        <th class="log__th">Должность</th>
        <th class="log__th">Компания</th>
        <th class="log__th">Действует с</th>
        <th class="log__th">Действует по</th>
      </tr>

      <tr class="log__tr" v-for="permit in storedPermits" v-bind:key="permit.id" v-bind:class="{ 'log__tr_upcoming': permit.dateStart > curTime, 'log__tr_expired': curTime > permit.dateEnd, 'log__tr_editing': permit.id == newPermit.id && permitEditing, 'log__tr_printing': printBag.indexOf(permit.id) !== -1 }">
        <td class="log__td">
          <div class="log__td_container">
            {{ permit.number }}
            &emsp;
            <button type="button" class="log__print-btn log__print-btn_one" @click="togglePermitToPrint(permit)" :class="{ 'log__print-btn_success': printBag.indexOf(permit.id) !== -1 }">
              <span class="material-icons-outlined">print</span>
            </button>
          </div>
        </td>
        <td class="log__td">{{ permit.surname }}</td>
        <td class="log__td">{{ permit.forename }}</td>
        <td class="log__td">{{ permit.patronymic }}</td>
        <td class="log__td">{{ permit.position }}</td>
        <td class="log__td">{{ permit.company }}</td>
        <td class="log__td">{{ formatDate(permit.dateStart) }}</td>
        <td class="log__td">
          {{ formatDate(permit.dateEnd) }} <span v-if="permit.id == newPermit.id && permitEditing" class="material-icons-outlined md-24">edit...</span>
          <Toolbar v-bind:permit="permit" v-bind:disabled="curTime > permit.dateEnd"/>
        </td>
      </tr>
    </table>
  </section>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import Toolbar from "./Toolbar";
import Paginate from "vuejs-paginate-next";

export default {
  components: {
    Paginate,
    Toolbar,
  },

  data() {
    return {
      token: '',
      curTime: null,
    }
  },

  methods: {
    ...mapMutations('permit', ['setPage']),
    ...mapMutations('permit', ['filterPermits']),
    ...mapMutations('permit', ['deselectAllPermitsToPrint']),

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
    },

    togglePermitToPrint(selectedPermit) {
      if (this.printBag.indexOf(selectedPermit.id) !== -1) {
        this.printBag.splice(this.printBag.indexOf(selectedPermit.id), 1);
      } else {
        this.printBag.push(selectedPermit.id);
      }
    },

    selectAllPermitsToPrint() {
      this.deselectAllPermitsToPrint();

      for (let storedPermit of this.storedPermits) {
        this.printBag.push(storedPermit.id);
      }
    },

    displayNextItems(pageNum) {
      this.filterPermits(); // 1 - first page for pagination
      this.deselectAllPermitsToPrint();
    },
  },

  computed: {
    ...mapState({ storedPermits: state => state.permit.storedPermits }),
    ...mapState({ newPermit: state => state.permit.newPermit }),
    ...mapState({ permitEditing: state => state.permit.permitEditing }),
    ...mapState({
      page: state => state.permit.page,
      pages: state => state.permit.pages,
      printBag: state => state.permit.printBag,
    }),

    currentPage: {
      get () { return this.page; },
      set (value) { this.setPage(value); }
    },
  },

  created() {
    setInterval(() => { this.curTime = this.getCurrentDateTime(); }, 1000);

    let token = document.head.querySelector('meta[name="csrf-token"]');
    this.token = token.content;
  }

};
</script>

<style>
.log {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 10px 0;
  max-width: 1600px;

  display: grid;
  grid-template-columns: 1fr;
  justify-items: center;
}
.log__panel {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 0 0 20px;
  width: 100%;
  max-width: 1280px;

  display: flex;
  justify-content: space-between;
  align-items: baseline;
}

.log__form {
  display: flex;
  justify-content: space-between;
  align-items: center;

  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.log__title {
  padding: 10px 0;
}

.log__table {
  width: 100%;
  max-width: 100%;
  border-collapse: collapse;
}

.log__trh {
  background: #e1f5fe;
}

.log__trh:hover {
  background: #cef0ff;
}

.log__th {
  padding: 11px 10px;
  border: 1px solid #ccc;
  font-size: 16px;
  font-weight: 400;
  border-left: 0;
  border-right: 0;
  text-align: left;
}

.log__th_print {
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.log__th_title {
  padding: 0 20px 0 0;
}

.log__tr {
  color: #009c24;
  background: lightyellow;
  transition: all .2s linear;
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
  background: lemonchiffon;
}

.log__tr_upcoming {
  color: rgba(255, 102, 14, 0.822);
}

.log__tr_expired {
  background: rgb(248, 248, 248, .3);
  color: #da261d5b;
}

.log__tr_expired:hover {
  background: rgb(248, 248, 248);
}

.log__tr_editing {
  background: rgba(255, 183, 123, 0.568);
  transform: scale(1.02, 1.02) ;
  animation: blink 2s linear infinite;
}

.log__tr_editing:hover {
  background: rgba(255, 160, 82, 0.767);
}

.log__tr_printing {
  background: #c3e6cb;
}

.log__tr_printing:hover {
  background: #8ed19e;
}

.log__td {
  position: relative;
  padding: 4px 10px;
  border: 1px solid #ccc;
  font-size: 16px;
  font-weight: 300;
  border-left: 0;
  border-right: 0;
}

.log__td_container {
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.log__print-btn {
  border: none;
  background: transparent;
  color: slategray;
}

.log__print-btn:hover {
  cursor: pointer;
  box-shadow: 0px 0px 2px rgba(0, 0, 0, .2);
}

.log__print-btn_all {
  display: flex;
  align-items: center;

  box-sizing: border-box;
  margin: 0;
  padding: 8px 16px;

  border-radius: 5px;
  border: 1px solid slategray;
  color: #000;

  transition: all .2s ease-in-out;
}

.log__print-btn_all:hover {
  background: slategray;
  color: #fff;
  cursor: pointer;
}

.log__print-btn_all:disabled {
  color: lightslategray;
  background: transparent;
  cursor: not-allowed;
}

.log__print-btn_all:disabled:hover {
  color: lightslategray;
  background: transparent;
}

.log__print-btn_one {
  width: 40px;
  height: 35px;

  transition: all .4s ease;
}
.log__print-btn_one:hover {
  transform: scale(1.1, 1.1);
}

.log__print-btn_success {
  color: #009c24;
}

.log__print-icon {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.log__print-btn-title {
  display: block;
  box-sizing: border-box;
  margin: 0 0 0 10px;
  padding: 0;

  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;
}

.log__print-badge {
  display: block;
  box-sizing: border-box;
  margin: 0 0 0 10px;
  padding: 2px 5px;
  border-radius: 5px;
  background: #8ed19e;
}

.log__print-btn .material-icons-outlined {
  display: block;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}



/* Pagination (based on Bootstrap v5.1.3) */
.pagination {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  list-style: none;

  display: flex;
}

.page-link {
  position: relative;
  display: block;
  color: #0d6efd;
  text-decoration: none;
  background-color: #fff;
  border: 1px solid #dee2e6;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;

  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;
}
@media (prefers-reduced-motion: reduce) {
  .page-link {
    transition: none;
  }
}
.page-link:hover {
  z-index: 2;
  color: #0a58ca;
  background-color: #e9ecef;
  border-color: #dee2e6;
  cursor: pointer;
}
.page-link:focus {
  z-index: 3;
  color: #0a58ca;
  background-color: #e9ecef;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.page-item:not(:first-child) .page-link {
  margin-left: -1px;
}
.page-item.active .page-link {
  z-index: 3;
  color: #fff;
  background-color: #0d6efd;
  border-color: #0d6efd;
}
.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  background-color: #fff;
  border-color: #dee2e6;
}

.page-link {
  padding: 0.375rem 0.75rem;
}

.page-item:first-child .page-link {
  border-top-left-radius: 0.25rem;
  border-bottom-left-radius: 0.25rem;
}
.page-item:last-child .page-link {
  border-top-right-radius: 0.25rem;
  border-bottom-right-radius: 0.25rem;
}



@media screen and (max-width: 1600px) {
  .log__table {
    max-width: 99%;
  }
}

@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: .4; }
  100% { opacity: 1; }
}
</style>
