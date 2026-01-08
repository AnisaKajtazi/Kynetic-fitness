<template>
  <section class="profile-section" v-if="user">
    <div class="profile-card">

      <div class="profile-header">
        <div class="photo-wrapper">
          <img
            :src="photoPreview || profilePhoto"
            class="profile-photo"
            @click="editing && selectPhoto()"
          />
        </div>

        <div class="profile-info">
          <h2>{{ user.name }} {{ user.surname }}</h2>
          <p class="role">{{ user.staff_type || 'User' }}</p>
        </div>
      </div>

      <div v-if="!editing" class="profile-details">
        <p class="info-line">
          <img :src="usernameIcon" class="icon" />
          <span class="label">Username:</span> {{ user.username }}
        </p>
        <p class="info-line">
          <img :src="emailIcon" class="icon" />
          <span class="label">Email:</span> {{ user.email }}
        </p>
        <p v-if="user.phone" class="info-line">
          <img :src="phoneIcon" class="icon" />
          <span class="label">Phone:</span> {{ user.phone }}
        </p>
        <p v-if="user.address" class="info-line">
          <img :src="locationIcon" class="icon" />
          <span class="label">Address:</span> {{ user.address }}
        </p>
        <p v-if="user.dob" class="info-line">
          <img :src="calendarIcon" class="icon" />
          <span class="label">DOB:</span> {{ formatDate(user.dob) }}
        </p>
        <p v-if="user.gender" class="info-line">
          <img :src="genderIcon" class="icon" />
          <span class="label">Gender:</span> {{ user.gender }}
        </p>
        <p v-if="user.fitness_goal" class="info-line">
          <img :src="goalIcon" class="icon" />
          <span class="label">Fitness Goal:</span> {{ user.fitness_goal }}
        </p>
        <p v-if="user.activity_level" class="info-line">
          <img :src="activityIcon" class="icon" />
          <span class="label">Activity Level:</span> {{ user.activity_level }}
        </p>
        <p v-if="user.focus_area" class="info-line">
          <img :src="focusIcon" class="icon" />
          <span class="label">Focus Area:</span> {{ user.focus_area }}
        </p>
        <p v-if="user.training_days !== null" class="info-line">
          <img :src="trainingIcon" class="icon" />
          <span class="label">Training Days:</span> {{ user.training_days }}
        </p>
      </div>

      <button class="edit-btn" @click="toggleEdit">
        {{ editing ? 'Cancel' : 'Edit Profile' }}
      </button>

      <form v-if="editing" class="profile-form" @submit.prevent="saveProfile">

        <input
          ref="photoInput"
          type="file"
          hidden
          accept="image/*"
          @change="onPhotoChange"
        />
        <button type="button" class="photo-btn" @click="selectPhoto">
          Change Photo
        </button>

        <input v-model="form.username" placeholder="Username" />
        <input v-model="form.name" placeholder="Name" />
        <input v-model="form.surname" placeholder="Surname" />
        <input v-model="form.email" type="email" placeholder="Email" />
        <input v-model="form.password" type="password" placeholder="New password (optional)" />

        <input v-model="form.phone" placeholder="Phone" />
        <input v-model="form.address" placeholder="Address" />
        <input v-model="form.dob" type="date" />

        <select v-model="form.gender">
          <option value="">Gender</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>

        <select v-model="form.fitness_goal">
          <option value="">Fitness Goal</option>
          <option value="lose fat">Lose Fat</option>
          <option value="gain muscle">Gain Muscle</option>
          <option value="stay fit">Stay Fit</option>
        </select>

        <select v-model="form.activity_level">
          <option value="">Activity Level</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
        </select>

        <select v-model="form.focus_area">
          <option value="">Focus Area</option>
          <option value="upper body">Upper Body</option>
          <option value="lower body">Lower Body</option>
          <option value="cardio">Cardio</option>
        </select>

        <input
          v-model="form.training_days"
          type="number"
          min="0"
          max="7"
          placeholder="Training Days"
        />

        <button class="save-btn">Save Changes</button>
      </form>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();
const API = "http://127.0.0.1:8000/api";
const token = localStorage.getItem("token");

const user = ref(null);
const form = ref({});
const editing = ref(false);

const photoInput = ref(null);
const photoFile = ref(null);
const photoPreview = ref(null);

