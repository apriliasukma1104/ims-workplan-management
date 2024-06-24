<template>
    <layout title="History List">
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
                <Column header="Date">
                    <template #body="slotProps">
                    <small>
                        <b>Submitted on: </b>
                        <br>
                        {{ slotProps.data._date_submitted }}
                    </small>
                    <br>
                    <small>
                        <b>Approved/Rejected on: </b>
                        <br>
                        {{ slotProps.data._created_at }}
                    </small>
                    </template>
                </Column>
                <Column field="code_workplans" header="Code"></Column>
                <Column header="Work Plans" style="max-width: 250px;">
                    <template #body="slotProps">
                        {{ slotProps.data.name }}
                        <br>
                    <small>
                        <b>Description: </b>
                        <br>
                        {{ slotProps.data.description }}
                    </small>
                    </template>
                </Column>
                <Column field="project_type" header="Type"></Column>
                <Column header="PIC">
                    <template #body="slotProps">
                        {{ slotProps.data.team_leader.name }}
                        <br>
                    <small>
                        <b>Team Member/s: </b>
                        <div v-for="member in slotProps.data.team_members">
                            - {{ member.name }}
                        </div>
                    </small>
                    </template>
                </Column>
                <Column field="year" header="Year"></Column>
                <Column header="Budget">
                    <template #body="slotProps">
                        {{ _formatRupiah(slotProps.data._budget) }}
                    </template>
                </Column>
                <Column header="Status">
                    <template #body="slotProps">
                        <span :class="['status-badge', getStatusBadgeClass(slotProps.data.status)]">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
                <Column header="Validation">
                    <template #body="slotProps">
                        {{ slotProps.data.validation }}
                        <br>
                    <small>
                        <b>Note: </b>
                        <br>
                        {{ slotProps.data.note ? slotProps.data.note : '-' }}
                    </small>
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
import { pageHistory } from '../../Api/history.api.js';
import { formatRupiah } from "../../utils/index.js";

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
        async loadLazyData() {
            this.loading = true;
            var response = await  pageHistory ({ page : this.lazyParams.page, search: this.search });
            this.projects = response.data.data.data;
            this.totalData = response.data.data.total;
            this.loading = false;
        },
        _formatRupiah(val){
            return formatRupiah(val)
        },
        onSearch(){
            this.totalData = 0;
            this.loadLazyData();
        },
        onPage(event) {
            this.lazyParams.page = event.page + 1;
            this.loadLazyData();
        },
        getStatusBadgeClass(status) {
        switch (status) {
            case "On progress":
            return "badge badge-primary";
            case "Overdue":
            return "badge badge-danger";
            case "Rejected":
            return "badge badge-dark";
            case "Completed":
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