<template>
  <div class="staff-dashboard">
    <Sidebar />

    <main class="dashboard-main">
      <section class="profile-section">
        <div class="profile-card">
          <div class="profile-header">
            <div class="photo-wrapper">
              <img 
                :src="photoPreview || (user.photo ? imageUrl(user) : defaultPhoto)" 
                alt="Profile Photo" 
                class="profile-photo"
                @click="editing && selectPhoto()"
              />
            </div>
            <div class="profile-info">
              <h2>Welcome back, {{ user.name }} {{ user.surname }}</h2>
              <p class="role">{{ user.roleName }}</p>
            </div>
          </div>

          <div class="profile-details">
            <div class="profile-column">
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

              <p class="info-line info-line--description">
                <img :src="locationIcon" class="icon" />
                <span class="label">Description:</span>
                <span class="description-text">
                  {{ user.description ? user.description : 'No description added yet. Click Edit Profile to add your trainer bio.' }}
                </span>
              </p>
            </div>
          </div>

          <button class="edit-btn" @click="toggleEdit">
            {{ editing ? 'Cancel Edit' : 'Edit Profile' }}
          </button>

          <form v-if="editing" class="profile-form" @submit.prevent="saveProfile">
            <input 
              type="file" 
              ref="photoInput" 
              style="display:none" 
              accept="image/*" 
              @change="onPhotoChange"
            />

            <div class="form-group">
              <label for="email">Email:</label>
              <input id="email" v-model="user.email" type="email" required />
            </div>

            <div class="form-group">
              <label for="phone">Phone:</label>
              <input id="phone" v-model="user.phone" type="text" />
            </div>

            <div class="form-group">
              <label for="address">Address:</label>
              <input id="address" v-model="user.address" type="text" />
            </div>

            <div class="form-group">
              <label for="dob">DOB:</label>
              <input id="dob" v-model="user.dob" type="date" />
            </div>

            <div class="form-group">
              <label for="gender">Gender:</label>
              <select id="gender" v-model="user.gender">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>

            <div class="form-group form-group--full">
              <label for="description">Description:</label>
              <textarea id="description" v-model="user.description" rows="4" placeholder="Describe your specialties and training style"></textarea>
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
          </form>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Sidebar from "@/components/Sidebar.vue";
import { useRouter } from "vue-router";
import api from "@/services/axios";
import { showSuccess, showError } from "@/stores/notifications";

const router = useRouter();
const user = ref({});
const editing = ref(false);
const photoPreview = ref(null);
const photoFile = ref(null);
const defaultPhoto = "/assets/default-avatar.png";
const photoInput = ref(null);

const emailIcon = new URL('../icons/email.png', import.meta.url).href;
const phoneIcon = new URL('../icons/telephone.png', import.meta.url).href;
const locationIcon = new URL('../icons/location.png', import.meta.url).href;
const calendarIcon = new URL('../icons/birthday.png', import.meta.url).href;
const genderIcon = new URL('../icons/gender.png', import.meta.url).href;

onMounted(loadUser);

async function loadUser() {
  const storedUser = localStorage.getItem("user");
  if (!storedUser) {
    router.push("/login");
    return;
  }

  const localUser = JSON.parse(storedUser);

  try {
    const res = await api.get(`/users/${localUser.UserID}`);
    user.value = res.data;
    localStorage.setItem("user", JSON.stringify(res.data));
  } catch (error) {
    console.error("Error loading user:", error);
    router.push("/login");
  }
}

const toggleEdit = () => {
  editing.value = !editing.value;
  if (!editing.value) {
    photoPreview.value = null;
    photoFile.value = null;
  }
};

const selectPhoto = () => {
  photoInput.value.click();
};

const onPhotoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    photoFile.value = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const IMG_BASE = api.defaults.baseURL.replace(/\/api\/?$/, "");
const imageUrl = (user) => {
  return user.photo ? `${IMG_BASE}/uploads/profilephotos/${user.photo}` : defaultPhoto;
};

