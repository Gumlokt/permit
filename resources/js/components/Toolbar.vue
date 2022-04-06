<template>
  <div class="toolbar">
    <button type="button" class="toolbar__btn toolbar__btn-delete" @click="onPermitDelete" v-bind:disabled="disabled" title="Удалить из базы данных без возможности восстановления"><span class="material-icons-outlined md-24">clear</span></button>
    <button type="button" class="toolbar__btn toolbar__btn-expire" @click="onPermitExpire" v-bind:disabled="disabled" title="Сделать срок действия пропуска истёкшим"><span class="material-icons-outlined md-24">delete</span></button>
    <button type="button" class="toolbar__btn toolbar__btn-edit" @click="editPermit(permit)" v-bind:disabled="disabled" title="Отредактировать пропуск"><span class="material-icons-outlined md-24">edit</span></button>
    <button type="button" class="toolbar__btn toolbar__btn-copy" @click="copyPermit(permit)" title="Создать новый пропуск на основе текущего"><span class="material-icons-outlined md-24">content_copy</span></button>
  </div>
  <!-- <Popup /> -->
</template>

<script>
import { mapMutations } from "vuex";

import Popup from "./Popup";

export default {
  components: {
    Popup,
  },

  props: {
    permit: {
      type: Object,
      required: true,
    },
    disabled: {
      type: Boolean,
      required: true
    }
  },

  methods: {
    ...mapMutations('permit', ['deletePermit', 'expirePermit', 'editPermit', 'copyPermit']),
    ...mapMutations('popup', ['openPopup', 'setPopupMessage']),

    onPermitDelete() {
      this.setPopupMessage({
        header: 'Удаление пропуска',
        title: `Внимание! Данная операция необратима!`,
        content: `Вы дейстительно желаете безвозвратно удалить пропуск № ${this.permit.number} из базы данных без возможности последующего его восстановления?`,
        permitId: this.permit.id,
        deleteAction: true // 'true' - to delete permit; 'false' - to do permit as expired
      });
      this.openPopup();

      // if (confirm(`Вы дейстительно желаете безвозвратно удалить пропуск № ${this.permit.number} из базы данных без возможности последующего его восстановления?`)) {
      //   this.deletePermit(this.permit.id);
      // }
    },

    onPermitExpire() {
      this.setPopupMessage({
        header: 'Сделать пропуск недействительным',
        title: `Внимание! Этот пропуск больше нельзя будет сделать действующим!`,
        content: `Вы дейстительно желаете сделать истёкшим срок действия пропуска № ${this.permit.number}?`,
        permitId: this.permit.id,
        deleteAction: false // 'true' - to delete permit; 'false' - to do permit as expired
      });
      this.openPopup();

      // if (confirm(`Вы дейстительно желаете сделать истёкшим срок действия пропуска № ${this.permit.number}?`)) {
      //   this.expirePermit(this.permit.id);
      // }
    }
  },

  // computed: {
  //   ...mapState({
  //     popupOpened: state => state.popup.popupOpened,
  //     popupOpened: state => state.popup.popupOpened,
  //   }),

  // },

};
</script>

<style>
.toolbar {
  position: absolute;
  top: 2px;
  right: 2px;

  display: grid;
  grid-auto-flow: column;
  justify-items: center;
  align-items: center;
  gap: 5px;
}

.toolbar__btn {
  width: 40px;
  height: 35px;
  background-color: rgba(250, 240, 230, 0.85);
  border-radius: 4px;
  border: none;
  box-shadow: 0px 0px 2px rgba(0, 0, 0, .2);

  display: grid;
  justify-items: center;
  align-items: center;
  justify-content: center;

  transition: all .15s ease-in-out;
}

.toolbar__btn:hover {
  cursor: pointer;
  color: #fff;
}

.toolbar__btn:disabled {
  color: darkgray;
  border: 1px solid darkgray;
}

.toolbar__btn:disabled:hover {
  color: darkgray;
  cursor: default;
  background-color: rgba(250, 240, 230, 0.85);
}

.toolbar__btn-delete {
  color: #da251d;
  border: 1px solid #da251d;
}

.toolbar__btn-delete:hover {
  background: #da251d;
}

.toolbar__btn-expire {
  color: slategray;
  border: 1px solid slategray;
}

.toolbar__btn-expire:hover {
  background: slategray;
}

.toolbar__btn-edit {
  color: coral;
  border: 1px solid coral;
}

.toolbar__btn-edit:hover {
  background: coral;
}

.toolbar__btn-copy {
  color: #28a745;
  border: 1px solid #28a745;
}

.toolbar__btn-copy:hover {
  background: #28a745;
}
</style>
