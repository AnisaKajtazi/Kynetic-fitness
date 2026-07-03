<template>
  <div class="admin-modal-overlay" @click.self="$emit('close')">
    <div class="admin-modal-content">
      <h3 class="mb-3">{{ user ? 'Edit User' : 'Add User' }}</h3>

      <form @submit.prevent="handleSubmit">
        <div class="admin-form-group">
          <label for="RoleID">Role</label>
          <select id="RoleID" v-model="formData.RoleID" class="admin-form-control">
            <option disabled value="">Select Role</option>
            <option v-for="role in roles" :key="role.value" :value="role.value">{{ role.label }}</option>
          </select>
        </div>

        <div v-if="formData.RoleID === 3" class="admin-form-group">
          <label for="staff_type">Staff Type</label>
          <select id="staff_type" v-model="formData.staff_type" class="admin-form-control">
            <option disabled value="">Select Staff Type</option>
            <option v-for="type in staffTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
          </select>
        </div>

        <div v-for="(row, rowIndex) in visibleFieldRows" :key="rowIndex" class="admin-form-row">
          <div v-for="field in row" :key="field.model" class="admin-form-group">
            <label :for="field.model">{{ field.label }}</label>
            <template v-if="field.type === 'textarea'">
              <textarea
                :id="field.model"
                v-model="formData[field.model]"
                class="admin-form-control"
                :placeholder="field.label"
                rows="4"
              />
            </template>
            <input
              v-else-if="field.type !== 'select'"
              :id="field.model"
              v-model="formData[field.model]"
              :type="field.type"
              class="admin-form-control"
              :placeholder="field.label"
            />
            <select v-else v-model="formData[field.model]" class="admin-form-control">
              <option disabled value="">Select {{ field.label }}</option>
              <option v-for="option in field.options" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>
        </div>

        <div class="admin-form-group">
          <label for="photo">Photo</label>
          <input id="photo" type="file" class="admin-form-control" @change="handlePhotoChange" />
          <div v-if="photoPreview" class="mt-2">
            <img :src="photoPreview" alt="Photo Preview" class="photo-preview" />
          </div>
          <div v-else-if="formData.photo" class="mt-2">
            <img :src="BASE_URL_IMG + formData.photo" alt="Current Photo" class="photo-preview" />
          </div>
        </div>

        <div class="admin-form-actions d-flex justify-content-end mt-4 flex-wrap gap-2">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary">{{ user ? 'Update' : 'Create' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { showError } from '@/stores/notifications'

