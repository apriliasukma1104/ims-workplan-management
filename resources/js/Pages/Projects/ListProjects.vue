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
            <DataTable :value="data" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :loading="loading" :currentPage="lazyParams.page" @page="onPage($event)" responsiveLayout="scroll">
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
                        <Button @click="onView(slotProps.data)" icon="pi pi-eye" class="p-button-rounded p-button-secondary" />
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
import { storeProjects, deleteProject } from '../../Api/projects.api.js';

export default {
    name: "ListProjects",
    components: {
        ErrorsAndMessages,
        Layout,
    },

    data() {
        return {
            data: [],
            dataPerPage: 10, 
            totalData: 0, 
            display: false,
            search:null,
            form: {
                id: null,
                name: null, 
                project_type: null,
                team_leader: null,
                team_members: null,
                start_date: null,
                end_date: null,
                status: null,
                description: null
            },
            initform: {
                id: null,
                name: null,
                project_type: null,
                team_leader: null,
                team_members: null,
                start_date: null,
                end_date: null,
                status: null,
                description: null
            },
            error: {},
            lazyParams: {
                page: 1
            },
            loading: false
        };
    },

    methods: {
        async loadLazyData() {
            window.location.reload();
            // this.loading = true; 
            // try {
            //     const res = await storeProjects({ page: this.lazyParams.page, search: this.search });
            //     this.data = res.projects.data;
            //     this.totalData = res.projects.total;
            //     this.loading = false; 
            // } catch (error) {
            //     console.error("Error while fetching projects:", error);
            //     this.loading = false; 
            // }
        },
        onSearch(){
            this.lazyParams.page = 1;
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
        },
        getStatusBadgeClass(status) {
        switch (status) {
            case "to do":
            return "badge badge-primary";
            case "doing":
            return "badge badge-info";
            case "done":
            return "badge badge-secondary";
            default:
            return "badge";
        }
        },
    },

    props: {
        errors: Object
    },
    mounted() {
        this.data = this.$page.props.projects.data; 
        this.totalData = this.$page.props.projects.data.length; 
    }

};
</script>

<style scoped>
</style>