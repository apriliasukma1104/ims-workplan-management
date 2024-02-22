<template>
  <layout title="Add New Work Plan">
  <ConfirmDialog></ConfirmDialog>
  <Toast position="top-center" />

    <form>
      <div class="callout callout">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mt-3 ml-3">
              <label for="name" class="control-label">Work Plan</label>
              <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
              <input type="text" name="name" class="form-control form-control-sm" required v-model="formData.name">
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="description" class="control-label">Description</label>
              <textarea name="description" class="form-control" style="height: 85px;" required v-model="formData.description"></textarea>
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="project_type" class="control-label">Type</label>
              <select class="custom-select custom-select-sm" required v-model="formData.project_type">
                <option disabled value="">Please Select One</option>
                <option>RKAP</option>
                <option>KPI</option>
                <option>NPM</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3 mr-3">
                <label for="team_leader" class="control-label">PIC</label>
                <select name="team_leader" id="team_leader" class="custom-select custom-select-sm" required v-model="formData.team_leader">
                    <option disabled value="">Please Select One</option>
                    <template v-if="user.role === 'Kabag' && user.sub_department === 'Apps Development & Governance'">
                        <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'Apps Development & Governance' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                    </template>
                    <template v-else-if="user.role === 'Kabag' && user.sub_department === 'Infrastructure & Support'">
                        <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'Infrastructure & Support' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                    </template>
                    <template v-else-if="user.role === 'Kabag' && user.sub_department === 'ERP & EIM'">
                        <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'ERP & EIM' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                    </template>
                </select>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="team_members" class="control-label">Team Member/s</label>
              <select name="team_members[]" id="team_members" class="custom-select custom-select-sm" multiple required v-model="formData.team_members">
                <template v-if="user.role === 'Kabag' && user.sub_department === 'Apps Development & Governance'">
                    <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'Apps Development & Governance' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                </template>
                <template v-else-if="user.role === 'Kabag' && user.sub_department === 'Infrastructure & Support'">
                    <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'Infrastructure & Support' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                </template>
                <template v-else-if="user.role === 'Kabag' && user.sub_department === 'ERP & EIM'">
                    <option v-for="member in members.filter(member => user && (member.role === 'Kabag' || member.role === 'Staf') && member.sub_department === 'ERP & EIM' && member.members_status === '1')" :key="member.id" :value="member.id">{{ member.name }}</option>
                </template>
              </select>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="year" class="control-label">Year</label>
              <input type="number" name="year" id="year" class="form-control form-control-sm" required v-model="formData.year" min="2024" max="9999">
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
import MultiSelect from 'primevue/multiselect';


export default {
  components: {
    Layout, 
    MultiSelect
  },
  setup() {
    const { userType, members } = usePage().props.value;
    const user = computed(() => usePage().props.value.auth.user); 

    const formData = reactive({
      id: "",
      name: "",
      description: "",
      project_type: "",
      team_leader: "",
      team_members: [],
      year: ""
    });

    function submitForm() {
      axios.post('/workplans/add/store_workplans', formData)
        .then(response => {
          if (response.status >= 200 && response.status < 300) {
            alert("Data Saved Successfully!");
            window.location.href = "/workplans/list_workplans";
          } else {
            alert("Data Failed to Save!");
          }
        })
        .catch(error => {
          console.error(error);
          alert("Data Failed to Save!");
        })
    };

    const cancelForm = () => {
      window.location.href = "/workplans/list_workplans";
    };

    return {
      user,
      userType,
      formData,
      members,
      submitForm,
      cancelForm
    };
  },
};
</script>

<style scoped>
.custom-select {
  font-size: 14px;
}
</style>