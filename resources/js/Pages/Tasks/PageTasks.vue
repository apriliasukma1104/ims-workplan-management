<template>
    <layout title="Tasks List">
    <Toast position="top-center" />
        <div class="card">
            <Toolbar class="p-mb-4">
                <template #left>
                        <Button label="Add Productivity" icon="pi pi-plus" class="p-button-primary p-button-sm"  @click="tambah"/>
                </template>
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
                <Column field="" header="Project Name"></Column>
                <Column field="" header="Tasks"></Column>
                <Column field="" header="Project Started"></Column>
                <Column field="" header="Project End Date"></Column>
                <Column field="" header="Project Status"></Column>
                <Column field="" header="Task Status"></Column>
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
    name: "PageReport",
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
        },
        onSearch(){
            this.lazyParams.page = 1;
            this.loadLazyData();
        },
        onPage(event) {
            this.lazyParams.page = event.page + 1;
            this.loadLazyData();
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