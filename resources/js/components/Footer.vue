<template>
  <footer class="footer">
    <div class="footer__container">
      <p class="footer__copyriht">
        <span class="text-muted">&copy; {{ curYear }}</span> <span class="text-danger">КПП ЗАО «Пургаз»</span>
      </p>
      <p class="footer__contacts">
        <button type="button" class="footer__contacts_btn" @click="showContact">
          <span class="text-danger">Контакты</span>
        </button>
      </p>
    </div>
  </footer>
</template>

<script>
import { mapMutations } from "vuex";

export default {
  data() {
    return {
      
    }
  },

  methods: {
    ...mapMutations('popup', ['openPopup', 'setPopupMessage']),

    showContact() {
      console.log('testtttt');
      this.setPopupMessage({
        header: 'Контактная информация',
        problem: 'Разработчик: Какупшев Игорь Владимирович',
        solution: 'Телефон: +7 (34936) 4-93-14' });
      this.openPopup();
    },
  },

  computed: {
    curYear: function () {
      let today = new Date();
      let curYear = today.getFullYear();

      if ('2022' == curYear) {
        return curYear;
      } else {
        return `2022-${curYear}`;
      }
    }
  },
};
</script>

<style>
.footer {
  box-sizing: border-box;
  margin: 0;
  padding: 20px 0;
  min-height: 50px;
  margin-top: auto;

  background-image:
    linear-gradient(178deg, rgb(142, 202, 255), rgba(0, 138, 255, 0) 100%),
    linear-gradient(20deg, rgb(0, 255, 198), rgba(0, 255, 198, 0) 100%),
    linear-gradient(120deg, rgb(0, 255, 198), rgba(0, 255, 198, 0) 100%);

  border-bottom: 1px solid #aaa;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
}

.footer__container {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 0;

  width: 1280px;
  display: flex;
  justify-content: space-between;
}

.footer__copyriht {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 0;

  width: 100%;
  text-align: left;
}

.footer__contacts {
  box-sizing: border-box;
  margin: 0 auto;
  padding: 0;

  width: 100%;
  text-align: right;
}

.text-muted {
  color: #6c757d;
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  text-shadow: 0 1px 0 #eee;
}

.text-danger {
  color: rgba(220, 53, 69, 1);
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
  text-shadow: 0 1px 0 #eee;
  
  transition: all .2s ease-in-out;
}

.footer__contacts_btn {
  border: 0;
  background: transparent;
}

.footer__contacts_btn:hover {
  cursor: pointer;
}

.footer__contacts_btn .text-danger:hover {
  color: rgb(167, 0, 17);
}

@media screen and (max-width: 1420px) {
  .header {
    padding: 10px;
  }
}
</style>
