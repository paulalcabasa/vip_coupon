/* eslint-disable */

import ApiService from "@/common/api.service";
import JwtService from "@/common/jwt.service";
import axios from "axios";

// action types
export const VERIFY_AUTH = "verifyAuth";
export const LOGIN = "login";
export const REFRESH_TOKEN = "refreshToken";
export const LOGOUT = "logout";
export const REGISTER = "register";
export const UPDATE_USER = "updateUser";

// mutation types
export const PURGE_AUTH = "logOut";
export const SET_AUTH = "setUser";
export const SET_ERROR = "setError";
export const SET_MESSAGE = "setMessage";
const state = {
  errors: [],
  message : '',
  user: {},
  isAuthenticated: !!JwtService.getToken()
};

const getters = {
  currentUser(state) {
    return state.user;
  },
  isAuthenticated(state) {
    return state.isAuthenticated;
  },
  message(state){
    return state.message;
  }
};

const actions = {
  [LOGIN](context, credentials) {
    return new Promise(resolve => {
      ApiService.post('/api/auth/login', credentials)
        .then(res => {
          if(res.status == 200){
            context.commit(SET_AUTH, res.data);
          }
          resolve(res);
        })
        .catch(error => {
          context.commit(SET_MESSAGE,'Invalid credentials.')
          context.commit(SET_ERROR, error);
          resolve(error);
        });
      /* ApiService.post("login", credentials)
        .then(({ data }) => {
          context.commit(SET_AUTH, data);
          resolve(data);
        })
        .catch(({ response }) => {
          context.commit(SET_ERROR, response.data.errors);
        }); */
    });
  },
  [REFRESH_TOKEN] (context) {
    return new Promise((resolve, reject) => {
      ApiService
        .post('api/auth/refresh')
        .then(response => {
          console.log(response);
          context.commit(SET_AUTH, response.data);
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  [LOGOUT](context) {
    context.commit(PURGE_AUTH);
  },
  [REGISTER](context, credentials) {
    return new Promise((resolve, reject) => {
      ApiService.post("users", { user: credentials })
        .then(({ data }) => {
          context.commit(SET_AUTH, data);
          resolve(data);
        })
        .catch(({ response }) => {
          context.commit(SET_ERROR, response.data.errors);
          reject(response);
        });
    });
  },
  [VERIFY_AUTH](context) {
    if (JwtService.getToken()) {
      ApiService.setHeader();
      ApiService.post('/api/auth/me')
        .then(res => {
          var user = {
            'user' : res.data.user,
            'access_token' : JwtService.getToken()
          };
          context.commit(SET_AUTH, user);
        })
        .catch(err => { 
          context.commit(PURGE_AUTH);
          context.commit(SET_MESSAGE, 'Your session has expired.');
       //   context.commit(SET_ERROR, ['Your session has expired.']);
        });
    }
    else {
      context.commit(PURGE_AUTH);
      return false;
    }   
    /* if (JwtService.getToken()) {
      ApiService.setHeader();
      ApiService.get("verify")
        .then(({ data }) => {
          context.commit(SET_AUTH, data);
        })
        .catch(({ response }) => {
          context.commit(SET_ERROR, response.data.errors);
        });
    } else {
      context.commit(PURGE_AUTH);
    } */
  },
  [UPDATE_USER](context, payload) {
    const { email, username, password, image, bio } = payload;
    const user = { email, username, bio, image };
    if (password) {
      user.password = password;
    }

    return ApiService.put("user", user).then(({ data }) => {
      context.commit(SET_AUTH, data);
      return data;
    });
  }
};

const mutations = {
  [SET_ERROR](state, error) {
    state.errors = error;
  },
  [SET_MESSAGE](state, message) {
    state.message = message;
  },
  [SET_AUTH](state, user) {
    state.isAuthenticated = true;
    state.user = user.user;
    state.errors = {};
    JwtService.saveToken(user.access_token);
  },
  [PURGE_AUTH](state) {
    state.isAuthenticated = false;
    state.user = {};
    state.errors = {};
    JwtService.destroyToken();
  }
};

export default {
  state,
  actions,
  mutations,
  getters
};
