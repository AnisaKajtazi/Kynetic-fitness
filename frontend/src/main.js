import './assets/main.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import axios from 'axios'
import { initializeAuth } from './stores/auth'


axios.defaults.baseURL = "http://127.0.0.1:8000/api";

const token = localStorage.getItem("token");
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}


initializeAuth();

const app = createApp(App)
app.use(router)
app.mount('#app')
