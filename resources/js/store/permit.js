export default {
  namespaced: true,

  state: {
    editing: false,
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
        dateStart: "20.08.2021",
        dateEnd: "15.08.2021",
      };
      this.commit("permit/setNextPermitNumber", payload);
    },

    setNewPermit(state, payload) {
      const formatDate = (dateString) => {
        let date = new Date(dateString);
        let day = String(date.getDate()).padStart(2, "0");
        let month = String(date.getMonth() + 1).padStart(2, "0"); // month number is an index number which is zero based, that's why +1 needed
        let year = date.getFullYear();

        return `${day}.${month}.${year}`;
      };

      state.newPermit.id = payload.id;
      state.newPermit.number = payload.number;
      state.newPermit.surname = payload.surname;
      state.newPermit.forename = payload.forename;
      state.newPermit.patronymic = payload.patronymic;
      state.newPermit.company = payload.company;
      state.newPermit.position = payload.position;
      state.newPermit.dateStart = formatDate(payload.dateStart);
      state.newPermit.dateEnd = formatDate(payload.dateEnd);
    },

    editPermit(state, payload) {
      state.newPermitActionPath.path = `api/permits/${payload.id}`;
      state.newPermitActionPath.method = "PUT";

      this.commit("permit/setNewPermit", payload);
      state.editing = true;
    },

    copyPermit(state, payload) {
      this.commit("permit/setNewPermit", payload);
      this.commit("permit/setNextPermitNumber");
      state.newPermit.dateStart = null;
      state.newPermit.dateEnd = null;
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
    },

    setNextPermitNumber(state) {
      const res = fetch(`api/permits/last`, {
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
