<template>
    <layout title="View Project">
    <Toast position="top-center" />

      <Dialog header="Header" v-model:visible="display" >
        <template #header>
                <label>New Task</label>
                </template>
                <div class="p-fluid">
                    <div class="p-field">
                        <label for="task">Task<span style="color:red;">*</span></label>
                        <InputText v-model="form.id" type="text" hidden/>
                        <InputText v-model="form.id_project" type="text" hidden/>
                        <InputText v-model="form.task" type="text" :class="error && error.task ? 'p-invalid' : '' "/>
                    </div>
                     <div class="p-field">
                        <label for="description">Description<span style="color:red;">*</span></label>
                        <InputText v-model="form.description" type="text" :class="error && error.description ? 'p-invalid' : ''" />

                    </div>
                    <div class="p-field">
                        <label for="status">Status<span style="color:red;">*</span></label>
                          <select class="custom-select custom-select-sm" v-model="form.status" :class="error && error.status ? 'p-invalid' : '' ">
                            <option disabled value="">Please Select One</option>
                            <option>to do</option>
                            <option>doing</option>
                            <option>done</option>
                          </select>
                    </div>
                </div>
                <template #footer>
                    <Button label="Batal" icon="pi pi-times" class="p-button-text" @click="display = false"/>
                    <Button label="Simpan" icon="pi pi-check" autofocus @click="simpanTask"/>
        </template>
      </Dialog>
    
      <form>
        <div class="callout callout">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mt-3 ml-3">
                    <label class="control-label">Project Name</label>
                    <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
                    <div>{{ formData.name }}</div>
                </div>
                <div class="form-group mt-3 ml-3">
                    <label class="control-label">Description</label>
                    <div>{{ formData.description }}</div>
                </div>
                <div class="form-group mt-3 ml-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">Project Type</label>
                    <div>{{ formData.project_type }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- <div class="form-group mt-3 ml-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">Team Members</label>
                    <div>
                    <span v-for="member in members" :key="member.id" v-if="formData.team_members.includes(member.id)">{{ member.name }}</span>
                    </div>
                </div> -->
                <div class="form-group mt-3 mr-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">Team Leader</label>
                    <div>{{ formData.team_leader }}</div>
                </div>
                <div class="form-group mt-3 mr-3">
                    <label class="control-label">Start Date</label>
                    <div>{{ formData.start_date }}</div>
                </div>
                <div class="form-group mt-3 mr-3">
                    <label class="control-label">End Date</label>
                    <div>{{ formData.end_date }}</div>
                </div>
                <div class="form-group mt-3 mr-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">Status</label>
                    <span class="badge badge-primary" v-if="formData.status === 'to do'">to do</span>
                    <span class="badge badge-info" v-if="formData.status === 'doing'">doing</span>
                    <span class="badge badge-secondary" v-if="formData.status === 'done'">done</span>
                </div>
            </div>
        </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <span><b>Team Member/s:</b></span>
                    </div>
                    <div class="card-body p-0">
                        <div>{{ formData.team_members }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <span><b>Task List:</b></span>
                        <div class="card-tools">
                            <Button label="New Task" icon="pi pi-plus" class="p-button-secondary p-button-sm"  @click="newTask"/>
                        </div>
                    </div>
                    <div class="card-body p-0 ml-2 mr-2">
                        <DataTable :value="data" :lazy="true" :rows="totalData" ref="dt" :loading="loading" responsiveLayout="scroll">
                            <Column field="" header="No">
                              <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                              </template>
                            </Column>
                            <Column field="task" header="Task"></Column>
                            <Column field="description" header="Description"></Column>
                            <Column field="status" header="Status"></Column>
                            <Column :exportable="false" header="Action">
                                <template #body="slotProps">
                                    <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 10px;" />
                                    <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-info" style="margin-right: 10px;" />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
      </form>
    </layout>
  </template>
  
  <script>
  import Layout from "../../Partials/Layout";
  import { usePage } from "@inertiajs/inertia-vue3";
  import { reactive, onMounted } from "vue";
  import { storeTasks } from '../../Api/projects.api.js';
  
  export default {
    components: {
      Layout,
    },
    setup() {
      const { userType, members } = usePage().props.value;
      const data = usePage().props.value.formData;

      const formData = reactive({
        id: data.id,
        name: data.name,
        project_type: data.project_type,
        team_leader: data.team_leader,
        team_members: data.team_members,
        start_date: data.start_date,
        end_date: data.end_date,
        status: data.status,
        description: data.description,
      });

      const form = reactive({
        id: "",
        id_project: "",
        task: "",
        description: "",
        status: "",
      });

      const tasks = reactive([]);

      async function simpanTask() {
        console.log(form); 
        form.id_project = formData.id;
        try {
          const response = await storeTasks(form);
          if (response.data.message === 'Data created successfully!') {
            alert("Data Saved Successfully!");
            window.location.href = "/projects/view_projects?id=" + formData.id;
          } else {
            alert("Data Failed to Save!");
          }
        } catch (error) {
          console.error(error);
          alert("Data Failed to Save!");
        }
      }

      async function mengambilTasks() {
          try {
            const response = await storeTasks(); 
            tasks.value = response.data.tasks; 
          } catch (error) {
            console.error(error);
          }
        }

      onMounted(mengambilTasks);

      return {
        userType,
        formData,
        form,
        tasks,
        members,
        simpanTask,
        mengambilTasks
      };
    },

    data(){
      return{
        data : [],
        display:false,
      }
    },

    methods:{
      newTask() {
      this.display = true;
      this.form.id = "";
      this.form.task = "";
      this.form.description = "";
      this.form.status = "";
    },
  }
  };
  </script>
  
  <style scoped>
  .custom-select {
    font-size: 14px;
  }
  </style>
  