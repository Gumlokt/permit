export default {
  namespaced: true,

  state: {
    popupOpened: false,
    popupMessage: {
      header: null,
      title: null,
      content: null,
      permitId: null,
      deleteAction: null, // 'true' - to delete permit; 'false' - to do permit as expired
    },
  },

  mutations: {
    openPopup(state, payload) {
      state.popupOpened = true;
    },

    closePopup(state, payload) {
      state.popupOpened = false;
      this.commit("popup/resetPopupMessage");
    },

    setPopupMessage(state, payload) {
      state.popupMessage.header = payload.header;
      state.popupMessage.title = payload.title;
      state.popupMessage.content = payload.content;
      state.popupMessage.permitId = payload.permitId;
      state.popupMessage.deleteAction = payload.deleteAction; // 'true' - to delete permit; 'false' - to do permit as expired
    },

    resetPopupMessage(state, payload) {
      state.popupMessage.header = null;
      state.popupMessage.title = null;
      state.popupMessage.content = null;
      state.popupMessage.permitId = null;
      state.popupMessage.deleteAction = null;
    },
  },
};
