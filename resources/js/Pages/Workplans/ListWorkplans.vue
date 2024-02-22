<template>
    <layout title="Work Plan List">
    <Toast position="top-center" />

        <div class="card">
            <Toolbar class="p-mb-4">
                <template #left>
                    <Calendar v-model="date" view="year" dateFormat="yy" placeholder="Select a Year" />
                </template>
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
                <Column field="name" header="Work Plans"></Column>
                <Column field="project_type" header="Type"></Column>
                <Column field="team_leader" header="PIC">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.team_leader.name }}</span>
                    </template>
                </Column>
                <Column field="year" header="Year"></Column>
                <Column field="" header="Work Plans Status"></Column>
                <Column field="" header="Validation"></Column>
                <!-- <Column field="" header="Status">
                    <template #body="slotProps">
                        <span :class="['status-badge', getStatusBadgeClass(slotProps.data.status)]">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column> -->
                <Column header="Status" v-if="user.role === 'Kabag'">
                    <template #body="slotProps">
                        <ToggleButton @click="onChangesToggle(slotProps.data)" v-model="slotProps.data.projects_status" :class="{'p-button-sm p-button-success': slotProps.data.projects_status, 'p-button-sm p-button-secondary': !slotProps.data.projects_status}" 
                            style="width: 5rem;" onLabel="Active" offLabel="Inactive" />
                    </template>
                </Column>
                <Column :exportable="false" header="Action">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 5px;" v-if="user.role === 'Kabag'" />
                        <Button @click="onView(slotProps.data)" icon="pi pi-eye" class="p-button-rounded p-button-warning" style="margin-right: 5px;" />
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
import { computed, ref } from "vue";
import { pageListWorkplans, updateStatusProject } from '../../Api/workplans.api.js';

export default {
    name: "ListWorkplans",
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
        async onChangesToggle(item){
            this.loading = true;
            var response = await updateStatusProject(item);
            this.loading = false;
            this.loadLazyData();
        },
        async loadLazyData() {
            this.loading = true;
            var response = await pageListWorkplans ({ page : this.lazyParams.page, search: this.search });
            this.projects = response.data.data.data;
            this.projects = this.projects.map(function(x) { 
                x.projects_status = parseInt(x.projects_status); 
                return x
            });
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
        this.$inertia.visit(`/workplans/list/edit_workplan?id=${data.id}`);
        },
        onView(data) {
        this.$inertia.visit(`/workplans/list/view_workplan?id=${data.id}`);
        },

        // getStatusBadgeClass(status) {
        // switch (status) {
        //     case "pending":
        //     return "badge badge-secondary";
        //     case "to do":
        //     return "badge badge-primary";
        //     case "doing":
        //     return "badge badge-info";
        //     case "submission":
        //     return "badge badge-dark";
        //     case "done":
        //     return "badge badge-success";
        //     default:
        //     return "badge";
        // }
        // },
    },
    mounted() {
        this.projects = this.$page.props.projects.data; 
        this.projects = this.projects.map(function(x) { 
            x.projects_status = parseInt(x.projects_status); 
            return x
            });
        this.totalData = this.$page.props.projects.total;
    }
};

const date = ref();

</script>

<style scoped>
.custom-select {
    font-size: 14px;
}
.status-badge {
    min-width: 80px; 
}
</style>
