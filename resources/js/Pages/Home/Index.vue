<template>
<layout>
    <div class="col-12">
        <div class="card custom-card">
        <div class="card-body">
            <h5>Welcome  {{user.name}} !</h5>
        </div>
        </div>
    </div>
    <hr>
    <div class="col-md-12">
    <div class="row">
      <div class="col-md-8">
        <div class="card card-outline card-secondary">
          <div class="card-body p-0 ml-2 mr-2 mt-3">
            <Toolbar class="p-mb-4">
                <template #left>
                    <span>
                        <Button icon="pi pi-search" iconPos="right"  class="p-button-sm"  @click="onSearch" />
                        <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
                    </span>
                </template>
            </Toolbar>
            <DataTable :value="formattedDashboard" :lazy="true" :paginator="true" :rows="dataPerPage" ref="dt"
                :totalRecords="totalProject" :totalProject="total" :loading="loading" @page="onPage($event)" responsiveLayout="scroll">
              <Column field="" header="No">
                <template #body="slotProps">
                  {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                </template>
              </Column>
              <Column field="project_name" header="Project"></Column>
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
                    <div>
                      <span :class="['status-badge', getStatusBadgeClass(slotProps.data.status)]">
                        {{ slotProps.data.status }}
                      </span>
                    </div>
                  </template>
              </Column>
              <template #empty>
                  No records found
              </template>
            </DataTable>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-12 col-sm-6 col-md-12">
            <div class="small-box bg-light shadow-sm border">
              <div class="inner">
                <h3>{{ total_projects }}</h3>
                <p>Total Projects</p>
              </div>
              <div class="icon">
                <i class="fa fa-th-list"></i>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-12">
            <div class="small-box bg-light shadow-sm border">
              <div class="inner">
                <h3>{{ total_tasks }}</h3>
                <p>Total Tasks</p>
              </div>
              <div class="icon">
                <i class="fa fa-tasks"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</layout>   
</template>

<script>
import Layout from "../../Partials/Layout";
import ErrorsAndMessages from "../../Partials/ErrorsAndMessages";
import {usePage} from "@inertiajs/inertia-vue3";
import {computed} from "vue";
import { pageListDashboard } from '../../Api/dashboard.api.js';

export default {
    name: "Posts",
    components: {
        ErrorsAndMessages,
        Layout,
    },
   data() {
        return {
          formattedDashboard: [],
          dataPerPage: 5,
          lazyParams: {
            page: 1,
          },
          loading:false
        }
    },
    setup() {     
        const user = computed(() => usePage().props.value.auth.user);
        return {
            user
        }
    },
    props: {
        errors: Object,
        total_projects: Number,
        total_tasks: Number,
    },
    methods:{
        async loadLazyData() {
            this.loading = true;
            var response = await pageListDashboard ({ page : this.lazyParams.page, search: this.search });
            this.formattedDashboard = response.data.dashboard.data;
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
    },
    mounted(){
        this.formattedDashboard = this.$page.props.dashboard.data;
        this.totalProject = this.$page.props.dashboard.total;
    }
}
</script>

<style scoped>
.custom-card {
  background: linear-gradient(to bottom right, #ff8000, #d05428);
  color: white;
}
.status-badge {
  min-width: 80px; 
}
</style>
