<template>
  <layout title="Tasks List">
    <Toast position="top-center" />
    <div class="card">
      <Toolbar class="p-mb-4">
        <template #left>
          <Button label="Add Productivity" icon="pi pi-plus" class="p-button-primary p-button-sm" @click="tambah" />
        </template>
        <template #right>
          <span>
            <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
            <Button icon="pi pi-search" iconPos="right" class="p-button-sm" @click="onSearch" />
          </span>
        </template>
      </Toolbar>
      <DataTable :value="tasksList.data" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
        :totalRecords="tasksList.total" :loading="loading" :currentPage="lazyParams.page" @page="onPage($event)"
        responsiveLayout="scroll">
        <Column field="" header="No">
          <template #body="slotProps">
            {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="project.name" header="Project Name"></Column>
        <Column field="task" header="Tasks"></Column>
        <Column field="project.start_date" header="Project Started"></Column>
        <Column field="project.end_date" header="Project End Date"></Column>
        <Column field="project.status" header="Project Status"></Column>
        <Column field="status" header="Task Status"></Column>
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
      tasksList: {},
      dataPerPage: 10,
      lazyParams: {
        page: 1,
      },
      loading: false,
      search: null,
    };
  },

  methods: {
    async onPage(event) {
      this.lazyParams.page = event.page;
      this.loadLazyData();
    },
    async loadLazyData() {
      this.loading = true;
      const response = await Inertia.get(route('tasks.page_tasks', lazyParams.value));
      this.tasksList = response.tasksList;
      this.loading = false;
    },
    async onSearch() {
      this.lazyParams.page = 1;
      this.loadLazyData();
    },
  },

  async mounted() {
    await this.loadLazyData();
  },
};
</script>

<style scoped>
</style>
