<template>
    <layout title="Tasks List">
    <Toast position="top-center" />

        <Dialog header="Header" v-model:visible="display" :style="{ width: '40vw' }">
            <template #header>
                <label>New Progress</label>
            </template>
                <div class="p-fluid">
                    <div class="p-field">
                        <InputText v-model="form.id" type="text" hidden/>
                        <label for="subject">Subject<span style="color:red;">*</span></label>
                        <InputText v-model="form.subject" type="text" />
                    </div>
                    <div class="p-field">
                        <label for="date">Date<span style="color:red;">*</span></label>
                        <InputText v-model="form.date" type="date" />
                    </div>
                    <div class="p-field">
                        <label for="start_time">Start Time<span style="color:red;">*</span></label>
                        <InputText v-model="form.start_time" type="time" />
                    </div>
                    <div class="p-field">
                        <label for="end_time">End Time<span style="color:red;">*</span></label>
                        <InputText v-model="form.end_time" type="time" />
                    </div>
                    <div class="p-field">
                        <label for="comment">Comment/Progress Description<span style="color:red;">*</span></label>
                        <div>
                            <TextArea v-model="form.comment" class="form-control" />
                        </div>
                    </div>
                </div>
            <template #footer>
                <Button @click="simpanProductivity" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
            </template>
        </Dialog>

        <div class="card">
          <Toolbar class="p-mb-4">
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
            <Column :exportable="false" header="Action">
                <template #body="slotProps">
                    <Button @click="addProductivity(slotProps.data)" icon="pi pi-plus" class="p-button-rounded p-button-primary" />
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
import { pageListTasks } from '../../Api/projects.api.js'

export default {
  name: "PageTasks",
  components: {
    ErrorsAndMessages,
    Layout
  },

  data() {
    return {
      tasks: [],
      projects: [],
      dataPerPage: 10,
      totalData: 0,
      display: false,
      form:{
        id:null,
        subject:null,
        date:null,
        start_time:null,
        end_time:null,
        comment:null,
      },
      lazyParams: {
        page: 1,
      },
      loading: false,
      sortOrder: null,
    };
  },

  props: {
    errors: Object,
  },

  methods: {
    async loadLazyData() {
      this.loading = true;
      var response = await pageListTasks ({ page : this.lazyParams.page, search: this.search});
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
    addProductivity(){
      this.display = true;
      this.form ='';
    },
    // addProductivity(data) {
    //   this.form.id = data.id;
    //   this.form.subject = '';
    //   this.form.date = '';
    //   this.form.start_time = '';
    //   this.form.end_time = '';
    //   this.form.comment = '';
    //   this.display = true;
    // },
    async simpanProductivity() {
        try {
            this.display = false;

            if (this.form.id) {
                await updateValidation(this.form); 
                alert("Data Saved Successfully!");
            } else {
                alert("Please fill in all required fields.");
            }
                
            this.loadLazyData();
        } catch (error) {
            alert("Data Failed to Save!");
        }
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