<template>
  <section class="permit">
    <form class="card">
      <h2 class="card__title"><span class="material-icons-outlined md-18">badge</span> Добавить пропуск</h2>

      <div class="card__body">
        <fieldset class="card__fieldset card__fieldset_first">
          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.number || focuses.number }" for="number">№ пропуска</label>
            <input type="text" size="10" name="number" class="card__text-input" v-model="permit.number" @focus="focuses.number = true" @blur="focuses.number = false" ref="number">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('number')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.surname || focuses.surname }" for="surname">Фамилия</label>
            <input type="text" name="surname" class="card__text-input" v-model="permit.surname" @focus="focuses.surname = true" @blur="focuses.surname = false" ref="surname" id="surname">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('surname')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.forename || focuses.forename }" for="forename">Имя</label>
            <input type="text" name="forename" class="card__text-input" v-model="permit.forename" @focus="focuses.forename = true" @blur="focuses.forename = false" ref="forename" id="forename">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('forename')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.patronymic || focuses.patronymic }" for="patronymic">Отчество</label>
            <input type="text" name="patronymic" class="card__text-input" v-model="permit.patronymic" @focus="focuses.patronymic = true" @blur="focuses.patronymic = false" ref="patronymic" id="patronymic">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('patronymic')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>
        </fieldset>

        <fieldset class="card__fieldset">
          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.company || focuses.company }" for="company">Компания</label>
            <input type="text" name="company" class="card__text-input" v-model="permit.company" @focus="focuses.company = true" @blur="focuses.company = false" ref="company">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('company')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.position || focuses.position }" for="position">Должность</label>
            <input type="text" name="position" class="card__text-input" v-model="permit.position" @focus="focuses.position = true" @blur="focuses.position = false" ref="position" id="position">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('position')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.dateStart || focuses.dateStart }" for="dateStart">Действует с</label>
            <input type="text" name="dateStart" class="card__text-input" v-model="permit.dateStart" @focus="focuses.dateStart = true" @blur="focuses.dateStart = false" ref="dateStart" id="dateStart">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('dateStart')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': permit.dateEnd || focuses.dateEnd }" for="dateEnd">Действует по</label>
            <input type="text" name="dateEnd" class="card__text-input" v-model="permit.dateEnd" @focus="focuses.dateEnd = true" @blur="focuses.dateEnd = false" ref="dateEnd" id="dateEnd">
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('dateEnd')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>
        </fieldset>
      </div>

      <div class="card__footer">
        <button type="reset" class="card__button card__button_reset-form" @click="resetForm" :disabled='resetButtonIsDisabled'><span class="material-icons-outlined md-18">clear</span> Очистить</button>
        <button type="submit" class="card__button card__button_save" @click.prevent="savePermit" :disabled='saveButtonIsDisabled'><span class="material-icons-outlined md-18">save</span> Сохранить</button>
      </div>

    </form>
        &nbsp;&nbsp;&nbsp;<span>{{ permit.number }}</span>
  </section>
</template>

<script>
// import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      permit: { number: null, surname: null, forename: null, patronymic: null, company: null, position: null, dateStart: null, dateEnd: null, },
      focuses: { number: false, surname: false, forename: false, patronymic: false, company: false, position: false, dateStart: false, dateEnd: false, }
    }
  },

  methods: {
    // ...mapMutations(['increment']),

    clearInput(inputField) {
      this.permit[inputField] = null;
      this.focusInput(inputField);
    },

    focusInput(inputField) {
      this.$refs[inputField].focus();
    },

    resetForm() {
      this.permit = { number: null, surname: null, forename: null, patronymic: null, company: null, position: null, dateStart: null, dateEnd: null, };
      this.focuses = { number: false, surname: false, forename: false, patronymic: false, company: false, position: false, dateStart: false, dateEnd: false, }
    },

    savePermit() {
      this.resetForm();
      console.log('saved...');
    },
  },

  computed: {
    // ...mapState(['permit']),

    resetButtonIsDisabled() {
      for (const prop in this.permit) {
        if(this.permit[prop]) {
          return false;
        }
      }

      return true;
    },

    saveButtonIsDisabled() {
      for (const prop in this.permit) {
        if(!this.permit[prop]) {
          return true;
        }
      }

      return false;
    },
  }
};
</script>

<style>
.permit {
  padding: 20px 0;
}

.card {
  border: 1px solid lightgray;
  border-radius: 5px;
}

.card__title {
  padding: 10px 20px;
  background: #f7f7f7;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  border-bottom: 1px solid lightgray;

  font-size: 20px;
  line-height: 1.2;
  font-weight: 300;
}

.card__body {
  padding: 20px;
}

.card__fieldset {
  padding: 20px 0;
  border: none;
  display: grid;
  grid-template-columns: 2fr 2fr 1fr 1fr;
  column-gap: 20px;
}

.card__fieldset_first {
  grid-template-columns: 150px 1fr 1fr 1fr;
}

.card__text-field {
  position: relative;
  padding: 0 0 5px;
  padding: 5px 0;
  border-bottom: 1px solid #ced4da;

  display: grid;
  grid-template-columns: 1fr max-content;
  transition: all .5s ease-in-out;
}

.card__text-field:hover .card__button_reset-input {
  opacity: 1;
  visibility: visible;
}

.card__text-field:focus-within {
  border-bottom: 1px solid steelblue;
  box-shadow: 0px -5px 4px -6px steelblue inset;
}

.card__text-label {
  position: absolute;
  top: 3px;
  left: 0;
  grid-column: 1 / 3;
  grid-row: 1;
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  color: gray;
  transition: all .5s ease-in-out;
}

.card__text-label_lifted {
  top: -18px;
  font-size: 14px;
  color: steelblue;
}

.card__text-input {
  width: 100%;
  border: 0;
  outline: 0;
  background: transparent;
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  z-index: 10;
}

.card__button {
    padding: 10px;
    border-radius: 5px;
    background: transparent;
    display: flex;
    align-items: center;
    font-size: 16px;
    line-height: 1.2;
    font-weight: 300;
    transition: all .4s ease-in-out;
}

.card__button:hover {
  color: #fff;
  cursor: pointer;
}

.card__button:disabled {
  color: darkgray;
  border: 1px solid darkgray;
}

.card__button:disabled:hover {
  cursor: default;
  background: transparent;
}

.card__button_reset-input {
  margin: 0 0 0 5px;
  padding: 0;
  width: 26px;
  height: 26px;
  border: 0;

  background: #fff;
  color: #da251d;
  border: 1px solid #da251d;

  opacity: 0;
  visibility: hidden;
  border-radius: 50%;

  transition: all .5s ease-in-out;
}

.card__button_reset-input:hover {
  background: #da251d;
}

.card__footer {
  padding: 10px 20px;
  background: #f7f7f7;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  display: grid;
  grid-template-columns: repeat(2, 130px);
  justify-content: end;
  gap: 10px;
  border-top: 1px solid lightgray;
}

.card__button_reset-form {
    border: 1px solid coral;
}

.card__button_save {
  border: 1px solid steelblue;
}

.card__button_reset-form:hover {
  background: coral;
}

.card__button_save:hover {
  background: steelblue;
}
</style>
