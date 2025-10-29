<template>
  <section class="contact-us-page">
    <div class="contact-overlay">
      <div class="contact-card-local">
        <h1 style="color: grey; font-family: Lucida Handwriting;">Contact Us</h1>
        <p>Choose your role and fill the form below.</p>

        <form @submit.prevent="handleSubmit">
          <label for="role">I am a:</label>
          <div class="input-group">
            <select id="role" v-model="role">
              <option value="" disabled>Select role</option>
              <option value="client">Client</option>
              <option value="staff">Staff</option>
            </select>
            <span v-if="roleError" class="error-text">Please select a role!</span>
          </div>

          <!-- CLIENT -->
          <div v-if="role === 'client'" class="fields">
            <div class="input-row">
              <div class="input-group">
                <input type="text" v-model="form.client.name" placeholder="Name" />
                <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
              </div>
              <div class="input-group">
                <input type="text" v-model="form.client.surname" placeholder="Surname" />
                <span v-if="errors.surname" class="error-text">{{ errors.surname }}</span>
              </div>
            </div>
            <div class="input-row">
              <div class="input-group">
                <input type="email" v-model="form.client.email" placeholder="Email" />
                <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
              </div>
              <div class="input-group">
                <input type="tel" v-model="form.client.phone" placeholder="Phone Number" />
                <span v-if="errors.phone" class="error-text">{{ errors.phone }}</span>
              </div>
            </div>
            <div class="input-group">
              <textarea v-model="form.client.comment" placeholder="Write your comment"></textarea>
              <span v-if="errors.comment" class="error-text">{{ errors.comment }}</span>
            </div>
          </div>

          <!-- STAFF -->
          <div v-if="role === 'staff'" class="fields">
            <div class="input-row">
              <div class="input-group">
                <input type="text" v-model="form.staff.name" placeholder="Name" />
                <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
              </div>
              <div class="input-group">
                <input type="text" v-model="form.staff.surname" placeholder="Surname" />
                <span v-if="errors.surname" class="error-text">{{ errors.surname }}</span>
              </div>
              <div class="input-group">
                <select v-model="form.staff.position">
                  <option value="" disabled>Select position</option>
                  <option value="Manager">Manager</option>
                  <option value="Trainer">Trainer</option>
                  <option value="Shankist">Shankist</option>
                  <option value="Maintenance">Maintenance</option>
                </select>
                <span v-if="errors.position" class="error-text">{{ errors.position }}</span>
              </div>
            </div>
            <div class="input-group">
              <input type="email" v-model="form.staff.email" placeholder="Email" />
              <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
            </div>
            <div class="input-group">
              <textarea v-model="form.staff.comment" placeholder="Write your comment"></textarea>
              <span v-if="errors.comment" class="error-text">{{ errors.comment }}</span>
            </div>
          </div>

          <button type="submit" class="btn-local">Send Message</button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive } from 'vue';
import '@/assets/global.css';

const role = ref('');
const roleError = ref(false);

const form = reactive({
  client: { name: '', surname: '', email: '', phone: '', comment: '' },
  staff: { name: '', surname: '', position: '', email: '', comment: '' },
});

const errors = reactive({
  name: '',
  surname: '',
  email: '',
  phone: '',
  comment: '',
  position: '',
});

const handleSubmit = () => {
  // Reset errors
  Object.keys(errors).forEach((key) => (errors[key] = ''));
  roleError.value = false;

  if (!role.value) {
    roleError.value = true;
    return;
  }

  let valid = true;

  if (role.value === 'client') {
    if (!form.client.name) {
      errors.name = 'Please enter your name.';
      valid = false;
    }
    if (!form.client.surname) {
      errors.surname = 'Please enter your surname.';
      valid = false;
    }
    if (!form.client.email) {
      errors.email = 'Please enter your email.';
      valid = false;
    }
    if (!form.client.comment) {
      errors.comment = 'Please write a comment.';
      valid = false;
    }
  }

  if (role.value === 'staff') {
    if (!form.staff.name) {
      errors.name = 'Please enter your name.';
      valid = false;
    }
    if (!form.staff.surname) {
      errors.surname = 'Please enter your surname.';
      valid = false;
    }
    if (!form.staff.position) {
      errors.position = 'Please select your position.';
      valid = false;
    }
    if (!form.staff.email) {
      errors.email = 'Please enter your email.';
      valid = false;
    }
    if (!form.staff.comment) {
      errors.comment = 'Please write a comment.';
      valid = false;
    }
  }

  if (valid) {
    alert('Form submitted successfully ✅');
    console.log('Data sent:', form[role.value]);

    // Reset all fields
    form.client = { name: '', surname: '', email: '', phone: '', comment: '' };
    form.staff = { name: '', surname: '', position: '', email: '', comment: '' };

    // Reset role to go back to the start
    role.value = '';
  }
};
</script>

<style scoped>
.contact-us-page {
  position: fixed !important;
  top: 0;
  left: 0;
  width: 100vw !important;
  height: 100vh !important;
  background: url('@/img/contactus.jpg') center/cover no-repeat !important;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden !important;
  z-index: 1000;
}

.contact-overlay {
  background: rgba(0, 0, 0, 0.5);
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
}

.contact-card-local {
  background: #fff;
  padding: 2rem 3rem;
  border-radius: 1.5rem;
  width: 90%;
  max-width: 700px;
  text-align: center;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
  display: flex;
  flex-direction: column;
}

.input-row {
  display: flex;
  gap: 1rem;
}

.input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.error-text {
  color: red;
  font-size: 0.8rem;
  margin-top: 0.3rem;
  text-align: left;
  width: 100%;
}

select,
input,
textarea {
  width: 100%;
  padding: 0.8rem 1rem;
  border-radius: 999px;
  border: 1px solid #ccc;
  font-size: 1rem;
  outline: none;
}

textarea {
  min-height: 80px;
  border-radius: 1rem;
  resize: none;
}

select:focus,
input:focus,
textarea:focus {
  border-color: #1a73e8;
  box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
}

.btn-local {
  background: #1a73e8;
  color: #fff;
  border: none;
  padding: 0.8rem;
  border-radius: 999px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-local:hover {
  background: #155ab6;
}
</style>
