export default {
  namespaced: true,

  state: {
    popupOpened: false,
    popupMessage: null
  },

  mutations: {
    openPopup(state, payload) {
      state.popupOpened = true;
    },

    closePopup(state, payload) {
      state.popupOpened = false;
      this.commit('popup/resetPopupMessage');
    },

    setPopupMessage(state, payload) {
      state.popupMessage = payload;
    },

    resetPopupMessage(state, payload) {
      state.popupMessage = null;
    },
  }
}
