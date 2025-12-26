<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <h3 class="mb-3">{{ user ? "Edit User" : "Add User" }}</h3>

      <form @submit.prevent="handleSubmit">

        <div class="form-row" v-for="row in fieldRows" :key="row[0].model">
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

        <div class="d-flex justify-content-end mt-4">
          <button type="button" class="btn btn-secondary me-2" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary">{{ user ? "Update" : "Create" }}</button>
        </div>

      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  props: ["user"],

  data() {
    return {
      formData: {
        username: "",
        name: "",
        surname: "",
        email: "",
        password: "",
        dob: "",
        RoleID: 2,  
        gender: "",
        fitness_goal: "",
        activity_level: "",
        focus_area: "",
        phone: "",
        address: "",
        training_days: 0
      },

      fields: [
        { label: "Username", model: "username", type: "text" },
        { label: "Name", model: "name", type: "text" },
        { label: "Surname", model: "surname", type: "text" },
        { label: "Email", model: "email", type: "email" },
        { label: "Password", model: "password", type: "password" },
        { label: "DOB", model: "dob", type: "date" },
        { label: "Role", model: "RoleID", type: "select", options: [
            { value: 1, label: "Admin" },
            { value: 2, label: "Staff" },
            { value: 3, label: "User" }
          ] },
        { label: "Gender", model: "gender", type: "select", options: [
            { value: "male", label: "Male" },
            { value: "female", label: "Female" },
            { value: "other", label: "Other" }
          ] },
        { label: "Fitness Goal", model: "fitness_goal", type: "select", options: [
            { label: "Lose Fat", value: "lose fat" },
            { label: "Gain Muscle", value: "gain muscle" },
            { label: "Stay Fit", value: "stay fit" }
        ] },
        { label: "Activity Level", model: "activity_level", type: "select", options: [
            { label: "Low", value: "low" },
            { label: "Medium", value: "medium" },
            { label: "High", value: "high" }
          ] },
        { label: "Focus Area", model: "focus_area", type: "select", options: [
            { label: "Upper Body", value: "upper body" },
            { label: "Lower Body", value: "lower body" },
            { label: "Cardio", value: "cardio" }
        ] },
        { label: "Phone", model: "phone", type: "text" },
        { label: "Address", model: "address", type: "text" },
        { label: "Training Days", model: "training_days", type: "number" }
      ]
    };
  },

  computed: {
    fieldRows() {
      const rows = [];
      for (let i = 0; i < this.fields.length; i += 2) {
        rows.push(this.fields.slice(i, i + 2));
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
        gender: this.user.gender || "",
        fitness_goal: this.user.fitness_goal || "",
        activity_level: this.user.activity_level || "",
        focus_area: this.user.focus_area || "",
        phone: this.user.phone || "",
        address: this.user.address || "",
        training_days: this.user.training_days || 0
      };
      this.formData.password = "";
    }
  },

  methods: {
    async handleSubmit() {
      try {
        let dataToSend = { ...this.formData };
        if (!dataToSend.password) delete dataToSend.password;
        dataToSend.RoleID = dataToSend.RoleID || 2;
        dataToSend.phone = dataToSend.phone || "";
        dataToSend.address = dataToSend.address || "";
        dataToSend.training_days = dataToSend.training_days || 0;

        const headers = { Authorization: `Bearer ${localStorage.getItem("token")}` };

        if (this.user && this.user.UserID) {
          await axios.put(`${BASE_URL}/users/${this.user.UserID}`, dataToSend, { headers });
        } else {
          await axios.post(`${BASE_URL}/users`, dataToSend, { headers });
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
  z-index: 1050;
  overflow-y: auto;
  padding: 20px;
}

.modal-content {
  background: white;
  padding: 25px;
  width: 90%;
  max-width: 1000px;
  max-height: 90vh;
  border-radius: 12px;
  overflow-y: auto;
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

</style>
