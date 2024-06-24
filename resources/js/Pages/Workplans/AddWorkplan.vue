<template>
  <layout title="Add New Work Plan">
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
              <textarea name="description" class="form-control" style="height: 118px; font-size: 14px;" required v-model="formData.description"></textarea>
            </div>
            <div class="form-group mt-3 ml-3">
              <label for="project_type" class="control-label">Type</label>
              <select class="custom-select custom-select-sm" required v-model="formData.project_type">
                <option disabled value="">Please Select One</option>
                <option>Program Kerja</option>
                <option>Kegiatan Rutin</option>
                <option>Investasi</option> 
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3 mr-3">
              <label for="team_leader" class="control-label">PIC</label>
              <select name="team_leader" id="team_leader" class="custom-select custom-select-sm" required v-model="formData.team_leader">
                <option disabled value="">Please Select One</option>
                <option v-for="member in pic" :key="member.id" :value="member.id">{{ member.name }}</option>
              </select>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="team_members" class="control-label" style="display: block; margin-top: 1rem;">Team Member/s </label>
              <MultiSelect name="team_members[]" id="team_members" v-model="formData.team_members" display="chip"
                :options="members" filter optionLabel="name" placeholder="Select Team Members" class="w-100" style="font-size: 14px;"/>
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="year" class="control-label">Year</label>
              <input type="number" name="year" id="year" class="form-control form-control-sm" required v-model="formData.year">
            </div>
            <div class="form-group mt-3 mr-3">
              <label for="budget" class="control-label">Budget</label>
              <div class="input-group">
                  <div class="input-group-prepend">
                      <span class="input-group-text" style="padding: 4px 8px; font-size: 14px;">Rp</span>
                  </div>
                  <input type="decimal" name="budget" id="budget" class="form-control form-control-sm" required v-model="formData.budget" placeholder="1000000.00">
              </div>
            </div>
            <div class="form-group mt-3 mr-3">
              <input type="text" name="status" class="form-control form-control-sm" required v-model="formData.status" hidden>
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
import { useToast } from 'primevue/usetoast';

export default {
  components: {
    Layout, 
    MultiSelect
  },
  setup() {
    const { userType, pic, members } = usePage().props.value;
    const user = computed(() => usePage().props.value.auth.user); 
    const toast = useToast();

    const formData = reactive({
      id: "",
      name: "",
      description: "",
      project_type: "",
      team_leader: "",
      team_members: "",
      year: "",
      budget: "",
      status: "Created"
    });

    function submitForm() {
      axios.post('/workplans/add/store_workplans', formData)
        .then(response => {
          if (response.status >= 200 && response.status < 300) {
            toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Saved Successfully!', life: 3000 });
            window.location.href = "/workplans/list_workplans";
          } else {
            toast.add({ severity: 'error', summary: 'Error!', detail: 'Data Failed to Save!', life: 3000 });
          }
        })
        .catch(error => {
          toast.add({ severity: 'error', summary: 'Error!', detail: 'Data Failed to Save!', life: 3000 });
        })
    };

    const cancelForm = () => {
      window.location.href = "/workplans/list_workplans";
    };

    return {
      userType,
      user,
      formData,
      members,
      pic,
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