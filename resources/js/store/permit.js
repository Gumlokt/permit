export default {
  namespaced: true,

  state: {
    cardTitle: 'Добавить',
    permitEditing: false,
    newPermitActionPath: {
      // store path by default
      path: "api/permits",
      method: "POST",
    },
    storedPermits: [],

    newPermit: {
      id: null,
      number: null,
      surname: null,
      forename: null,
      patronymic: null,
      company: null,
      position: null,
      dateStart: null,
      dateEnd: null,
    },
  },

  mutations: {
    updateNewPermit: function (state, payload) {
      // [:print:] matches a visible character [\x21-\x7E]
      // \s+ matches any whitespace character (equal to [\r\n\t\f\v ])
      let pattern = /[\d\w\s\+\-\*\/~!@"#№$;%\^:&?<>\(\)\[\]\{\}а-яА-ЯёЁ]+/;

      if (pattern.test(payload.value)) {
        if (!/^\s+/.test(payload.value)) {
          state.newPermit[payload.field] = payload.value;
          console.log(payload.value);
        }
        // console.log('field: ' + payload.field + '; value: ' + payload.value);
      } else {
        state.newPermit[payload.field] = '';
      }

      if(state.newPermit.surname || state.newPermit.forename || state.newPermit.patronymic || state.newPermit.position || state.newPermit.company) {
        this.commit("permit/filterPermits");
      }

      if(!state.newPermit.surname && !state.newPermit.forename && !state.newPermit.patronymic && !state.newPermit.position && !state.newPermit.company) {
        this.commit("permit/populatePermits");
      }

    },

    filterPermits(state, payload) {
      const res = fetch(`api/permits/filter`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          mainPermitFields: {
            surname: state.newPermit.surname,
            forename: state.newPermit.forename,
            patronymic: state.newPermit.patronymic,
            position: state.newPermit.position,
            company: state.newPermit.company,
          }
        }),
      })
      .then((response) => {
        return response.json();
      })
      .then((res) => {
        console.log(res);
        state.storedPermits = res;
        return res; // res is an array of objects, where each object contain permit data
      })
      .catch((err) => {
        console.log(err);
      });

    },

    fillInNewPermit(state, payload) {
      // temporary method for development cases
      state.newPermit = {
        id: null,
        number: null,
        surname: "Иванов",
        forename: "Иван",
        patronymic: "Иванович",
        company: "ООО Рога и Копыта",
        position: "Ведущий специалист",
        dateStart: "2021-12-22 00:00:00",
        dateEnd: "2021-12-31 23:59:59",
      };
      this.commit("permit/setNextPermitNumber", 2022);
    },

    setNewPermit(state, payload) {
      state.newPermit = {
        id: payload.id,
        number: payload.number,
        surname: payload.surname,
        forename: payload.forename,
        patronymic: payload.patronymic,
        company: payload.company,
        position: payload.position,
        dateStart: payload.dateStart,
        dateEnd: payload.dateEnd,
      };
    },

    editPermit(state, payload) {
      state.newPermitActionPath.path = `api/permits/${payload.id}`;
      state.newPermitActionPath.method = "PUT";

      this.commit("permit/setNewPermit", payload);
      state.cardTitle = 'Отредактировать';
      state.permitEditing = true;
    },

    copyPermit(state, payload) {
      this.commit("permit/setNewPermit", payload);
      this.commit("permit/setNextPermitNumber");
      state.newPermit.dateStart = null;
      state.newPermit.dateEnd = null;
      state.cardTitle = 'Добавить';
      state.permitEditing = false;
    },

    resetNewPermit(state, payload) {
      state.newPermitActionPath.path = "api/permits";
      state.newPermitActionPath.method = "POST";
      state.newPermit = {
        id: null,
        number: null,
        surname: null,
        forename: null,
        patronymic: null,
        company: null,
        position: null,
        dateStart: null,
        dateEnd: null,
      };
      state.cardTitle = 'Добавить';
      state.permitEditing = false;
    },

    setNextPermitNumber(state, payload) {
      let currentYear = new Date().getFullYear();

      if (payload) {
        currentYear = payload;
      }

      // console.log(currentYear);
      // currentYear = 2019;

      const res = fetch(`api/permits/last/${currentYear}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          state.newPermit.number = parseInt(res) + 1;
          return res; // res is a last stored permit number
        })
        .catch((err) => {
          console.log(err);
        });
    },

    populatePermits(state, payload) {
      const res = fetch(`api/permits`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          state.storedPermits = res;
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },

    expirePermit(state, payload) {
      const res = fetch(`api/permits/expire/${payload}`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          this.commit("permit/populatePermits");
          this.commit("permit/resetNewPermit");
          this.commit("permit/setNextPermitNumber");
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },

    deletePermit(state, payload) {
      const res = fetch(`api/permits/${payload}`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          this.commit("permit/populatePermits");
          this.commit("permit/resetNewPermit");
          this.commit("permit/setNextPermitNumber");
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },
};
