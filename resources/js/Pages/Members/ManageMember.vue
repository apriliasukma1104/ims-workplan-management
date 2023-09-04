<template>
    <layout title="Manage Member Account">
    <ConfirmDialog></ConfirmDialog>
    <Toast position="top-center" />
  
      <form @submit.prevent="manage">
        <div class="callout callout">
          <div class="row">
            <div class="col">
              <div class="form-group mt-3 ml-3 mr-3">
                  <label class="control-label">Password</label>
                  <input type="text" class="form-control form-control-sm" name="password" v-model="formData.password">
                  <small><i>{{ "Leave this blank if you don't want to change your password" }}</i></small>
              </div>
              <div class="form-group mt-3 ml-3 mr-3">
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
            </div>
            <hr>
            <div class="row">
              <div class="col-md-12 text-right justify-content-center d-flex mb-3">
                <button @click="manage" class="btn btn-dark ml-2" >Save</button>
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
  import { manageMember } from '../../Api/members.api.js';
  
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
        email: data.email,
        password: data.password,
        image: data.image,
      });
      
      const avatar = reactive({
        value: null,
      });

      const manage = async (e) => {
        e.preventDefault();
        try {
          await manageMember(formData);
          alert("Data Saved Successfully!");
          window.location.href = "/members/list_members";
        } catch (error) {
          console.error(error);
          alert("Data failed to save");
        }
      };
  
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
        manage,
        cancelForm,
        displayImg,
      };
    },
  };
  </script>
  
  <style scoped>
  
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
  