const saveProfile = async () => {
  const formData = new FormData();
  formData.append("_method", "PUT"); // MUST for Laravel PUT with multipart

  formData.append("email", user.value.email);
  formData.append("phone", user.value.phone || "");
  formData.append("address", user.value.address || "");
  formData.append("dob", user.value.dob || "");
  formData.append("gender", user.value.gender || "");
  formData.append("description", user.value.description || "");

  if (photoFile.value) {
    formData.append("photo", photoFile.value);
  }

  try {
    const res = await api.post(`/users/${user.value.UserID}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    user.value = res.data;
    localStorage.setItem("user", JSON.stringify(res.data));
    editing.value = false;
    photoPreview.value = null;
    photoFile.value = null;
    showSuccess("Profile updated successfully.");
  } catch (error) {
    console.error("Error updating profile:", error);
    showError("Failed to update profile.");
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleDateString();
};
</script>


<style scoped>
.staff-dashboard {
  display: flex;
  width: 100vw;
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg-card), var(--bg-card));
  color: #f9fafb;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-main {
  flex: 1;
  margin-left: var(--sidebar-width);
  padding: var(--page-top-with-navbar) 3rem 3rem;
  display: flex;
  justify-content: center;
}

.profile-section {
  width: 100%;
  display: flex;
  justify-content: center;
}

.profile-card {
  width: 100%;
  max-width: 1240px;
  background: linear-gradient(145deg, var(--bg-card), var(--bg-card));
  border-radius: 16px;
  padding: 2.75rem 3.5rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.7);
  color: #f9fafb;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.8);
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 2.25rem;
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
  margin: 0;
  font-size: clamp(1.55rem, 3vw, 3rem);
  line-height: 1.08;
  letter-spacing: -0.04em;
  max-width: 900px;
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
.description-text {
  display: inline;
  margin-left: 0;
  color: #d1d5db;
  flex: 1;
}
.info-line--description {
  align-items: flex-start;
}
.profile-form textarea {
  width: 100%;
  min-height: 120px;
  resize: vertical;
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(255,255,255,0.12);
  background: rgba(255,255,255,0.05);
  color: var(--text-strong);
}

.edit-btn {
  margin-top: 1.5rem;
  width: 100%;
  max-width: 320px;
  background-color: var(--accent-blue);
  color: var(--accent-purple);
  font-weight: 600;
  font-size: var(--text-md);
  padding: 0.85rem 1.25rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.25s ease;
  border: none;
  display: block;
  margin-left: auto;
}

.edit-btn:hover {
  background-color: #1d4ed8;
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

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  font-size: var(--text-md);
  color: #60a5fa;
}

.form-group input,
.form-group select {
  padding: 0.85rem 1rem;
  border-radius: 8px;
  border: none;
  outline: none;
  font-size: var(--text-md);
  background-color: var(--bg-card);
  color: #f9fafb;
  transition: all 0.2s ease;
}

.form-group--full {
  grid-column: 1 / -1;
}

.form-group input:focus,
.form-group select:focus {
  box-shadow: 0 0 0 2px var(--accent-blue);
}

.save-btn {
  margin-top: 0.5rem;
  grid-column: 1 / -1;
  max-width: 320px;
  background-color: var(--accent-blue);
  color: var(--accent-purple);
  font-weight: 600;
  font-size: var(--text-base);
  padding: 0.85rem 1.25rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.25s ease;
  border: none;
}

@media (max-width: 900px) {
  .staff-dashboard {
    flex-direction: column;
    padding-top: var(--navbar-height);
  }

  .staff-dashboard :deep(.sidebar) {
    position: relative;
    width: 100%;
    height: auto;
    padding: 1rem;
  }

  .dashboard-main {
    margin-left: 0;
    padding: 1rem;
  }

  .profile-details,
  .profile-form {
    grid-template-columns: 1fr;
  }

  .profile-details {
    gap: 1rem;
  }

  .profile-card {
    padding: 1.75rem 1.25rem;
  }

  .edit-btn,
  .save-btn {
    max-width: none;
    margin-left: 0;
  }
}

.save-btn:hover {
  background-color: #1d4ed8;
}
</style>
