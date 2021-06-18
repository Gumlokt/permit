import { createStore } from 'vuex';

export const store = createStore({
  state () {
    return {
      permit: {
        number: null,
        surname: null,
        forename: null,
        patronymic: null,
        company: null,
        position: null,
        dateStart: null,
        dateEnd: null,
      },
    }
  },

  mutations: {
    increment (state, payload) {
      state.count = state.count + payload;
    },

  }
});
