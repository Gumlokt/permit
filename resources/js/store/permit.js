export default {
  namespaced: true,

  state: {
    typedText: {
      surname: null,
      forename: null,
      patronymic: null,
      position: null,
      company: null,
    },
    inputSuggestions: {
      surname: [],
      forename: [],
      patronymic: [],
      position: [],
      company: [],
    },

    cardTitle: "Добавить",
    permitEditing: false,
    newPermitActionPath: {
      path: "api/permits", // store path by default
      method: "POST",
    },
    storedPermits: [],

    newPermit: {
      id: null,
      number: null,
      surname: null,
      forename: null,
      patronymic: null,
      position: null,
      company: null,
      dateStart: null,
      dateEnd: null,
    },
    page: 1,
    pages: 1,
    rowsRoDisplay: 16,
    printBag: [],

    stats: {
      totalPermitsCount: null,
      validPermitsCount: null,
      expiredPermitsCount: null,
    }
  },

  mutations: {
    updateNewPermit: function (state, payload) {
      // [:print:] matches a visible character [\x21-\x7E]
      // \s+ matches any whitespace character (equal to [\r\n\t\f\v ])
      let pattern = /[\d\w\s\+\-\*\/~!@"#№$;%\^:&?<>\(\)\[\]\{\}а-яА-ЯёЁ]+/;

      if (pattern.test(payload.value)) {
        if (!/^\s+/.test(payload.value)) {
          state.newPermit[payload.field] = payload.value;
        }
      } else {
        state.newPermit[payload.field] = "";
      }

      this.commit("permit/filterPermits");
    },

    setTypedText(state, payload) {
      state.typedText[payload.field] = payload.value;
    },

    resetTypedText(state, payload) {
      state.typedText = {
        surname: null,
        forename: null,
        patronymic: null,
        position: null,
        company: null,
      };
    },

    resetAllInputSuggestions(state, payload) {
      state.inputSuggestions = {
        surname: [],
        forename: [],
        patronymic: [],
        position: [],
        company: [],
      };
    },

    setPage(state, payload) {
      state.page = payload;
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
          },
          page: state.page,
          rows: state.rowsRoDisplay,
        }),
      })
        .then((response) => {
          return response.json();
        })
        .then((res) => {
          state.storedPermits = res.filteredPermits;
          state.pages = Math.ceil(+res.totalPermitsCount / state.rowsRoDisplay);

          state.stats.totalPermitsCount = +res.totalPermitsCount;
          state.stats.expiredPermitsCount = +res.expiredPermitsCount;
          state.stats.validPermitsCount = state.stats.totalPermitsCount - state.stats.expiredPermitsCount;
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
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
      state.page = 1;
      state.newPermitActionPath.path = `api/permits/${payload.id}`;
      state.newPermitActionPath.method = "PUT";

      this.commit("permit/setNewPermit", payload);
      state.cardTitle = "Отредактировать";
      state.permitEditing = true;
      this.commit("permit/filterPermits");
    },

    copyPermit(state, payload) {
      state.page = 1;
      this.commit("permit/setNewPermit", payload);
      this.commit("permit/setNextPermitNumber");
      state.newPermit.dateStart = null;
      state.newPermit.dateEnd = null;
      state.cardTitle = "Добавить";
      state.permitEditing = false;
      this.commit("permit/filterPermits");
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
      state.cardTitle = "Добавить";
      state.permitEditing = false;
    },

    setNextPermitNumber(state, payload) {
      let currentYear = new Date().getFullYear();

      if (payload) {
        currentYear = payload;
      }

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
          this.commit("permit/resetNewPermit");
          this.commit("permit/setNextPermitNumber");
          this.commit("permit/filterPermits");
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
          this.commit("permit/resetNewPermit");
          this.commit("permit/setNextPermitNumber");
          this.commit("permit/filterPermits");
          return res; // res is an array of objects, where each object contain permit data
        })
        .catch((err) => {
          console.log(err);
        });
    },

    deselectAllPermitsToPrint(state, payload) {
      state.printBag = [];
    },
  },
};
