<template>
    <layout title="Project List">
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
                <Column field="name" header="Project Name"></Column>
                <Column field="project_type" header="Project Type"></Column>
                <Column field="team_leader" header="Team Leader">
                    <template #body="slotProps">
                        <span>{{ slotProps.data.team_leader_name }}</span>
                    </template>
                </Column>
                <Column field="start_date" header="Start Date"></Column>
                <Column field="end_date" header="End Date"></Column>
                <Column field="status" header="Status">
                    <template #body="slotProps">
                        <span :class="getStatusClass(slotProps.data.status)">
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
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
import { storeProjects, getTeamLeaderName, deleteProjects } from '../../Api/projects.api.js';

export default {
    name: "ListProjects",
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
                name: null, 
                project_type: null,
                team_leader: null,
                team_members: null,
                start_date: null,
                end_date: null,
                status: null,
                description: null
            },
            initform: {
                id: null,
                name: null,
                project_type: null,
                team_leader: null,
                team_members: null,
                start_date: null,
                end_date: null,
                status: null,
                description: null
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
            window.location.reload();
            const res = await storeProjects({ page: this.lazyParams.page + 1, search: this.search });
            this.data = res.projects.data; // Ubah menjadi res.data, sesuai dengan respons dari server
        },
        onSearch(){
            this.lazyParams.page = 1;
            this.loadLazyData();
        },
        onEdit(data) {
        this.$inertia.visit(`/projects/list/edit_projects?id=${data.id}`);
        },
        async getTeamLeaderName(team_leader_id) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = fetch(`/projects/list/team_leader_name`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken, // Sertakan token CSRF dalam header permintaan
                    },
                    body: JSON.stringify({ team_leader_id }), // Kirim data team_leader_id sebagai JSON di dalam body permintaan
                });
                const data = response.json();
                return data.name;
                console.log(data.name)
            } catch (error) {
                console.error("Error while fetching team leader name:", error);
                return "";
            }
        },
        
        onDelete(data) {
            if (window.confirm("Yakin ingin menghapus data?")) {
                deleteProjects({ id: data.id })
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

        getStatusClass(status) {
        switch (status) {
            case "to do":
            return "badge badge-primary";
            case "doing":
            return "badge badge-info";
            case "done":
            return "badge badge-success";
            default:
            return "badge";
        }
        },
        
    },

    props: {
        errors: Object
    },
    mounted() {
        this.data = this.$page.props.projects; // Assign data dari server ke variabel data
        this.totalData = this.$page.props.projects.length; // Set jumlah total data dari server
       
    }

};
</script>

<style scoped>
.badge {
  font-size: 12px;
  padding: 6px 8px;
  border-radius: 4px;
}

.badge-primary {
  background-color: #007bff;
  color: #fff;
}

.badge-info {
  background-color: #17a2b8;
  color: #fff;
}

.badge-success {
  background-color: #28a745;
  color: #fff;
}
</style>
