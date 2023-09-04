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
          <div class="card-body p-0 ml-2 mr-2">
            <DataTable :value="dashboard" :lazy="true" :rows="totalData" ref="dt" :loading="loading" responsiveLayout="scroll" class="table-hover">
              <Column field="" header="No">
                <template #body="slotProps">
                  {{ slotProps.index + 1 }}
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
import {Inertia} from "@inertiajs/inertia";
import {computed, inject} from "vue";

export default {
    name: "Posts",
    components: {
        ErrorsAndMessages,
        Layout,
    },
   data() {
        return {
          dashboard: [],
          lazyParams: {},
          loading:false
        }
    },
    props: {
        errors: Object,
        total_projects: Number,
        total_tasks: Number,
    },
    methods:{
        loadLazyData() {
            this.loading = true;
            axios.post('/dashboard', this.lazyParams).then(response=>{
                 this.data = response.data;
                 this.loading = false;
            })
        },
        onPage(event) {
            this.lazyParams = event;
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
    setup() {     
        const user = computed(() => usePage().props.value.auth.user);
        return {
            user
        }
    },
    mounted(){
        this.dashboard = this.$page.props.dashboard;
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
