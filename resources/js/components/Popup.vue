<template>
  <div class="popup" v-bind:class="{ 'popup_opened': popupOpened }">
    <div class="popup__container" v-bind:class="{ 'popup__container_lifted': popupOpened }">
      <button class="popup__btn-close" @click="closePopup"></button>

      <div class="popup__header">
        <h4 class="popup__header_title">{{ headerTitle }}</h4>
      </div>

      <div class="popup__body">
        <p class="popup__message popup__message_problem">{{ popupMessage.problem }}</p>
        <p class="popup__message popup__message_solution">{{ popupMessage.solution }}</p>
      </div>

      <div class="popup__footer">
        <button class="popup__btn-ok" @click="closePopup">Закрыть</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  data() {
    return {
      headerTitle: 'Сообщение',
      footerTitle: null
    }
  },

  methods: {
    ...mapMutations('popup', ['closePopup']),
  },

  computed: {
    ...mapState({
      popupOpened: state => state.popup.popupOpened,
      popupMessage: state => state.popup.popupMessage,
    }),
  },

};
</script>

<style>
.popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 200;
  background-color: rgba(0, 0, 0, .5);

  display: grid;
  justify-items: center;
  align-items: center;

  opacity: 0;
  visibility: hidden;
  transition: all .4s ease-in-out;
}

.popup_opened {
  opacity: 1;
  visibility: visible;
}

.popup__container {
  position: relative;
  padding: 0;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: 0px 0px 25px rgba(0, 0, 0, .3);
  width: 100%;
  max-width: 500px;
  z-index: 400;
  top: 0;
  transition: all .4s ease-in-out;
}

.popup__container_lifted {
  top: -60px;
}

.popup__btn-close {
  position: absolute;
  top: -40px;
  right: -40px;
  width: 46px;
  height: 46px;

  border: 0;
  background-color: transparent;
  background-image: url(../../images/icons/cross.svg);
  background-size: 46px;
  background-position: center;
  background-repeat: no-repeat;
  opacity: .8;

  transition: all .4s ease;
}

.popup__btn-close:hover {
  cursor: pointer;
  opacity: 1;
  transform: rotate(90deg) scale(1.1, 1.1);
}

.popup__header {
  padding: 10px 20px;
  background: #f7f7f7;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  border-bottom: 1px solid lightgray;
}

.popup__header_title {
  font-size: 20px;
  line-height: 1.2;
  font-weight: 300;
}

.popup__body {
  padding: 20px;

  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.popup__message {
  font-size: 18px;
  line-height: 1.2;
  font-weight: 300;
}

.popup__message_problem {
  color: #da251d;
}

.popup__message_solution {
  color: darkslategray;
}

.popup__footer {
  padding: 10px 20px;
  background: #f7f7f7;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;

  border-top: 1px solid lightgray;
  font-size: 20px;
  line-height: 1.2;
  font-weight: 300;

  display: flex;
  justify-content: flex-end;
  align-items: center;
}

.popup__btn-ok {
  padding: 10px 20px;
  border-radius: 5px;
  background: transparent;
  border: 1px solid slategray;
  display: flex;
  align-items: center;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 300;

  transition: all .2s ease-in-out;
}

.popup__btn-ok:hover {
  background: slategray;
  color: #fff;
  cursor: pointer;
}
</style>
