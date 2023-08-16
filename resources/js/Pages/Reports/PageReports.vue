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
                        <Button icon="pi pi-print" class="p-button-sm" style="margin-left: 10px" @click="onPrint(slotProps.data)" />
                    </span>
                </template>
            </Toolbar>

            <DataTable :value="reports" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :loading="loading" :currentPage="lazyParams.page" @page="onPage($event)" responsiveLayout="scroll">
                <Column field="" header="No">
                    <template #body="slotProps">
                        {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="" header="Project Name">
                    <template #body="slotProps">
                    {{ slotProps.data.project_name }}
                    <br>
                    <small>Due: {{ slotProps.data.due }}</small>
                    </template>
                </Column>
                <Column field="total_tasks" header="Total Tasks"></Column>
                <Column field="completed_task" header="Completed Task"></Column>
                <Column field="work_duration" header="Work Duration">
                    <template #body="slotProps">
                        {{ slotProps.data.work_duration }} days
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
                <!-- <Column field="status" header="Status"></Column> -->
                <Column field="status" header="Status">
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
    name: "PageReports",
    components: {
        ErrorsAndMessages,
        Layout,
    },

    data() {
        return {
        reports: [],
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
            case "done":
            return "badge badge-success";
            case "over due":
            return "badge badge-danger";
            default:
            return "badge";
        }
        },
    },

    mounted() {
        this.reports = this.$page.props.reports; 
        this.totalData = this.$page.props.reports.total; 
    }

};
</script>