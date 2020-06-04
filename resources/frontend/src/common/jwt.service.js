const ID_TOKEN_KEY = "vic_token";
const USER_DETAIL = "vic_user";

export const getToken = () => {
  return window.localStorage.getItem(ID_TOKEN_KEY);
};

export const saveToken = token => {
  window.localStorage.setItem(ID_TOKEN_KEY, token);
};

export const destroyToken = () => {
  window.localStorage.removeItem(ID_TOKEN_KEY);
};

export const getUser = () => {
  return window.localStorage.getItem(USER_DETAIL);
};

export const saveUser = user => {
  window.localStorage.setItem(USER_DETAIL, user);
};

export const destroyUser = () => {
  window.localStorage.removeItem(USER_DETAIL);
};



export default { getToken, saveToken, destroyToken, getUser, saveUser, destroyUser };