const usernameIcon = new URL('../icons/user.png', import.meta.url).href;
const emailIcon = new URL('../icons/email.png', import.meta.url).href;
const phoneIcon = new URL('../icons/telephone.png', import.meta.url).href;
const locationIcon = new URL('../icons/location.png', import.meta.url).href;
const calendarIcon = new URL('../icons/birthday.png', import.meta.url).href;
const genderIcon = new URL('../icons/gender.png', import.meta.url).href;
const goalIcon = new URL('../icons/goal.png', import.meta.url).href;
const activityIcon = new URL('../icons/activity.png', import.meta.url).href;
const focusIcon = new URL('../icons/focus.png', import.meta.url).href;
const trainingIcon = new URL('../icons/training.png', import.meta.url).href;

onMounted(loadUser);

async function loadUser() {
  const localUser = JSON.parse(localStorage.getItem("user"));
  if (!localUser?.UserID) router.push("/login");

  const res = await axios.get(`${API}/users/${localUser.UserID}`, {
    headers: { Authorization: `Bearer ${token}` }
  });

  user.value = res.data;
  localStorage.setItem("user", JSON.stringify(res.data));
  resetForm();
}

function resetForm() {
  form.value = {
    username: user.value.username ?? "",
    name: user.value.name ?? "",
    surname: user.value.surname ?? "",
    email: user.value.email ?? "",
    password: "",
    phone: user.value.phone ?? "",
    address: user.value.address ?? "",
    dob: user.value.dob ? user.value.dob.slice(0, 10) : "",
    gender: user.value.gender ?? "",
    fitness_goal: user.value.fitness_goal ?? "",
    activity_level: user.value.activity_level ?? "",
    focus_area: user.value.focus_area ?? "",
    training_days: user.value.training_days ?? ""
  };
}

function toggleEdit() {
  editing.value = !editing.value;
  if (editing.value) resetForm();
}

function selectPhoto() {
  photoInput.value.click();
}

function onPhotoChange(e) {
  photoFile.value = e.target.files[0];
  photoPreview.value = URL.createObjectURL(photoFile.value);
}

const profilePhoto = computed(() =>
  user.value?.photo
    ? `http://127.0.0.1:8000/uploads/profilephotos/${user.value.photo}`
    : "/assets/default-avatar.png"
);

async function saveProfile() {
  const fd = new FormData();
  fd.append("_method", "PUT");
  Object.entries(form.value).forEach(([k,v]) => { if(v) fd.append(k,v); });
  if(photoFile.value) fd.append("photo", photoFile.value);
  await axios.post(`${API}/users/${user.value.UserID}`, fd, {
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "multipart/form-data" }
  });
  await loadUser();
  editing.value = false;
  photoFile.value = null;
  photoPreview.value = null;
  alert("Profile updated successfully ✅");
}

function formatDate(d) { return new Date(d).toLocaleDateString(); }
</script>

<style scoped>
.profile-section {
  display: flex;
  justify-content: center;
  padding: 3rem;
}

.profile-card {
  max-width: 850px;
  width: 100%;
  background: linear-gradient(145deg, #1f2937, #111827);
  border-radius: 16px;
  padding: 2.5rem;
  color: #f9fafb;
  box-shadow: 0 10px 30px rgba(0,0,0,0.7);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.8);
}

.profile-header {
  display: flex;
  gap: 2rem;
  align-items: center;
  margin-bottom: 2rem;
}

.photo-wrapper {
  position: relative;
}

.profile-photo {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #2563eb;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-photo:hover {
  transform: scale(1.1);
  box-shadow: 0 0 15px #2563eb;
}

.profile-info h2 {
  margin: 0;
  font-size: 2rem;
  font-weight: 700;
}

.profile-info .role {
  color: #60a5fa;
  font-weight: 600;
  margin-top: 0.25rem;
  font-size: 1.1rem;
}

.profile-details {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  margin-top: 1rem;
}

.info-line {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.icon {
  width: 35px;
  height: 35px;
  object-fit: contain;
}

.profile-details .label {
  font-weight: 600;
  color: #60a5fa;
  margin-right: 0.3rem;
}

.edit-btn,
.save-btn,
.photo-btn {
  margin-top: 1rem;
  padding: 0.7rem;
  width: 100%;
  background: #2563eb;
  border-radius: 10px;
  font-weight: bold;
  color: #f9fafb;
  transition: background 0.25s ease;
}

.edit-btn:hover,
.save-btn:hover,
.photo-btn:hover {
  background-color: #1d4ed8;
}

.profile-form {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  background-color: #111827;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.6);
}

.profile-form input,
.profile-form select {
  padding: 0.6rem;
  border-radius: 8px;
  border: none;
  outline: none;
  background-color: #1f2937;
  color: #f9fafb;
  transition: all 0.2s ease;
}

.profile-form input:focus,
.profile-form select:focus {
  box-shadow: 0 0 0 2px #2563eb;
}
</style>
