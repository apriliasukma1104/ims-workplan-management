<template>
    <layout title="Tasks List">
    <Toast position="top-center" />
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
            :totalRecords="totalData" :loading="loading" :currentPage="lazyParams.page" @page="onPage($event)" responsiveLayout="scroll">
            <Column field="" header="No">
              <template #body="slotProps">
                {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
              </template>
            </Column>
            <Column field="name" header="Project Name"></Column>
            <Column field="task" header="Tasks"></Column>
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
          </DataTable>

        </div>
    </layout>
</template>

<script>
import Layout from "../../Partials/Layout";
import ErrorsAndMessages from "../../Partials/ErrorsAndMessages";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import { computed, inject } from "vue";

export default {
  name: "PageTasks",
  components: {
    ErrorsAndMessages,
    Layout,
  },

  data() {
    return {
      tasks: [],
      dataPerPage: 10,
      totalData: 0, 
      display: false,
      search:null,
      lazyParams: {
        page: 1,
      },
      loading: false,
    };
  },

  props: {
    errors: Object
  },

  methods: {
    async loadLazyData() {
      window.location.reload();
      // this.loading = true;
      // const response = await Inertia.get(route('tasks.page_tasks', lazyParams));
      // this.tasksList = response.tasksList;
      // console.log(tasksList);
      // this.loading = false;
    },
    onSearch() {
      this.lazyParams.page = 1;
      this.loadLazyData();
    },
    onPage(event) {
      this.lazyParams.page = event.page  + 1;
      this.loadLazyData();
    }, 
    getStatusBadgeClass(status) {
        switch (status) {
            case "to do":
            return "badge badge-primary";
            case "doing":
            return "badge badge-info";
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