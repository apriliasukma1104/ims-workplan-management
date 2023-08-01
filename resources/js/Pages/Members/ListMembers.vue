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
            <DataTable :value="data" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalData" :loading="loading" @page="onPage($event)" responsiveLayout="scroll">
                <Column field="" header="No">
                    <template #body="slotProps">
                        {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                    </template>
                </Column>
                <Column field="image" header="Image">
                    <template #body="slotProps">
                        <img :src="slotProps.data.image" style="max-width: 2cm; max-height: 2cm;" />
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>
                <Column field="position" header="Position"></Column>
                <Column field="role" header="Member Role"></Column>
                <Column field="email" header="Email"></Column>
                <Column :exportable="false" header="Action">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 10px;" />
                        <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-danger" />
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
import { storeMembers, deleteMembers } from '../../Api/members.api.js';

export default {
    name: "ListMembers",
    components: {
        ErrorsAndMessages,
        Layout,
    },
    data() {
        return {
            data: [],
            dataPerPage: 10, // Ubah sesuai dengan jumlah data per halaman yang diinginkan
            totalData: 0, // Jumlah total data anggota
            display: false,
            search:null,
            form: {
                id: null,
                image: null,
                name: null, 
                position: null, 
                role: null,
                email: null
            },
            initform: {
                id: null,
                image: null,
                name: null,
                position: null, 
                role: null,
                email: null
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
            // window.location.reload();
            this.loading = true;
            const res = await storeMembers({page : this.lazyParams.page + 1, search:this.search})

            this.data = res.data.data;

            this.loading = false;
        },
        onSearch(){
            this.lazyParams.page = 0;
            this.loadLazyData();
        },
        onEdit(data) {
        this.$inertia.visit(`/members/list/edit_members?id=${data.id}`);
        },
        onDelete(data) {
            if (window.confirm("Yakin ingin menghapus data?")) {
                deleteMembers({ id: data.id })
                    .then(() => {
                        this.$toast.add({
                            severity: "success",
                            summary: "Informasi!",
                            detail: "Berhasil Dihapus",
                            life: 3000,
                        });
                        this.loadLazyData();
                    })
                    .catch((error) => {
                        console.error("Error while deleting:", error);
                        this.$toast.add({
                            severity: "error",
                            summary: "Error!",
                            detail: "Gagal menghapus data",
                            life: 3000,
                        });
                    });
            }
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
        this.data = this.$page.props.members; // Assign data dari server ke variabel data
        this.totalData = this.$page.props.members.length; // Set jumlah total data dari server
    }
};
</script>

<style scoped>
</style>
