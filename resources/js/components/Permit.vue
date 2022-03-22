<template>
  <section class="permit">
    <form class="card" v-bind:class="{ 'card_editing': permitEditing }">
      <h2 class="card__title"><span class="material-icons-outlined md-18">badge</span> {{ cardTitle }} пропуск</h2>

      <div class="card__body">
        <fieldset class="card__fieldset card__fieldset_first">
          <div class="card__text-field">
            <label class="card__text-label" v-bind:class="{ 'card__text-label_lifted': newPermit.number }" for="number">№ пропуска</label>
            <input type="text" size="10" name="number" class="card__text-input" v-model="newPermit.number" disabled>
            <!-- <button class="card__button card__button_reset-input" @click.prevent="clearInput('number')" disabled><span class="material-icons material-icons-outlined">clear</span></button> -->
          </div>

          <PersonAutocomplete v-bind:field="'surname'" v-bind:title="'Фамилия'"/>

          <PersonAutocomplete v-bind:field="'forename'" v-bind:title="'Имя'"/>

          <PersonAutocomplete v-bind:field="'patronymic'" v-bind:title="'Отчество'"/>
        </fieldset>

        <fieldset class="card__fieldset">
          <PersonAutocomplete v-bind:field="'position'" v-bind:title="'Должность'"/>

          <PersonAutocomplete v-bind:field="'company'" v-bind:title="'Компания'"/>

          <div class="card__text-field">
            <label class="card__text-label card__text-label_lifted" for="dateStart">Действует с</label>
            <Datepicker
              name="dateStart"
              class="card__text-input"
              v-model="newPermit.dateStart"
              :enableTimePicker="false"
              :format="format"
              :clearable="false"
              locale="ru"
              monthNameFormat="long"
              placeholder="ДД.ММ.ГГГГ"
              autoApply
              hideInputIcon
              uid="dateStart">
            </Datepicker>
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('dateStart')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>

          <div class="card__text-field">
            <label class="card__text-label card__text-label_lifted" for="dateEnd">Действует по</label>
            <Datepicker
              name="dateEnd"
              class="card__text-input"
              v-model="newPermit.dateEnd"
              :enableTimePicker="false"
              :format="format"
              :clearable="false"
              locale="ru"
              monthNameFormat="long"
              placeholder="ДД.ММ.ГГГГ"
              autoApply
              hideInputIcon
              uid="dateEnd">
            </Datepicker>
            <button class="card__button card__button_reset-input" @click.prevent="clearInput('dateEnd')"><span class="material-icons material-icons-outlined">clear</span></button>
          </div>
        </fieldset>
      </div>

      <div class="card__footer">
        <!-- <button type="button" class="card__button card__button_temporary" @click.prevent="getPermits"><span class="material-icons-outlined md-18">file_download</span> Получить</button> -->
        <!-- <button type="button" class="card__button card__button_temporary" @click.prevent="fillInNewPermit"><span class="material-icons-outlined md-18">format_color_fill</span> Заполнить</button> -->
        <button type="reset" class="card__button card__button_reset-form" @click.prevent="resetForm" :disabled='resetButtonIsDisabled'><span class="material-icons-outlined md-18">clear</span> Очистить</button>
        <button type="submit" class="card__button card__button_save" @click.prevent="savePermit" :disabled='saveButtonIsDisabled'><span class="material-icons-outlined md-18">save</span> Сохранить</button>
      </div>

    </form>
  </section>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import Datepicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css';
import PersonAutocomplete from "./PersonAutocomplete";

