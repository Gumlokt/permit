import { createStore } from "vuex";

import permit from './permit';
import popup from './popup';

export const store = createStore({
  modules: {
    permit,
    popup
  }
});
