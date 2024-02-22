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
                <Column field="nip" header="NIP"></Column>
                <Column field="position" header="Position"></Column>
                <Column field="sub_department" header="Unit"></Column>
                <Column field="role" header="Role"></Column>
                <Column field="email" header="Email"></Column>
                <Column header="Status">
                    <template #body="slotProps">
                        <ToggleButton @click="onChangesToggle(slotProps.data)" v-model="slotProps.data.members_status" :class="{'p-button-sm p-button-success': slotProps.data.members_status, 'p-button-sm p-button-secondary': !slotProps.data.members_status}" 
                            style="width: 5rem;" onLabel="Active" offLabel="Inactive" />
                    </template>
                </Column>
                <Column header="Action" v-if="user && user.role === 'Admin'">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" />
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
import { pageListMembers, updateStatusMember } from '../../Api/members.api.js';

export default {
    name: "ListMembers",
    components: {
        ErrorsAndMessages,
        Layout,
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        return {
            user
        }
    },
    data() {
        return {
            members: [],
            dataPerPage: 10, 
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
       async onChangesToggle(item){
            this.loading = true;
            var response = await updateStatusMember(item);
            this.loading = false;
            this.loadLazyData();
        },
        async loadLazyData() {
            this.loading = true;
            var response = await pageListMembers ({ page : this.lazyParams.page, search: this.search });
            this.members = response.data.data.data;
            this.members = this.members.map(function(x) { 
                x.members_status = parseInt(x.members_status); 
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
        this.$inertia.visit(`/members/list/edit_member?id=${data.id}`);
        },
    },
    mounted() {
        this.members = this.$page.props.members.data; 
        this.members = this.members.map(function(x) { 
            x.members_status = parseInt(x.members_status); 
            return x
            });
        this.totalData = this.$page.props.members.total; 
    }
};
</script>