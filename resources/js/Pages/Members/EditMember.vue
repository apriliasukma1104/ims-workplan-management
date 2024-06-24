<template>
  <layout title="Edit Member">
  <Toast position="top-center" />

    <form @submit.prevent="update">
      <div class="callout callout">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mt-3 ml-3">
              <label for="name" class="control-label">Name</label>
              <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
              <input type="text" name="name" class="form-control form-control-sm" required v-model="formData.name" >
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="nip" class="control-label">NIP</label>
              <input type="text" name="nip" class="form-control form-control-sm" required v-model="formData.nip">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="position" class="control-label">Position</label>
              <select class="custom-select custom-select-sm" required v-model="formData.position">
                <option disabled value="">Please Select One</option>
                <option>Kepala Departemen</option>
                <option>Kepala Bagian</option>
                <option>Staf</option>
                <option>IT Help Desk</option>
              </select>
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
              <label class="control-label">Password</label>
              <input type="password" name="password" class="form-control form-control-sm" v-model="formData.password">
              <small><i>{{ "Leave this blank if you don't want to change your password" }}</i></small>
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
import { useToast } from 'primevue/usetoast';
import { updateMember } from '../../Api/members.api.js';

export default {
  components: {
    Layout,
  },
  setup() {
    const { userType } = usePage().props;
    const data = usePage().props.value.formData;
    const toast = useToast();
   
    const formData = reactive({
      id: data.id,
      name: data.name,
      nip: data.nip,
      position: data.position,
      sub_department: data.sub_department,
      role: data.role,
      email: data.email,
      password: data.password,
    });

    const update = async (e) => {
      e.preventDefault();
      try {
        await updateMember(formData);
        toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Successfully Updated!', life: 3000 });
        window.location.href = "/members/list_members";
      } catch (error) {
        toast.add({ severity: 'error', summary: 'Error!', detail: 'Data Failed to Save!', life: 3000 });
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