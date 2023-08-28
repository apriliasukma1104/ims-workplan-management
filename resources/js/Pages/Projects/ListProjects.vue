<template>
    <layout title="Project List">
    <Toast position="top-center" />
        <div class="card">
            <Toolbar class="p-mb-4">
                <template #right>
                    <span>
                        <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
                        <Button icon="pi pi-search" iconPos="right"  class="p-button-sm"  @click="onSearch"/>
                    </span>
                </template>
            </Toolbar>

            <DataTable :value="projects" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :loading="loading"  @page="onPage($event)" responsiveLayout="scroll">
                <Column field="" header="No">
                    <template #body="slotProps">
                        {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="name" header="Project Name"></Column>
                <Column field="project_type" header="Project Type"></Column>
                <Column field="team_leader" header="Team Leader">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.team_leader.name }}</span>
                    </template>
                </Column>
                <Column field="start_date" header="Start Date"></Column>
                <Column field="end_date" header="End Date"></Column>
                <Column field="status" header="Status">
                    <template #body="slotProps">
                        <span :class="getStatusBadgeClass(slotProps.data.status)">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
                <Column :exportable="false" header="Action">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 10px;" />
                        <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-info" style="margin-right: 10px;" />
                        <Button @click="onView(slotProps.data)" icon="pi pi-eye" class="p-button-rounded p-button-success" />
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
import { Inertia } from "@inertiajs/inertia";
import { computed, inject } from "vue";
import { pageListProjects, deleteProject } from '../../Api/projects.api.js';

export default {
    name: "ListProjects",
    components: {
        ErrorsAndMessages,
        Layout,
    },

    data() {
        return {
            projects: [],
            dataPerPage: 5, 
            totalData: 0, 
            display: false,
            error: {},
            lazyParams: {
                page: 1
            },
            loading: false
        };
    },

    props: {
        errors: Object
    },

    methods: {
        async loadLazyData() {
            this.loading = true;
            var response = await pageListProjects ({ page : this.lazyParams.page, search: this.search });
            this.projects = response.data.data.data;
            this.totalData = response.data.data.total;
            this.loading = false;
        },
        onSearch(){
            this.totalData = 0;
            this.loadLazyData();
        },
        onPage(event) {
            this.lazyParams.page = event.page + 1;
            this.loadLazyData();
        },
        onEdit(data) {
        this.$inertia.visit(`/projects/list/edit_project?id=${data.id}`);
        },
        onView(data) {
        this.$inertia.visit(`/projects/list/view_project?id=${data.id}`);
        },
        onDelete(data) {
            if (window.confirm("Are you sure you want to delete data?")) {
                deleteProject({ id: data.id })
                    .then(() => {
                        this.$toast.add({
                            severity: "success",
                            summary: "Information!",
                            detail: "Data Deleted Successfully!",
                            life: 3000,
                        });
                        this.loadLazyData();
                    })
                    .catch((error) => {
                        console.error("Error while deleting:", error);
                        this.$toast.add({
                            severity: "error",
                            summary: "Error!",
                            detail: "Data Failed to Delete!",
                            life: 3000,
                        });
                    });
            }
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
        this.projects = this.$page.props.projects.data; 
        this.totalData = this.$page.props.projects.total;
    }
};
</script>