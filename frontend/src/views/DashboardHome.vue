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
        <div class="profile-column">
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
        </div>

        <div class="profile-column">
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
  if (!localUser?.UserID) return router.push("/login");

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
    training_days: user.value.training_days ?? null
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
  if (photoFile.value) {
    photoPreview.value = URL.createObjectURL(photoFile.value);
  }
}

const profilePhoto = computed(() =>
  user.value?.photo
    ? `http://127.0.0.1:8000/uploads/profilephotos/${user.value.photo}`
    : "/assets/default-avatar.png"
);

async function saveProfile() {
  try {
    const fd = new FormData();
    fd.append("_method", "PUT");

    Object.entries(form.value).forEach(([k, v]) => {
      if (k === "password" && !v) return;

      if (v === "") v = null;

      if (v !== null && v !== undefined) {
        fd.append(k, v);
      }
    });

    if (photoFile.value) {
      fd.append("photo", photoFile.value);
    }

    await axios.post(`${API}/users/${user.value.UserID}`, fd, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    await loadUser();

    editing.value = false;
    photoFile.value = null;
    photoPreview.value = null;

    alert("Profile updated successfully ✅");

  } catch (err) {
    console.log("ERROR:", err.response?.data);
    alert("Error updating profile ❌");
  }
}

function formatDate(d) {
  return new Date(d).toLocaleDateString();
}
</script>

<style scoped>
.profile-section {
  display: flex;
  justify-content: center;
  padding: 6rem 3rem 3rem;
  width: 100%;
}

.profile-card {
  max-width: 1240px;
  width: 100%;
  background: linear-gradient(145deg, var(--bg-card), var(--bg-card));
  border-radius: 16px;
  padding: 2.75rem 3.5rem;
  color: var(--text-strong);
  box-shadow: 0 10px 30px rgba(0,0,0,0.7);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.8);
}

.profile-header {
  display: flex;
  gap: 2.25rem;
  align-items: center;
  margin-bottom: 2.25rem;
}

.photo-wrapper {
  position: relative;
}

.profile-photo {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid var(--accent-blue);
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-photo:hover {
  transform: scale(1.1);
  box-shadow: 0 0 15px var(--accent-blue);
}

.profile-info h2 {
  font-size: clamp(1.55rem, 3vw, 3rem);
  line-height: 1.08;
  letter-spacing: -0.04em;
  max-width: 900px;
  margin: 0;
  font-weight: 600;
  color: #97dffc;
}

.profile-info .role {
  color: var(--accent-blue);
  font-weight: 600;
  margin-top: 0.35rem;
  font-size: var(--text-lg);
}

.profile-details {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.25rem 10rem;
  margin-top: 1.75rem;
}

.profile-column {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.info-line {
  display: flex;
  align-items: flex-start;
  gap: 0.8rem;
  font-size: var(--text-md);
  line-height: 1.7;
}

.icon {
  width: 45px;
  height: 45px;
  object-fit: contain;
  flex-shrink: 0;
}

.profile-details .label {
  font-weight: 600;
  color: var(--accent-blue);
  margin-right: 0.3rem;
}

.edit-btn,
.save-btn,
.photo-btn {
  margin-top: 1.5rem;
  padding: 0.85rem 1.25rem;
  width: 100%;
  max-width: 320px;
  background: var(--accent-blue);
  border-radius: 10px;
  font-weight: bold;
  font-size: var(--text-md);
  color: var(--accent-purple);
  border: none;
  cursor: pointer;
  transition: background 0.25s ease;
}

.edit-btn {
  display: block;
  margin-left: auto;
}

.edit-btn:hover,
.save-btn:hover,
.photo-btn:hover {
  background-color: var(--accent-blue-dark);
  color: var(--text-strong);
}

.profile-form {
  margin-top: 2rem;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.25rem 2rem;
  background-color: var(--bg-card);
  padding: 2.25rem;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.6);
}

.profile-form input,
.profile-form select {
  padding: 0.85rem 1rem;
  border-radius: 8px;
  border: 1px solid rgba(var(--theme-ice-rgb), 0.16);
  outline: none;
  background-color: var(--bg-card);
  color: var(--text-strong);
  font-size: var(--text-md);
  transition: all 0.2s ease;
}

.photo-btn,
.save-btn {
  grid-column: 1 / -1;
}

@media (max-width: 900px) {
  .profile-details,
  .profile-form {
    grid-template-columns: 1fr;
  }

  .profile-details {
    gap: 1rem;
  }

  .profile-section {
    padding: 1.5rem 1rem 2rem;
  }

  .profile-card {
    padding: 1.75rem 1.25rem;
  }

  .edit-btn,
  .save-btn,
  .photo-btn {
    max-width: none;
    margin-left: 0;
  }
}

.profile-form input:focus,
.profile-form select:focus {
  box-shadow: 0 0 0 2px var(--accent-blue);
}
</style>
