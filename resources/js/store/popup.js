export default {
  namespaced: true,

  state: {
    popupOpened: false,
    popupMessage: {
      problem: null,
      solution: null,
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
      state.popupMessage.problem = payload.problem;
      state.popupMessage.solution = payload.solution;
    },

    resetPopupMessage(state, payload) {
      state.popupMessage.problem = null;
      state.popupMessage.solution = null;
    },
  },
};
