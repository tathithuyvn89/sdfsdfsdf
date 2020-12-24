import '@/bootstrap';
import { Message } from 'element-ui';
import store from '../store';
import router from '../router/index';
import i18n from '../lang/index';
// import { isLogged, setLogged } from '@/utils/auth';
// Create axios instance
const service = window.axios.create({
  baseURL: process.env.MIX_BASE_API,
  timeout: 10000, // Request timeout
});
// Request intercepter
service.interceptors.request.use(
  config => {
    // const token = isLogged();
    // if (token) {
    //   config.headers['Authorization'] = 'Bearer ' + isLogged(); // Set JWT token
    // }
    return config;
  },
  error => {
    // Do something with request error
  //  console.log(error); // for debug
    Promise.reject(error);
  }
);
// response pre-processing
service.interceptors.response.use(
  response => {
    return response.data;
  },
  error => {
    let message = error.message;
    if (error.response.data.error === 'dashboard.message.timeout' && error.response.status === 401) {
      if (store.getters.roles.toString() === 'user') {
        router.push('/');
      } else {
        router.push('/login');
      }
    }
    if (error.response.data && error.response.data.errors) {
      message = error.response.data.errors;
    } else if (error.response.data && error.response.data.error) {
      message = error.response.data.error;
    }
    Message.closeAll();
    Message({
      message: i18n.t(message),
      type: 'error',
      duration: 5 * 1000,
    });
    return Promise.reject(error);
  }
);
export default service;
