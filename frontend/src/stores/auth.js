import { ref } from 'vue';

export const loggedIn = ref(false);


export function setLoggedIn(value) {
  loggedIn.value = value;

  if (!value) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
}


export function initializeAuth() {

}
