<template>
    <layout title="Queue List">
    <Toast position="top-center" />

        <ConfDialog :visible.sync="visible" :message="messageConfirm" v-on:onApprove="SaveApprove()"></ConfDialog>

        <Dialog header="Header" v-model:visible="display" style="width: 400px;" >
            <template #header>
                <label>Rejection Note</label>
            </template>
            <div class="p-fluid">
                <textarea v-model="form.note" class="form-control" style="height: 150px;"></textarea>
            </div>
            <template #footer>
                <Button @click="saveNotApprove" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
            </template>
        </Dialog>

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
                <Column :exportable="false" header="Action">
                    <template #body="slotProps">
                        <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" :title="'Edit'" style="margin-right: 5px;" />
                        <Button @click="onNotApprove(slotProps.data)" icon="pi pi-times" class="p-button-rounded p-button-danger" :title="'Reject'" style="margin-right: 5px;" />
                        <Button @click="onApprove(slotProps.data)" icon="pi pi-check" class="p-button-rounded p-button-success" :title="'Approve'" />
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
import ConfDialog from "../../Components/ConfDialog.vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref } from "vue";
import { formatRupiah } from "../../utils/index.js";
import { pageQueue } from '../../Api/queue.api.js';
import { approve, notApprove  } from '../../Api/history.api.js';
 
export default {
    name: "ListWorkplans",
    components: {
        Layout,
        ErrorsAndMessages,
        ConfDialog
    },
    setup() {
        const user = computed(() => usePage().props.value.auth.user);

        return {
            user
        }
    },
    data() {
        return {
            projects: [],
            display:false,
            form:{},
            selectedDataApprove:[],
            messageConfirm:null,
            visible:false,
            dataPerPage: 10, 
            totalData: 0, 
            error: {},
            lazyParams: {
                page: 1
            },
            loading: false,
        };
    },
    props: {
        errors: Object,
    },
    methods: {
        async loadLazyData() {
            this.loading = true;
            var response = await pageQueue ({ page : this.lazyParams.page, search: this.search });
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
        onEdit(data) {
        this.$inertia.visit(`/queue/edit_queue?id=${data.id}`);
        },
        onApprove(data){
            this.visible = true;
            this.messageConfirm = `Are you sure? If you proceed, the work plan with code ${data.code_workplans} will be locked as an active work plan to work on.`;
            this.selectedDataApprove = data;
        },
        async SaveApprove(){
            this.visible = false;
            this.form.id = this.selectedDataApprove.id;
            this.form.validation = 'Approved';
            await approve(this.form); 
            this.$toast.add({severity:'success', summary: 'Information!', detail:'Data Saved Successfully!', life: 3000});
            this.loadLazyData();
        },
        onNotApprove(data) {
            this.display = true;
            this.form.id = data.id;
            this.form.validation = 'Not Approved';
            this.form.note = '';
        },
        async saveNotApprove() {
            try {
                this.display = false;

                if (this.form.id && this.form.note) {
                    await notApprove(this.form); 
                    this.$toast.add({severity:'success', summary: 'Information!', detail:'Data Saved Successfully!', life: 3000});
                } else {
                    this.$toast.add({severity:'error', summary: 'Error!', detail:'Please fill in the note fields!', life: 3000});
                }
                this.loadLazyData();
            } catch (error) {
                this.$toast.add({severity:'error', summary: 'Error!', detail:'Data Failed to Save!', life: 3000});
            }
        },
        getStatusBadgeClass(status) {
        switch (status) {
            case "Submitted":
            return "badge badge-warning";
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