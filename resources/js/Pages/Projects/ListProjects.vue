<template>
    <layout title="Project List">
    <Toast position="top-center" />

            <Dialog header="Header" v-model:visible="display" :style="{ width: '30vw' }" >
            <template #header>
                    <label>Project Validation</label>
                    </template>
                    <div class="p-fluid">
                        <div class="p-field">
                            <label for="validation">Validation<span style="color:red;">*</span></label>
                            <InputText v-model="form.id" type="text" hidden/>
                            <select class="custom-select custom-select-sm" v-model="form.validation">
                                <option disabled>Please Select One</option>
                                <option>approved</option>
                                <option>not approved</option>
                            </select>
                        </div>
                        <div class="p-field">
                            <label for="note">Note<span style="color:red;">*</span></label>
                            <InputText v-model="form.note" type="text" />
                            <!-- <div>
                                <TextArea v-model="form.note" class="form-control" />
                            </div> -->
                        </div>
                    </div>
                    <template #footer>
                        <Button @click="simpanValidation" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                    </template>
        </Dialog>

        <div class="card">
            <Toolbar class="p-mb-4">
                <!-- <template #left>
                <select name="project_type" id="project_type" class="custom-select" required @change="handleTeamLeaderChange">
                    <option disabled value="">Please Select One</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.project_type }}</option>
                </select>
                </template> -->
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
                <!-- <Column field="team_members" header="Team Members">
                    <template #body="slotProps">
                        <div v-for="member in slotProps.data.team_members">
                            {{ member.name }}
                        </div>
                    </template>
                </Column> -->
                <Column field="start_date" header="Start Date"></Column>
                <Column field="end_date" header="End Date"></Column>
                <Column field="status" header="Status">
                    <template #body="slotProps">
                        <span :class="['status-badge', getStatusBadgeClass(slotProps.data.status)]">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
                <Column :exportable="false" header="Action" v-if="user && (user.role === 'Kadiv' || user.role === 'Kadep' || user.role === 'User')">
                    <template #body="slotProps">
                        <Button v-if="user && (user.role === 'Kadiv' || user.role === 'Kadep')" @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 5px;" />
                        <Button v-if="user && (user.role === 'Kadiv' || user.role === 'Kadep')" @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-info" style="margin-right: 5px;" />
                        <Button v-if="user && (user.role === 'Kadep' || user.role === 'User')" @click="onView(slotProps.data)" icon="pi pi-eye" class="p-button-rounded p-button-success" style="margin-right: 5px;" />
                        <!-- <Button v-if="user && (user.role === 'Kadep' || user.role === 'User') && (slotProps.data.team_leader.id === user.id || slotProps.data.team_members.some(member => member.id === user.id))" @click="onView(slotProps.data)" icon="pi pi-eye" class="p-button-rounded p-button-success" style="margin-right: 5px;" /> -->
                        <Button v-if="user && user.role === 'Kadiv' && slotProps.data.status === 'submission'" @click="onValidation(slotProps.data)" icon="pi pi-check" class="p-button-rounded p-button-secondary" />
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
import { computed } from "vue";
import { pageListProjects, deleteProject, validation } from '../../Api/projects.api.js';

export default {
    name: "ListProjects",
    components: {
        ErrorsAndMessages,
        Layout,
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        return {
            user,
        }
    },
    data() {
        return {
            projects: [],
            display:false,
            form:{
                id:"",
                validation:"",
                note:""
            },
            dataPerPage: 10, 
            totalData: 0, 
            error: {},
            lazyParams: {
                page: 1
            },
            loading: false
        };
    },
    props: {
        errors: Object,
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

        onValidation(data) {
            this.form.id = data.id;
            this.form.validation = '';
            this.form.note = '';
            this.display = true;
        },

        async simpanValidation() {
            try {
                this.display = false;

                if (this.form.id && this.form.validation.trim() !== '' && this.form.note.trim() !== '') {
                    await validation(this.form); 
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
            case "pending":
            return "badge badge-secondary";
            case "to do":
            return "badge badge-primary";
            case "doing":
            return "badge badge-info";
            case "submission":
            return "badge badge-dark";
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

<style scoped>
.custom-select {
    font-size: 14px;
}
.status-badge {
    min-width: 80px; 
}
</style>
