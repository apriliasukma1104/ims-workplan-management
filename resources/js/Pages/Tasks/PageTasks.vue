<template>
    <layout title="Tasks List">
    <Toast position="top-center" />

      <!-- <Dialog header="Header" v-model:visible="dataTask.display" >
          <template #header>
                  <label>New Progress</label>
                  </template>
                  <div class="p-fluid">
                      <div class="p-field">
                          <label for="task">Task<span style="color:red;">*</span></label>
                          <InputText v-model="form.id" type="text" hidden/>
                          <InputText v-model="form.id_project" type="text" hidden/>
                          <InputText v-model="form.task" type="text" />
                      </div>
                      <div class="p-field">
                          <label for="description">Description<span style="color:red;">*</span></label>
                          <InputText v-model="form.description" type="text" />
                      </div>
                      <div class="p-field">
                          <label for="status">Status<span style="color:red;">*</span></label>
                            <select class="custom-select custom-select-sm" v-model="form.status">
                              <option disabled value="">Please Select One</option>
                              <option>to do</option>
                              <option>doing</option>
                              <option>done</option>
                            </select>
                      </div>
                  </div>
                  <template #footer>
                      <Button @click="simpanTask" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                  </template>
        </Dialog> -->

        <div class="card">
          <Toolbar class="p-mb-4">
            <template #left>
              <Button label="Add Productivity" icon="pi pi-plus" class="p-button-primary p-button-sm" @click="addProductivity" />
            </template>
            <template #right>
              <span>
                <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
                <Button icon="pi pi-search" iconPos="right" class="p-button-sm" @click="onSearch" />
              </span>
            </template>
          </Toolbar>

          <DataTable :value="tasks" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
            :totalRecords="totalData" :loading="loading"  @page="onPage($event)" responsiveLayout="scroll">
            <Column field="" header="No">
              <template #body="slotProps">
                {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
              </template>
            </Column>
            <Column field="name" header="Project Name"></Column>
            <Column field="" header="Tasks">
                <template #body="slotProps">
                  {{ slotProps.data.task }}
                <br>
                <small>Description: {{ slotProps.data.description }}</small>
                </template>
            </Column>
            <Column field="start_date" header="Project Started"></Column>
            <Column field="end_date" header="Project End Date"></Column>
            <Column field="project_status" header="Project Status">
                <template #body="slotProps">
                    <span :class="getStatusBadgeClass(slotProps.data.project_status)">
                            {{ slotProps.data.project_status }}
                    </span>
                </template>
            </Column>
            <Column field="status" header="Task Status">
                <template #body="slotProps">
                    <span :class="getStatusBadgeClass(slotProps.data.status)">
                            {{ slotProps.data.status }}
                    </span>
                </template>
            </Column>
            <template #empty>
                No records found
            </template>
          </DataTable>

        </div>
    </layout>
</template>

<script>
import Layout from "../../Partials/Layout";
import ErrorsAndMessages from "../../Partials/ErrorsAndMessages";
import { usePage } from "@inertiajs/inertia-vue3";
import { reactive} from "vue";
import { Inertia } from "@inertiajs/inertia";
import { computed, inject } from "vue";
import { pageListTasks } from '../../Api/projects.api.js'

export default {
  name: "PageTasks",
  components: {
    ErrorsAndMessages,
    Layout,
  },

  // setup(){
  //   const { userType } = usePage().props.value;
  //   const tasks = usePage().props.value.tasks;

  //   const form = reactive({
  //           id: "",
  //           task: "",
  //           description: "",
  //           status: "",
  //       });

  //       var dataTask = reactive({
  //           task : [],
  //           display: false
  //       });

  //       dataTask.task=tasks;

  //       const newTask = () => {
  //           dataTask.display = true;
  //           form.id = '';
  //           form.task = '';
  //           form.description = '';
  //           form.status = '';
  //       };

  //       async function loadLazyTask(form) {
  //           var params = {id_project:formData.id};
  //           const result = await listTasks( form );
  //           dataTask.task = result.data.data;
  //       };

  //       async function simpanTask() {
  //           form.id_project = formData.id;
  //           try {
  //               dataTask.display = false;  
  //               if (form.id) {
  //                   await updateTask(form); 
  //                   alert("Data Updated Successfully!");
  //               } else {
  //                   const response = await storeTasks(form);
  //                   alert("Data Saved Successfully!");
  //               }
  //               loadLazyTask(form);
  //           } catch (error) {
  //               console.error(error);
  //               alert("Data Failed to Save!");
  //           }
  //       };

  //       return {
  //           userType,
  //           form,
  //           tasks,
  //           dataTask,
  //           newTask,
  //           loadLazyTask,
  //           simpanTask
  //       };
  // },

  data() {
    return {
      tasks: [],
      dataPerPage: 10,
      totalData: 0,
      display: false,
      lazyParams: {
        page: 1,
      },
      loading: false,
    };
  },

  props: {
    errors: Object,
  },

  methods: {
    async loadLazyData() {
      this.loading = true;
      var response = await pageListTasks ({ page : this.lazyParams.page, search: this.search });
      this.tasks = response.data.data.data;
      this.totalData = response.data.data.total;
      this.loading = false;
    },
    onSearch() {
      this.totalData = 0;
      this.loadLazyData();
    },
    onPage(event) {
      this.lazyParams.page = event.page + 1;
      this.loadLazyData();
    }, 
    getStatusBadgeClass(status) {
        switch (status) {
            case "to do":
            return "badge badge-primary";
            case "doing":
            return "badge badge-info";
            case "review":
            return "badge badge-dark";
            case "done":
            return "badge badge-success";
            default:
            return "badge";
      }
    },
  },

  mounted() {
        this.tasks = this.$page.props.tasks.data; 
        this.totalData = this.$page.props.tasks.total;
  }

};
</script>