export default {
  components: { Datepicker, PersonAutocomplete },

  setup() {
    const format = (date) => {
      const day   = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const year = date.getFullYear();

      return `${day}.${month}.${year}`;
    }

    return {
      format,
    }
  },


  methods: {
    ...mapMutations('popup', ['openPopup', 'setPopupMessage']),
    ...mapMutations('permit', ['fillInNewPermit']), // get dummy data
    ...mapMutations('permit', ['setNextPermitNumber']),
    ...mapMutations('permit', ['populatePermits']),
    ...mapMutations('permit', ['resetNewPermit']),

    getFilteredPermits(inputField) {
      console.log(`getFilteredPermits.... ${inputField}`);
    },

    clearInput(inputField) {
      this.newPermit[inputField] = null;
    },

    resetForm() {
      this.resetNewPermit();
      this.setNextPermitNumber();
      this.populatePermits();
    },

    savePermit() {
      const permit = {
        id: this.newPermit.id,
        number: this.newPermit.number,
        surname: this.newPermit.surname,
        forename: this.newPermit.forename,
        patronymic: this.newPermit.patronymic,
        company: this.newPermit.company,
        position: this.newPermit.position,
        dateStart: this.formatDate(this.newPermit.dateStart),
        dateEnd: this.formatDate(this.newPermit.dateEnd),
      };

      const res = fetch(this.newPermitActionPath.path, {
        method: this.newPermitActionPath.method,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(permit),
      })
      .then((response) => {
        return response.json();
      })
      .then((res) => {
        if(res.error) {
          this.setPopupMessage(res);
          this.openPopup();
          return;
        }

        if(res.errors) {
          this.setPopupMessage({ problem: `Похоже, что водимымые данные содержат неизвестную ошибку. [ ${res.message} ]`, solution: 'Попробуйте исправить вводимые данные. Если после этого ошибка не исчезнет, обратитесь  за помощью к разработчику приложения.' });
          this.openPopup();
          return;
        }

        // this.populatePermits();
        this.resetForm();
        return res;
      })
      .catch((err) => {
        console.log(err);
      });
      // console.log('saved...');
    },

    getPermits() { // temporary method for development cases
      console.log(this.$store.state.permit.storedPermits);
    },

    formatDate(permitDate) {
      const date = new Date(permitDate);

      const day   = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const year = date.getFullYear();

      return `${day}.${month}.${year}`;
    },
  },

  computed: {
    ...mapState({
      newPermit: state => state.permit.newPermit,
      newPermitActionPath: state => state.permit.newPermitActionPath,
      cardTitle: state => state.permit.cardTitle,
      permitEditing: state => state.permit.permitEditing,
    }),

    resetButtonIsDisabled() {
      for (const prop in this.newPermit) {
        if(this.newPermit[prop]) {
          return false;
        }
      }

      return true;
    },

    saveButtonIsDisabled() {
      for (const prop in this.newPermit) {
        if(prop != 'id' && !this.newPermit[prop]) {
          return true;
        }
      }

      return false;
    },
  },

  created() {
    this.setNextPermitNumber();
    this.populatePermits(); // get all already stored permits
  }
};
</script>

<style>
.permit {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 20px 0 10px;

  max-width: 1280px;
}

.card {
  border: 1px solid lightgray;
  border-radius: 5px;
  transition: all .2s linear;
}

.card_editing {
  border: 1px solid rgb(253, 114, 0);
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
  padding: 10px 20px;
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
  transform: rotate(90deg) scale(1.05, 1.05);
}

.card__text-field:focus-within {
  border-bottom: 1px solid steelblue;
  box-shadow: 0px -5px 4px -6px steelblue inset;
}

.card__text-label {
  position: absolute;
  top: 2px;
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
  padding: 0 5px;
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

.card__button_temporary {
  border: 1px solid slategray;
}

.card__button_temporary:hover {
  background: slategray;
}

.card__button_reset-input {
  margin: 0 0 0 5px;
  padding: 0;
  width: 32px;
  height: 32px;
  border: 0;

  background: #fff;
  color: #da251d;
  border: 1px solid #da251d;

  opacity: 0;
  visibility: hidden;
  border-radius: 50%;

  display: grid;
  justify-items: center;
  align-items: center;
  justify-content: center;

  transition: all .5s ease-in-out;
}

.card__button_reset-input:hover {
  background: #da251d;
}

.card__footer {
  padding: 5px 20px;
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

@media screen and (max-width: 1280px) {
  .permit {
    max-width: 99%;
  }
}

.dp__theme_light {
  /* --dp-background-color: #fff9f2; */
  --dp-background-color: #fff;
  --dp-text-color: #212121;
  --dp-hover-color: #f3f3f3;
  --dp-hover-text-color: #212121;
  --dp-hover-icon-color: #959595;
  --dp-primary-color: #1976d2;
  --dp-primary-text-color: #f8f5f5;
  --dp-secondary-color: #c0c4cc;
  /* --dp-border-color: #ddd; */
  --dp-border-color: none;
  --dp-border-color-hover: #aaaeb7;
  --dp-disabled-color: #f6f6f6;
  --dp-scroll-bar-background: #f3f3f3;
  --dp-scroll-bar-color: #959595;
  --dp-success-color: #76d275;
  --dp-success-color-disabled: #a3d9b1;
  --dp-icon-color: #959595;
  --dp-danger-color: #ff6f60;
}
</style>
