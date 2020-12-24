// import defaultSettings from '@/settings';
// const { showSettings, tagsView, fixedHeader, sidebarLogo, theme } = defaultSettings;
import { CLASS, DEPARTMENT, SEARCH, TAG, SURVEY, QUIZ, OPTION } from '../../constant_keys';
const state = {
  ALL_CLASSES: [],
  ALL_ID_CLASS: [],
  ALL_ID_SURVEY: [],
  ALL_DEPARTMENTS: [],
  ALL_TAGS: [],
  LANG_SELECT: '',
  CLASS_SELECT: null,
  SURVEY_SELECT: null,
  [CLASS.KEY_ID]: '',
  [SURVEY.KEY_ID]: '',
  [QUIZ.KEY_ID]: '',
  [OPTION.KEY_ID]: '',
  [TAG.KEY_LIST]: '',
  [SEARCH.KEY_VALUE]: '',
  [DEPARTMENT.KEY_ID]: '',
  //   sidebarLogo: sidebarLogo,
};

const mutations = {
  CHANGE_SELECTION: (state, { key, value }) => {
    if (Object.prototype.hasOwnProperty.call(state, key)) {
      state[key] = value;
    }
  },
  STORE_SELECTIONS: (state, { key, value }) => {
    if (Object.prototype.hasOwnProperty.call(state, key)) {
      state[key] = value;
    }
  },
};

const actions = {
  changeSelection({ commit }, data) {
    commit('CHANGE_SELECTION', data);
  },
  storeSelections({ commit }, data) {
    for (var i = 0; i < data.length; i++) {
      commit('STORE_SELECTIONS', data[i]);
    }
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
};
