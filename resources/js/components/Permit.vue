<template>
  <section class="permit">
    <form class="card">
      <h2 class="card__title">Добавить пропуск</h2>

      <div class="card__body">
        <fieldset class="card__fieldset card__fieldset_first">
          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': liftLabel }" for="number">№ пропуска</label>
            <input name="number" class="card__text-input" v-model="permit.number" @focus="focuses.number = true" @blur="focuses.number = false" ref="number" id="number">
            <button class="card__button-reset" @click.prevent="clearInput('number')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': liftLabel }" for="surname">Фамилия</label>
            <input name="surname" class="card__text-input" v-model="permit.surname" @focus="focuses.surname = true" @blur="focuses.surname = false" ref="surname" id="surname">
            <button class="card__button-reset" @click.prevent="clearInput('surname')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': liftLabel }" for="forename">Имя</label>
            <input name="forename" class="card__text-input" v-model="permit.forename" @focus="focuses.forename = true" @blur="focuses.forename = false" ref="forename" id="forename">
            <button class="card__button-reset" @click.prevent="clearInput('forename')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': liftLabel }" for="patronymic">Отчество</label>
            <input name="patronymic" class="card__text-input" v-model="permit.patronymic" @focus="focuses.patronymic = true" @blur="focuses.patronymic = false" ref="patronymic" id="patronymic">
            <button class="card__button-reset" @click.prevent="clearInput('patronymic')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>
        </fieldset>
      </div>

      <div class="card__footer">
        <button class="card__button-save">Сохранить</button>
        &nbsp;&nbsp;&nbsp;<span>{{ permit.number }}</span>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  data() {
    return {
      permit: {
        number: null,
        surname: null,
        forename: null,
        patronymic: null,
      },
      focuses: {
        number: false,
        surname: false,
        forename: false,
        patronymic: false,
      }
    }
  },

  methods: {
    clearInput(inputField) {
      this.permit[inputField] = null;
      this.focusInput(inputField);
    },

    focusInput(inputField) {
      this.$refs[inputField].focus();
    }
  },

  computed: {
    liftLabel() {
      let myInputField = 'number';
      console.log(myInputField);

      if(this.permit[myInputField]) { return true; }

      if(this.focuses[myInputField]) {
        return true;
      } else {
        return false;
      }
    }
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
  padding: 20px 0 0;
  border: none;
  display: grid;
  /* grid-template-columns: repeat(4, 1fr); */
  column-gap: 20px;
}

.card__fieldset_first {
  /* grid-template-columns: 150px repeat(3, 1fr); */
  grid-template-columns: 10fr repeat(3, 30fr);
}

.card__text-field {
  position: relative;
  padding: 0 0 5px;
  border-bottom: 1px solid #ced4da;
  display: grid;
  grid-template-columns: 1fr min-content;
  grid-template-columns: 1fr 1fr;
  grid-template-columns: min-content min-content;
  grid-template-columns: max-content max-content;
  grid-template-columns: 1fr max-content;
  align-items: end;
  align-content: end;
  transition: all .5s ease-in-out;
  /* border: 1px solid red; */
}

.card__text-field:hover .card__button-reset {
  opacity: 1;
  visibility: visible;
}

.card__text-field:focus-within {
  border-bottom: 1px solid steelblue;
  box-shadow: 0px -5px 4px -6px steelblue inset;
}

.card__text-label {
  position: absolute;
  top: 0;
  left: 0;
  grid-column: 1 / 3;
  grid-row: 1;
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  color: gray;
  transition: all .5s ease-in-out;
  /* border: 1px solid blue; */
}

.card__text-label_lifted {
  top: -18px;
  font-size: 14px;
  color: steelblue;
  text-shadow: 1px 1px 2px rgba(150, 150, 150, 1);
}

.card__text-input {
  border: 0;
  outline: 0;
  background: transparent;
  /* max-width: 100px; */
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  z-index: 10;
  /* border: 1px solid green; */
}

.card__button-reset {
  margin: 0 0 0 5px;
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

.card__button-reset:hover {
  background: #da251d;
  color: #fff;
}

.card__footer {
  padding: 10px 20px;
  background: #f7f7f7;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  border-top: 1px solid lightgray;
}

.card__button-save {
  padding: 10px;
  border: 1px solid steelblue;
  border-radius: 5px;
  background: transparent;
  
  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;

  transition: all .4s ease-in-out;
}

.card__button-save:hover {
  color: #fff;
  background: steelblue;
}
</style>
