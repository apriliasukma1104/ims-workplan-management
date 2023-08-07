<template>
  <layout title="Add New Member">
  <ConfirmDialog></ConfirmDialog>
  <Toast position="top-center" />

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
              <label for="role" class="control-label" style="display: block; margin-top: 1rem;">Member Role</label>
              <select class="custom-select custom-select-sm" required v-model="formData.role">
                <option disabled value="">Please Select One</option>
                <option>Super Admin</option>
                <option>Admin 1</option>
                <option>Admin 2</option>
                <option>User</option>
              </select>
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="customFile" class="control-label">Image</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="image" ref="customFile" @change="displayImg($event)">
                <label class="custom-file-label" for="customFile">Choose file</label>
              </div>
            </div>
            <div class="form-group d-flex justify-content-center align-items-center">
              <div class="avatar-circle">
                <img :src="avatar.value" alt="Avatar" id="cimg" class="img-fluid img-thumbnail">
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3 mr-3">
              <label for="position" class="control-label">Position</label>
              <input type="text" name="position" class="form-control form-control-sm" required v-model="formData.position">
            </div>
            <div class="form-group mt-3 mr-3">
              <label class="control-label">Email</label>
              <input type="email" class="form-control form-control-sm" name="email" required v-model="formData.email">
            </div>
            <div class="form-group mt-3 mr-3">
              <label class="control-label">Password</label>
              <input type="password" class="form-control form-control-sm" name="password" required v-model="formData.password">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-12 text-right justify-content-center d-flex mb-3">
            <button @click="submitForm" class="btn btn-dark ml-2" >Save</button>
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
import { storeMembers } from '../../Api/members.api.js';
import { router } from '@inertiajs/vue3';
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
      role: "",
      image: null,
      position: "",
      email: "",
      password: "",

    });
    
    const avatar = reactive({
      value: null,
    });

    // function submitForm () {
    //   var bodyFormData = new FormData();
    //   bodyFormData.append('image', formData.image);
    //   router.post('/members/add/store_members', formData, {
    //   forceFormData: true,
    // })
    // }

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

    const displayImg = (event) => {
      const file = event.target.files[0];
      formData.image=file
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          avatar.value = reader.result;
        };
        reader.readAsDataURL(file);
      } else {
        avatar.value = null;
      }
    };

    return {
      userType,
      formData,
      avatar,
      submitForm,
      cancelForm,
      displayImg,
    };
  },
};
</script>

<style scoped>
.custom-select {
  font-size: 14px;
}

.avatar-circle {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 1px solid #b7b4b1;
}

.avatar-circle img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
</style>
