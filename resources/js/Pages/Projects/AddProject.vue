<template>
  <layout title="Add New Project">
  <ConfirmDialog></ConfirmDialog>
  <Toast position="top-center" />

    <form>
      <div class="callout callout">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mt-3 ml-3">
              <label for="name" class="control-label">Project Name</label>
              <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
              <input type="text" name="name" class="form-control form-control-sm" required v-model="formData.name">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="project_type" class="control-label" style="display: block; margin-top: 1rem;">Project Type</label>
              <select class="custom-select custom-select-sm" required v-model="formData.project_type">
                <option disabled value="">Please Select One</option>
                <option>RKAP</option>
                <option>KPI</option>
                <option>NPM</option>
              </select>
            </div>
            <div class="form-group mt-3 ml-3">
                <label for="team_leader" class="control-label" style="display: block; margin-top: 1rem;">Team Leader</label>
                <select name="team_leader" id="team_leader" class="custom-select custom-select-sm" required v-model="formData.team_leader" @change="handleTeamLeaderChange">
                  <option disabled value="">Please Select One</option>
                  <option v-for="member in members.filter(member => user && (member.role === 'Kadep' || member.role === 'User'))" :key="member.id" :value="member.id">{{ member.name }}</option>
                </select>
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="team_members" class="control-label" style="display: block; margin-top: 1rem;">Team Members</label>
              <select name="team_members[]" id="team_members" class="custom-select custom-select-sm" multiple required v-model="formData.team_members">
                <option v-for="member in members.filter(member => user && (member.role === 'Kadep' || member.role === 'User'))" :key="member.id" :value="member.id">{{ member.name }}</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3 mr-3">
              <label for="start_date" class="control-label">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" required v-model="formData.start_date">
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="end_date" class="control-label">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" required v-model="formData.end_date">
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="status" class="control-label" style="display: block; margin-top: 1rem;">Status</label>
              <select class="custom-select custom-select-sm" required v-model="formData.status">
                <option disabled value="">Please Select One</option>
                <option>to do</option>
                <option>doing</option>
                <option>review</option>
                <option>done</option>
              </select>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="description" class="control-label">Description</label>
              <textarea name="description" class="form-control" required v-model="formData.description"></textarea>
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
import { reactive } from 'vue';
import { usePage } from "@inertiajs/inertia-vue3";
import { computed} from "vue";

export default {
  components: {
    Layout, 
  },
  setup() {
    const { userType, members } = usePage().props.value;
    const user = computed(() => usePage().props.value.auth.user); 

    const formData = reactive({
      id: "",
      name: "",
      project_type: "",
      team_leader: "",
      team_members: [],
      start_date: "",
      end_date: "",
      status: "",
      description: "",
    });

    function submitForm() {
      axios.post('/projects/add/store_projects', formData)
        .then(response => {
          if (response.status >= 200 && response.status < 300) {
            alert("Data Saved Successfully!");
            window.location.href = "/projects/list_projects";
          } else {
            alert("Data Failed to Save!");
          }
        })
        .catch(error => {
          console.error(error);
          alert("Data Failed to Save!");
        });
    }

    const cancelForm = () => {
      window.location.href = "/projects/list_projects";
    };

    return {
      user,
      userType,
      formData,
      members,
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