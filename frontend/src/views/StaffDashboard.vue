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

            <p class="info-line">
              <img :src="locationIcon" class="icon" />
              <span class="label">Description:</span>
              <span class="description-text">
                {{ user.description ? user.description : 'No description added yet. Click Edit Profile to add your trainer bio.' }}
              </span>
            </p>
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

            <div class="form-group">
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
import axios from "axios";

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
    const res = await axios.get(`http://127.0.0.1:8000/api/users/${localUser.UserID}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    });
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

const imageUrl = (user) => {
  return user.photo ? `http://127.0.0.1:8000/uploads/profilephotos/${user.photo}` : defaultPhoto;
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
    const res = await axios.post(
      `http://127.0.0.1:8000/api/users/${user.value.UserID}`,
      formData,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "multipart/form-data",
        },
      }
    );

    user.value = res.data;
    localStorage.setItem("user", JSON.stringify(res.data));
    editing.value = false;
    photoPreview.value = null;
    photoFile.value = null;
    alert("Profile updated successfully!");
  } catch (error) {
    console.error("Error updating profile:", error);
    alert("Failed to update profile.");
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
  background: linear-gradient(135deg, #0f1115, #1c1f26);
  color: #f9fafb;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-main {
  flex: 1;
  margin-left: 240px;
  padding: 3rem;
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
  max-width: 800px;
  background: linear-gradient(145deg, #1f2937, #111827);
  border-radius: 16px;
  padding: 2.5rem;
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
  gap: 2rem;
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
  min-width: 90px;
}
.description-text {
  display: inline-block;
  margin-left: 0.4rem;
  color: #d1d5db;
  max-width: calc(100% - 120px);
}
..profile-form textarea {
  width: 100%;
  min-height: 120px;
  resize: vertical;
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(255,255,255,0.12);
  background: rgba(255,255,255,0.05);
  color: #fff;
}

.edit-btn {
  margin-top: 2rem;
  width: 100%;
  background-color: #2563eb;
  color: #f9fafb;
  font-weight: 600;
  padding: 0.8rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.25s ease;
}

.edit-btn:hover {
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

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.form-group label {
  font-weight: 600;
  font-size: 1rem;
  color: #60a5fa;
}

.form-group input,
.form-group select {
  padding: 0.6rem 1rem;
  border-radius: 8px;
  border: none;
  outline: none;
  font-size: 1rem;
  background-color: #1f2937;
  color: #f9fafb;
  transition: all 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
  box-shadow: 0 0 0 2px #2563eb;
}

.save-btn {
  margin-top: 1.2rem;
  background-color: #2563eb;
  color: #f9fafb;
  font-weight: 600;
  padding: 0.7rem 1.4rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.25s ease;
}

.save-btn:hover {
  background-color: #1d4ed8;
}
</style>
