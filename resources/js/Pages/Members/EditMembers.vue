<template>
  <layout title="Edit Member">
    <form @submit.prevent="update">
      <div class="card">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group mt-3 ml-3 mr-3">
              <label for="name" class="control-label">Name</label>
              <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
              <input type="text" name="name" class="form-control form-control-sm" v-model="formData.name">
            </div>
            <div class="form-group mt-3 ml-3 mr-3">
              <label for="role" class="control-label" style="display: block; margin-top: 1rem;">Member Role</label>
              <select class="custom-select custom-select-sm" v-model="formData.role">
                <option disabled value="">Please Select One</option>
                <option>Super Admin</option>
                <option>Admin 1</option>
                <option>Admin 2</option>
                <option>User</option>
              </select>
            </div>
            <div class="form-group mt-3 ml-3 mr-3">
              <label for="position" class="control-label">Position</label>
              <input type="text" name="position" class="form-control form-control-sm" v-model="formData.position">
            </div>
            <div class="form-group mt-3 ml-3 mr-3">
              <label class="control-label">Email</label>
              <input type="email" class="form-control form-control-sm" name="email" v-model="formData.email">
            </div>
            <div class="form-group mt-3 ml-3 mr-3">
              <label class="control-label">Password</label>
              <input type="password" class="form-control form-control-sm" name="password" v-model="formData.password">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-12 text-right justify-content-center d-flex mb-3">
            <button @click="update" class="btn btn-dark ml-2" >Update</button>
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
import { updateMembers } from '../../Api/members.api.js';

export default {
  components: {
    Layout,
  },
  setup() {
    const { userType } = usePage().props;
    const data = usePage().props.value.formData;

    const formData = reactive({
      id: data.id,
      name: data.name,
      role: data.role,
      position: data.position,
      email: data.email,
      password: data.password,
    });

    const update = async (e) => {
      e.preventDefault();
      try {
        await updateMembers(formData); // Menggunakan updateMembers dari API untuk menyimpan data ke server
        window.location.href = "/members/list_members";
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors;
          for (const key in errors) {
            if (Object.hasOwnProperty.call(errors, key)) {
              formData[key + "_error"] = errors[key][0];
              alert(errors[key][0]);
            }
          }
        } else {
          console.error(error);
        }
      }
    };

    const cancelForm = () => {
      window.location.href = "/members/list_members";
    };

    return {
      userType,
      formData,
      update,
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
