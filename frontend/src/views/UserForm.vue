<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <h3 class="mb-3">{{ user ? "Edit User" : "Add User" }}</h3>

      <form @submit.prevent="handleSubmit">
        <!-- Role Selector -->
        <div class="form-group">
          <label for="RoleID">Role</label>
          <select id="RoleID" v-model="formData.RoleID" class="form-control">
            <option disabled value="">Select Role</option>
            <option v-for="role in roles" :value="role.value" :key="role.value">{{ role.label }}</option>
          </select>
        </div>

        <!-- Staff Type Dropdown ONLY if RoleID = 3 -->
        <div class="form-group" v-if="formData.RoleID === 3">
          <label for="staff_type">Staff Type</label>
          <select id="staff_type" v-model="formData.staff_type" class="form-control">
            <option disabled value="">Select Staff Type</option>
            <option v-for="type in staffTypes" :value="type.value" :key="type.value">{{ type.label }}</option>
          </select>
        </div>

        <!-- Other fields dynamically -->
        <div class="form-row" v-for="row in visibleFieldRows" :key="row[0].model">
          <div class="form-group" v-for="field in row" :key="field.model">
            <label :for="field.model">{{ field.label }}</label>
            <input
              v-if="field.type !== 'select'"
              :type="field.type"
              class="form-control"
              :id="field.model"
              v-model="formData[field.model]"
              :placeholder="field.label"
            />
            <select
              v-else
              class="form-control"
              v-model="formData[field.model]"
            >
              <option disabled value="">Select {{ field.label }}</option>
              <option v-for="option in field.options" :value="option.value" :key="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Photo upload -->
        <div class="form-group">
          <label for="photo">Photo</label>
          <input type="file" id="photo" class="form-control" @change="handlePhotoChange" />
          <div v-if="photoPreview" class="mt-2">
            <img :src="photoPreview" alt="Photo Preview" class="photo-preview" />
          </div>
          <div v-else-if="formData.photo" class="mt-2">
            <img :src="BASE_URL_IMG + formData.photo" alt="Current Photo" class="photo-preview" />
          </div>
        </div>

        <div class="d-flex justify-content-end mt-4 flex-wrap gap-2">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary">{{ user ? "Update" : "Create" }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: ["user"],
  data() {
    return {
      BASE_URL: "http://127.0.0.1:8000/api",
      BASE_URL_IMG: "http://127.0.0.1:8000/",
      roles: [
        { value: 1, label: "Admin" },
        { value: 2, label: "User" },
        { value: 3, label: "Staff" }
      ],
      staffTypes: [
        { value: "trainer", label: "Trainer" },
        { value: "maintenance", label: "Maintenance" },
        { value: "service_staff", label: "Service Staff" }
      ],
      formData: {
        username: "",
        name: "",
        surname: "",
        email: "",
        password: "",
        dob: "",
        RoleID: 2,
        staff_type: "",
        gender: "",
        fitness_goal: "",
        activity_level: "",
        focus_area: "",
        phone: "",
        address: "",
        training_days: 0,
        photo: null
      },
      photoFile: null,
      photoPreview: null,
      fields: [
        { label: "Username", model: "username", type: "text" },
        { label: "Name", model: "name", type: "text" },
        { label: "Surname", model: "surname", type: "text" },
        { label: "Email", model: "email", type: "email" },
        { label: "Password", model: "password", type: "password" },
        { label: "DOB", model: "dob", type: "date" },
        { label: "Gender", model: "gender", type: "select", options: [
            { value: "male", label: "Male" },
            { value: "female", label: "Female" },
            { value: "other", label: "Other" }
          ] },
        { label: "Phone", model: "phone", type: "text" },
        { label: "Address", model: "address", type: "text" },
        { label: "Fitness Goal", model: "fitness_goal", type: "select", options: [
            { label: "Lose Fat", value: "lose fat" },
            { label: "Gain Muscle", value: "gain muscle" },
            { label: "Stay Fit", value: "stay fit" }
        ], role: 2 },
        { label: "Activity Level", model: "activity_level", type: "select", options: [
            { label: "Low", value: "low" },
            { label: "Medium", value: "medium" },
            { label: "High", value: "high" }
        ], role: 2 },
        { label: "Focus Area", model: "focus_area", type: "select", options: [
            { label: "Upper Body", value: "upper body" },
            { label: "Lower Body", value: "lower body" },
            { label: "Cardio", value: "cardio" }
        ], role: 2 },
        { label: "Training Days", model: "training_days", type: "number", role: 2 }
      ]
    };
  },
  computed: {
    visibleFields() {
      return this.fields.filter(f => !f.role || f.role === this.formData.RoleID);
    },
    visibleFieldRows() {
      const rows = [];
      const fields = this.visibleFields;
      for (let i = 0; i < fields.length; i += 2) {
        rows.push(fields.slice(i, i + 2));
      }
      return rows;
    }
  },
  mounted() {
    if (this.user) {
      this.formData = {
        ...this.user,
        dob: this.user.dob ? this.user.dob.split("T")[0] : "",
        RoleID: this.user.RoleID || 2,
        staff_type: this.user.staff_type || "",
        gender: this.user.gender || "",
        fitness_goal: this.user.fitness_goal || "",
        activity_level: this.user.activity_level || "",
        focus_area: this.user.focus_area || "",
        phone: this.user.phone || "",
        address: this.user.address || "",
        training_days: this.user.training_days || 0,
        photo: this.user.photo || null
      };
      this.formData.password = "";
      this.photoPreview = this.user.photo ? this.BASE_URL_IMG + this.user.photo : null;
    }
  },
  methods: {
    handlePhotoChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.photoFile = file;
        this.photoPreview = URL.createObjectURL(file);
      } else {
        this.photoFile = null;
        this.photoPreview = this.formData.photo ? this.BASE_URL_IMG + this.formData.photo : null;
      }
    },
    async handleSubmit() {
      try {
        const dataToSend = new FormData();
        for (const key in this.formData) {
          if (key === "photo") continue;
          dataToSend.append(key, this.formData[key] || "");
        }
        if (this.photoFile) dataToSend.append("photo", this.photoFile);
        if (this.formData.RoleID !== 3) dataToSend.set("staff_type", null);

        const headers = {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "multipart/form-data"
        };

        if (this.user && this.user.UserID) {
          await axios.post(`${this.BASE_URL}/users/${this.user.UserID}?_method=PUT`, dataToSend, { headers });
        } else {
          await axios.post(`${this.BASE_URL}/users`, dataToSend, { headers });
        }

        this.$emit("saved");
        this.$emit("close");
      } catch (error) {
        if (error.response && error.response.data) {
          console.error("Error saving user:", error.response.data);
          alert(JSON.stringify(error.response.data.errors || error.response.data.message));
        } else {
          console.error("Error saving user:", error);
        }
      }
    }
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  overflow-y: auto;
  padding: 20px;
}

.modal-content {
  background: white;
  padding: 25px;
  width: 95%;
  max-width: 1000px;
  max-height: 90vh;
  border-radius: 12px;
  overflow-y: auto;
  box-sizing: border-box;
  position: relative;
}

.form-row {
  display: flex;
  gap: 20px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.form-group {
  flex: 1 1 45%;
  display: flex;
  flex-direction: column;
  min-width: 200px;
}

.photo-preview {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}

@media (max-width: 768px) {
  .form-row {
    flex-direction: column;
  }
  .form-group {
    flex: 1 1 100%;
  }
  .d-flex.justify-content-end {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
