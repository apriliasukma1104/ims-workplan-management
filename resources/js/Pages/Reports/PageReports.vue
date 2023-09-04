<template>
    <layout title="Reports">
    <Toast position="top-center" />
        <div class="card">
            <Toolbar class="p-mb-4">
                <template #left>
                    <h6><b>Project Progress</b></h6>
                </template>
                <template #right>
                    <span>
                        <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
                        <Button icon="pi pi-search" iconPos="right"  class="p-button-sm"  @click="onSearch" />
                        <Button icon="pi pi-print" class="p-button-sm" style="margin-left: 10px" @click="onPrint" />
                    </span>
                </template>
            </Toolbar>

            <DataTable :value="reports" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :totalData="total" :loading="loading" @page="onPage($event)" responsiveLayout="scroll">
                <Column field="" header="No">
                    <template #body="slotProps">
                        {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="" header="Project">
                    <template #body="slotProps">
                    {{ slotProps.data.project_name }}
                    <br>
                    <small>Due: {{ slotProps.data.due }}</small>
                    </template>
                </Column>
                <Column field="total_tasks" header="Tasks"></Column>
                <Column field="completed_task" header="Completed Task"></Column>
                <Column field="" header="Work Duration">
                    <template #body="slotProps">
                        {{ slotProps.data.work_duration }} day
                    </template>
                </Column>
                <Column field="progress" header="Progress">
                    <template #body="slotProps">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-orange" role="progressbar" :aria-valuenow="slotProps.data.progress"
                                aria-valuemin="0" aria-valuemax="100" :style="'width: ' + slotProps.data.progress + '%'">
                            </div>
                        </div>
                        <small>{{ slotProps.data.progress.toFixed(2) }}% Complete</small>
                    </template>
                </Column>
                <Column field="status" header="Status">
                    <template #body="slotProps">
                        <span :class="['status-badge', getStatusBadgeClass(slotProps.data.status)]">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
                <Column field="validation" header="Validation">
                </Column>
                <Column field="note" header="Note">
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
import { pageListReports } from '../../Api/reports.api.js';
export default {
    name: "PageReports",
    components: {
        ErrorsAndMessages,
        Layout,
    },

    data() {
        return {
        reports: [],
        dataPerPage: 3,
        display: false,
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
            this.loading = true;
            var response = await pageListReports ({ page : this.lazyParams.page, search: this.search });
            this.reports = response.data.reports.data;
            this.loading = false;
        },
        onSearch(){
            this.lazyParams.page = 1;
            this.loadLazyData();
        },
        onPage(event) {
            this.lazyParams.page = event.page  + 1;
            this.loadLazyData();
        }, 
        getStatusBadgeClass(status) {
        switch (status) {
            case "pending":
            return "badge badge-secondary";
            case "started":
            return "badge badge-primary";
            case "on-progress":
            return "badge badge-info";
            case "review":
            return "badge badge-dark";
            case "done":
            return "badge badge-success";
            case "over due":
            return "badge badge-danger";
            default:
            return "badge";
        }
        },
        onPrint() {
        window.print();
        },
    },

    mounted() {
        this.reports = this.$page.props.reports.data;
        this.totalData = this.$page.props.reports.total;
    }

};
</script>

<style scoped>
.status-badge {
  min-width: 80px; 
}
</style>