<template>
    <layout title="Member List">
    <ConfirmDialog></ConfirmDialog>
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
            <DataTable :value="members" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :loading="loading" @page="onPage($event)" responsiveLayout="scroll">
                <Column field="" header="No">
                    <template #body="slotProps">
                        {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>
                <Column field="position" header="Position"></Column>
                <Column field="role" header="Member Role"></Column>
                <Column field="email" header="Email"></Column>
                <Column :exportable="false" header="Action">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 10px;" />
                        <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-info" />
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
import { pageListMembers, deleteMember } from '../../Api/members.api.js';

export default {
    name: "ListMembers",
    components: {
        ErrorsAndMessages,
        Layout,
    },
    data() {
        return {
            members: [],
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
            var response = await pageListMembers ({ page : this.lazyParams.page, search: this.search });
            this.members = response.data.data.data;
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
        this.$inertia.visit(`/members/list/edit_member?id=${data.id}`);
        },
        onDelete(data) {
            if (window.confirm("Are you sure you want to delete data?")) {
                deleteMember({ id: data.id })
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
    },
    mounted() {
        this.members = this.$page.props.members.data; 
        this.totalData = this.$page.props.members.total; 
    }
};
</script>