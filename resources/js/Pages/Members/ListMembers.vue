<template>
    <layout title="Member List">
    <Toast position="top-center" />

    <ConfDialogDelete :visible.sync="visible" :message="messageConfirm" v-on:onDelete="SaveDelete()"></ConfDialogDelete>
    
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
                <Column header="Action" v-if="user && user.role === 'Admin'">
                    <template #body="slotProps">
                        <ToggleButton @click="onChangesToggle(slotProps.data)" v-model="slotProps.data.members_status" :class="{'p-button-sm p-button-success': slotProps.data.members_status, 'p-button-sm p-button-secondary': !slotProps.data.members_status}" 
                            onIcon="pi pi-lock" offIcon="pi pi-lock-open" class="p-button-rounded" style="margin-right: 5px;" />
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" :title="'Edit'" style="margin-right: 5px;" />
                        <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-danger" :title="'Delete'" />
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
import ConfDialogDelete from "../../Components/ConfDialogDelete.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";
import { pageListMembers, updateStatusMember, deleteMember } from '../../Api/members.api.js';

export default {
    name: "ListMembers",
    components: {
        Layout,
        ErrorsAndMessages,
        ConfDialogDelete
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
            form:{},
            dataPerPage: 10, 
            totalData: 0, 
            display: false,
            messageConfirm:null,
            visible:false,
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
        onDelete(data){
            this.visible = true;
            this.messageConfirm = `Do you want to delete this data (NIP: ${data.nip})?`;
            this.selectedDataDelete = data;
        },
        async SaveDelete(){
            this.visible = false;
            this.form.id = this.selectedDataDelete.id;
            await deleteMember(this.form); 
            this.$toast.add({severity:'info', summary: 'Deleted!', detail:'Data Deleted Successfully!', life: 3000});
            this.loadLazyData();
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