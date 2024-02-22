<template>
  <layout title="Add New Member">
  <ConfirmDialog></ConfirmDialog>
  <Toast position="top-center" />

    <!-- <form enctype="multipart/formData"> -->
    <form>
      <div class="callout callout">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mt-3 ml-3">
              <label for="name" class="control-label">Name</label>
              <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
              <input type="text" name="name" class="form-control form-control-sm" required v-model="formData.name">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="nip" class="control-label">NIP</label>
              <input type="text" name="nip" class="form-control form-control-sm" required v-model="formData.nip">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="position" class="control-label">Position</label>
              <input type="text" name="position" class="form-control form-control-sm" required v-model="formData.position">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="sub_department" class="control-label">Unit</label>
              <select class="custom-select custom-select-sm" required v-model="formData.sub_department">
                <option disabled value="">Please Select One</option>
                <option>Infrastructure & Support</option>
                <option>ERP & EIM</option>
                <option>Apps Development & Governance</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3 mr-3">
              <label for="role" class="control-label">Role</label>
              <select class="custom-select custom-select-sm" required v-model="formData.role">
                <option disabled value="">Please Select One</option>
                <option>Kadep</option>
                <option>Kabag</option>
                <option>Staf</option>
                <option>Admin</option>
              </select>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="email" class="control-label">Email</label>
              <input type="email" name="email" class="form-control form-control-sm" required v-model="formData.email">
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="password" class="control-label">Password</label>
              <input type="password" name="password" class="form-control form-control-sm" required v-model="formData.password">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-12 text-right justify-content-center d-flex mb-3">
            <button @click="submitForm" class="btn btn-dark ml-2">Save</button>
            <button @click="cancelForm" class="btn btn-secondary ml-2">Cancel</button>
          </div>
        </div>
      </div>
    </form>
  </layout>
</template>

<script>
import Layout from "../../Partials/Layout";
import { usePage } from "@inertiajs/inertia-vue3";
import { reactive } from "vue";
import axios from 'axios'

export default {
  components: {
    Layout,
  },
  setup() {
    const { userType } = usePage().props;

    const formData = reactive({
      id: "",
      name: "",
      nip: "",
      position: "",
      sub_department: "",
      role: "",
      email: "",
      password: ""
    });
    
    async function submitForm() {
      try {
        const response = await axios.post('/members/add/store_members', formData);
        if (response.status >= 200 && response.status < 300) {
          alert("Data Saved Successfully!");
          window.location.href = "/members/list_members";
        } else {
          alert("Data Failed to Save!");
        }
      } catch (error) {
        console.error(error);
        alert("Data Failed to Save!");
      }
    }

    const cancelForm = () => {
      window.location.href = "/members/list_members";
    };
    
    return {
      userType,
      formData,
      submitForm,
      cancelForm,
    };
  },
};
</script>

<style scoped>
.custom-select {
  font-size: 14px;
}

</style>
