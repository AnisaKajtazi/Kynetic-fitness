<template>
  <section class="contact-us-page">
    <div class="contact-overlay">
      <div class="contact-card-local">
        <h1 style="color: grey; font-family: Lucida Handwriting;">Contact Us</h1>
        <p>Fill the form below to send us a message.</p>

        <form @submit.prevent="handleSubmit">
          <div class="input-group">
            <input type="text" v-model="form.name" placeholder="Name" />
            <span v-if="errors.name" class="error-text">{{ errors.name }}</span>
          </div>

          <div class="input-group">
            <input type="text" v-model="form.surname" placeholder="Surname" />
            <span v-if="errors.surname" class="error-text">{{ errors.surname }}</span>
          </div>

          <div class="input-group">
            <input type="email" v-model="form.email" placeholder="Email" />
            <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
          </div>

          <div class="input-group">
            <input type="text" v-model="form.phone" placeholder="Phone Number" />
            <span v-if="errors.phone" class="error-text">{{ errors.phone }}</span>
          </div>

          <div class="input-group">
            <textarea v-model="form.comment" placeholder="Write your comment"></textarea>
            <span v-if="errors.comment" class="error-text">{{ errors.comment }}</span>
          </div>

          <button type="submit" class="btn-local">Send Message</button>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import api from '../services/axios';

const roleId = ref(null);
const staffType = ref('');

const form = reactive({
  name: '',
  surname: '',
  email: '',
  phone: '',
  comment: ''
});

const errors = reactive({
  name: '',
  surname: '',
  email: '',
  phone: '',
  comment: ''
});

onMounted(async () => {
  try {
    const { data } = await api.get('/auth/me');
    roleId.value = data.role_id;

    if (roleId.value === 3) {
      staffType.value = data.staff_type; // staff marrin pozicionin automatik
    }
  } catch(err) {
    console.error('Error fetching user info:', err.response || err);
  }
});

const handleSubmit = async () => {
  Object.keys(errors).forEach(k => errors[k]='');

  let valid = true;
  if (!form.name) { errors.name='Please enter name'; valid=false; }
  if (!form.surname) { errors.surname='Please enter surname'; valid=false; }
  if (!form.email) { errors.email='Please enter email'; valid=false; }
  if (!form.comment) { errors.comment='Please write a comment'; valid=false; }

  if (!valid) return;

  try {
    await api.post('/contact-us', {
      ...form,
      role: roleId.value === 3 ? 'staff' : 'client',
      position: roleId.value === 3 ? staffType.value : null
    });

    alert('Message sent successfully ✅');

    Object.keys(form).forEach(k => form[k]='');

  } catch(err) {
    console.error('Error submitting contact:', err.response || err);
    alert('Failed to send message!');
  }
};
</script>

<style scoped>
.contact-us-page {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: url('@/img/contactus.jpg') center/cover no-repeat;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
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
  max-width: 600px;
  text-align: center;
  box-shadow: 0 15px 40px rgba(0,0,0,0.4);
  display: flex;
  flex-direction: column;
}

.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
  width: 100%;
}

input, textarea {
  width: 100%;
  padding: 0.8rem 1rem;
  border-radius: 999px;
  border: 1px solid #ccc;
  font-size: 1rem;
  outline: none;
}

textarea {
  min-height: 100px;
  border-radius: 1rem;
  resize: none;
}

input:focus, textarea:focus {
  border-color: #1a73e8;
  box-shadow: 0 0 0 2px rgba(26,115,232,0.2);
}

.error-text {
  color: red;
  font-size: 0.8rem;
  margin-top: 0.3rem;
  text-align: left;
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