export default {
  props: ['user'],
  data() {
    return {
      BASE_URL: 'http://127.0.0.1:8000/api',
      BASE_URL_IMG: 'http://127.0.0.1:8000/',
      roles: [
        { value: 1, label: 'Admin' },
        { value: 2, label: 'User' },
        { value: 3, label: 'Staff' },
      ],
      staffTypes: [
        { value: 'trainer', label: 'Trainer' },
        { value: 'maintenance', label: 'Maintenance' },
        { value: 'service_staff', label: 'Service Staff' },
      ],
      formData: {
        username: '',
        name: '',
        surname: '',
        email: '',
        password: '',
        dob: '',
        RoleID: 2,
        staff_type: '',
        gender: '',
        fitness_goal: '',
        activity_level: '',
        focus_area: '',
        phone: '',
        address: '',
        training_days: 0,
        description: '',
        photo: null,
      },
      photoFile: null,
      photoPreview: null,
      fields: [
        { label: 'Username', model: 'username', type: 'text' },
        { label: 'Name', model: 'name', type: 'text' },
        { label: 'Surname', model: 'surname', type: 'text' },
        { label: 'Email', model: 'email', type: 'email' },
        { label: 'Password', model: 'password', type: 'password' },
        { label: 'DOB', model: 'dob', type: 'date' },
        {
          label: 'Gender',
          model: 'gender',
          type: 'select',
          options: [
            { value: 'male', label: 'Male' },
            { value: 'female', label: 'Female' },
            { value: 'other', label: 'Other' },
          ],
        },
        { label: 'Phone', model: 'phone', type: 'text' },
        { label: 'Address', model: 'address', type: 'text' },
        {
          label: 'Fitness Goal',
          model: 'fitness_goal',
          type: 'select',
          options: [
            { label: 'Lose Fat', value: 'lose fat' },
            { label: 'Gain Muscle', value: 'gain muscle' },
            { label: 'Stay Fit', value: 'stay fit' },
          ],
          role: 2,
        },
        {
          label: 'Activity Level',
          model: 'activity_level',
          type: 'select',
          options: [
            { label: 'Low', value: 'low' },
            { label: 'Medium', value: 'medium' },
            { label: 'High', value: 'high' },
          ],
          role: 2,
        },
        {
          label: 'Focus Area',
          model: 'focus_area',
          type: 'select',
          options: [
            { label: 'Upper Body', value: 'upper body' },
            { label: 'Lower Body', value: 'lower body' },
            { label: 'Cardio', value: 'cardio' },
          ],
          role: 2,
        },
        { label: 'Training Days', model: 'training_days', type: 'number', role: 2 },
        { label: 'Description', model: 'description', type: 'textarea', role: 3 },
      ],
    }
  },
  computed: {
    visibleFields() {
      return this.fields.filter((field) => !field.role || field.role === this.formData.RoleID)
    },
    visibleFieldRows() {
      const rows = []
      const fields = this.visibleFields
      for (let i = 0; i < fields.length; i += 2) {
        rows.push(fields.slice(i, i + 2))
      }
      return rows
    },
  },
  watch: {
    user: {
      immediate: true,
      handler(user) {
        this.resetForm(user)
      },
    },
  },
  methods: {
    resetForm(user = null) {
      this.formData = {
        username: '',
        name: '',
        surname: '',
        email: '',
        password: '',
        dob: '',
        RoleID: 2,
        staff_type: '',
        gender: '',
        fitness_goal: '',
        activity_level: '',
        focus_area: '',
        phone: '',
        address: '',
        training_days: 0,
        description: '',
        photo: null,
      }
      this.photoFile = null
      this.photoPreview = null

      if (user) {
        this.formData = {
          ...this.formData,
          username: user.username || '',
          name: user.name || '',
          surname: user.surname || '',
          email: user.email || '',
          password: '',
          dob: user.dob ? user.dob.split('T')[0] : '',
          RoleID: user.RoleID || 2,
          staff_type: user.staff_type || '',
          gender: user.gender || '',
          fitness_goal: user.fitness_goal || '',
          activity_level: user.activity_level || '',
          focus_area: user.focus_area || '',
          phone: user.phone || '',
          address: user.address || '',
          training_days: user.training_days ?? 0,
          description: user.description || '',
          photo: user.photo || null,
        }
      }
    },
    handlePhotoChange(e) {
      const file = e.target.files[0]
      if (file) {
        this.photoFile = file
        this.photoPreview = URL.createObjectURL(file)
      } else {
        this.photoFile = null
        this.photoPreview = this.formData.photo ? this.BASE_URL_IMG + this.formData.photo : null
      }
    },
    async handleSubmit() {
      try {
        const dataToSend = new FormData()
        const isEditing = Boolean(this.user && this.user.UserID)

        for (const [key, value] of Object.entries(this.formData)) {
          if (key === 'photo') continue
          dataToSend.append(key, value ?? '')
        }

        if (this.photoFile) dataToSend.append('photo', this.photoFile)
        if (this.formData.RoleID !== 3) dataToSend.set('staff_type', null)

        const headers = {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'multipart/form-data',
        }

        if (isEditing) {
          await axios.post(`${this.BASE_URL}/users/${this.user.UserID}?_method=PUT`, dataToSend, { headers })
        } else {
          await axios.post(`${this.BASE_URL}/users`, dataToSend, { headers })
        }

        this.$emit('saved')
        this.$emit('close')
      } catch (error) {
        if (error.response && error.response.data) {
          console.error('Error saving user:', error.response.data)
          showError(JSON.stringify(error.response.data.errors || error.response.data.message))
        } else {
          console.error('Error saving user:', error)
          showError('Error saving user.')
        }
      }
    },
  },
}
</script>

<style scoped>
.photo-preview {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  max-height: 120px;
}

@media (max-width: 768px) {
  .d-flex.justify-content-end {
    flex-direction: column;
    gap: 10px;
  }
}
</style>
