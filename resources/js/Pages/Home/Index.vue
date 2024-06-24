<template>
  <layout>
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-3" v-if="user && user.role === 'Admin'">
          <div class="small-box">
            <div class="inner">
              <h3>{{ total_members }}</h3>
              <p>Total Members</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-secondary">
            <div class="card-body p-0 ml-2 mr-2 mt-3">
              <Toolbar class="p-mb-4">
                <template #left>
                  <div class="p-field">
                    <Dropdown v-model="selectYear" :options="uniqueYears" optionLabel="year" optionValue="year" 
                      @change="onSelectYearChange" style="font-size: 14px; width: 140px;" />
                  </div>
                </template>
              </Toolbar>
              <div class="row mt-3">
                <div class="col-md-6">
                  <p><b>Created Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueCreated" class="progress-bar-created"> {{ progressTextCreated }} </ProgressBar>
                  </div>
                </div>
                <div class="col-md-6">
                  <p><b>Submitted Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueSubmitted" class="progress-bar-submit"> {{ progressTextSubmitted }} </ProgressBar>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <p><b>On Progress Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueOnProgress"> {{ progressTextOnProgress }} </ProgressBar>
                  </div>
                </div>
                <div class="col-md-6">
                  <p><b>Completed Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueCompleted" class="progress-bar-completed"> {{ progressTextCompleted }} </ProgressBar>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <p><b>Overdue Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueOverdue" class="progress-bar-overdue"> {{ progressTextOverdue }} </ProgressBar>
                  </div>
                </div>
                <div class="col-md-6">
                  <p><b>Rejected Work Plans in {{ selectYear }}</b></p>
                  <div class="card">
                    <ProgressBar :value="progressValueRejected" class="progress-bar-rejected"> {{ progressTextRejected }} </ProgressBar>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row" v-show="user && user.role === 'Kadep'">
        <div class="col-md-12">
        <div class="card card-outline card-secondary">
          <div class="card-body p-0 ml-2 mr-2 mt-3">
            <h5 style="text-align: center;"><b>Work Plan Data Accumulation Chart in the Department</b></h5>
            <div class="row">
              <div class="col-md-6">
                <div class="card p-0 mt-3">
                  <p style="text-align: center; margin-top: 10px;"><b>Bar Chart (Monthly)</b></p>
                  <apexchart ref="barChartDept" type="bar" :options="barChartOptionsDept" :series="barChartSeriesDept" />
                </div>
              </div>
              <div class="col-md-6">
                <div class="card p-0 mt-3">
                  <p style="text-align: center; margin-top: 10px;"><b>S-Curve (Cumulative)</b></p>
                  <apexchart ref="lineChartDept" type="line" :options="lineChartOptionsDept" :series="lineChartSeriesDept" />
                </div>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card card-outline card-secondary">
            <div class="card-body p-0 ml-2 mr-2 mt-3">
              <Toolbar class="p-mb-4">
                <template #left>
                  <div class="p-field">
                    <Dropdown v-model="selectStatus" :options="statusOptions" optionLabel="label" optionValue="value" 
                      @change="onSelectStatusChange" style="font-size: 14px; width: 160px;" />
                    <Dropdown v-model="selectName" :options="filteredProjects" optionLabel="name" optionValue="id" 
                      @change="onSelectNameChange" style="font-size: 14px; width: 400px; margin-left: 10px;" />
                    <InputText type="text" v-model="selectLeader" style="font-size: 14px; width: 200px; margin-left: 10px;" readonly />
                  </div>
                </template>
                <template #right>
                  <label style="margin-right: 10px;">Progress (%): </label>
                  <InputText type="text" v-model="formattedProgress" style="font-size: 18px; text-align: center; width: 68px; height: 50px;" readonly />
                </template>
              </Toolbar>

              <div class="row">
                <div class="col-md-6">
                  <div class="card p-0 mt-3">
                    <p style="text-align: center; margin-top: 10px;"><b>Bar Chart (Monthly)</b></p>
                    <apexchart ref="barChart" type="bar" :options="barChartOptions" :series="barChartSeries" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card p-0 mt-3">
                    <p style="text-align: center; margin-top: 10px;"><b>S-Curve (Cumulative)</b></p>
                    <apexchart ref="lineChart" type="line" :options="lineChartOptions" :series="lineChartSeries" />
                  </div>
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
import { usePage } from "@inertiajs/inertia-vue3";
import { replaceDotWithComma } from "../../utils/index.js";
import VueApexCharts from "vue3-apexcharts";
import axios from 'axios';
import { computed, ref } from "vue";

export default {
  name: "Posts",
  components: {
    ErrorsAndMessages,
    Layout,
    apexchart: VueApexCharts
  },
  props: {
    total_members: Number
  },
  data() {
    return {
      statusOptions: [
        { label: 'On progress', value: 'On progress' },
        { label: 'Overdue', value: 'Overdue' },
        { label: 'Completed', value: 'Completed' }
      ],
      // Bar Chart Dept
      barChartSeriesDept: [],
      barChartOptionsDept: {
        chart: {
          type: 'bar'
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          labels: {
            formatter: (val) => val % 2 === 0 ? val : ''
          }
        },
        fill: {
          opacity: 1
        },
        colors: ['#2d4353', '#ff0008'],
        dataLabels: {
          enabled: false
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            }
          }
        }
      },

      // Line Chart Dept
      lineChartSeriesDept: [],
      lineChartOptionsDept: {
        lineChart: {
          type: 'line'
        },
        stroke: {
          curve: 'smooth',
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          labels: {
            formatter: (val) => val % 2 === 0 ? val : ''
          }
        },
        fill: {
          opacity: 1
        },
        colors: ['#2d4353', '#ff0008'],
        dataLabels: {
          enabled: false
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            }
          }
        }
      },

      // Bar Chart
      barChartSeries: [],
      barChartOptions: {
        barChart: {
          type: 'bar'
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          labels: {
            formatter: (val) => val % 2 === 0 ? val : ''
          }
        },
        fill: {
          opacity: 1
        },
        colors: ['#2d4353', '#ff0008'],
        dataLabels: {
          enabled: false
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            }
          }
        }
      },

      // Line Chart
      lineChartSeries: [],
      lineChartOptions: {
        lineChart: {
          type: 'line'
        },
        stroke: {
          curve: 'smooth',
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          labels: {
            formatter: (val) => val % 2 === 0 ? val : ''
          }
        },
        fill: {
          opacity: 1
        },
        colors: ['#2d4353', '#ff0008'],
        dataLabels: {
          enabled: false
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return val;
            }
          }
        }
      }
    };
  },
  setup(props) {
    const user = computed(() => usePage().props.value.auth.user);
    const projects = usePage().props.value.projects;

    const _replaceDotWithComma = (val) => {
        return replaceDotWithComma(val);
    };

    // Set Opsi Years Work Plan
    const uniqueYears = computed(() => {
      const years = projects.map(project => project.year);
      const uniqueYearsSet = new Set(years);
      return Array.from(uniqueYearsSet).map(year => ({ year })).sort((a, b) => b.year - a.year);
    });

    const selectYear = ref("");
    const selectStatus = ref("");
    const selectProgress = ref("");
    const selectName = ref("");
    
    // Set Opsi Names Work Plan
    const filteredProjects = computed(() => {
      if (!selectYear.value) return projects;
      return projects.filter(project => project.year === selectYear.value);
    });

    // Set Team Leader
    const selectLeader = computed(() => {
      const project = projects.find(p => p.id === selectName.value);
      return project ? project.team_leader.name : '';
    });

    const progressValueCreated = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsCreated = selectedProjects.filter(project => project.status === 'Created').length;
      return totalProjects > 0 ? (totalProjectsCreated / totalProjects) * 100 : 0;
    });

    const progressTextCreated = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsCreated = selectedProjects.filter(project => project.status === 'Created').length;
      return `${totalProjectsCreated} / ${totalProjects}`;
    });

    const progressValueSubmitted = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsSubmitted = selectedProjects.filter(project => project.status === 'Submitted').length;
      return totalProjects > 0 ? (totalProjectsSubmitted / totalProjects) * 100 : 0;
    });

    const progressTextSubmitted = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsSubmitted = selectedProjects.filter(project => project.status === 'Submitted').length;
      return `${totalProjectsSubmitted} / ${totalProjects}`;
    });

    const progressValueOnProgress = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsOnProgress = selectedProjects.filter(project => project.status === 'On progress').length;
      return totalProjects > 0 ? (totalProjectsOnProgress / totalProjects) * 100 : 0;
    });

    const progressTextOnProgress = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsOnProgress = selectedProjects.filter(project => project.status === 'On progress').length;
      return `${totalProjectsOnProgress} / ${totalProjects}`;
    });

    const progressValueCompleted = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsCompleted = selectedProjects.filter(project => project.status === 'Completed').length;
      return totalProjects > 0 ? (totalProjectsCompleted / totalProjects) * 100 : 0;
    });

    const progressTextCompleted = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsCompleted = selectedProjects.filter(project => project.status === 'Completed').length;
      return `${totalProjectsCompleted} / ${totalProjects}`;
    });

    const progressValueOverdue = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsOverdue = selectedProjects.filter(project => project.status === 'Overdue').length;
      return totalProjects > 0 ? (totalProjectsOverdue / totalProjects) * 100 : 0;
    });

    const progressTextOverdue = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsOverdue = selectedProjects.filter(project => project.status === 'Overdue').length;
      return `${totalProjectsOverdue} / ${totalProjects}`;
    });

    const progressValueRejected = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsRejected = selectedProjects.filter(project => project.status === 'Rejected').length;
      return totalProjects > 0 ? (totalProjectsRejected / totalProjects) * 100 : 0;
    });

    const progressTextRejected = computed(() => {
      const selectedProjects = projects.filter(project => project.year === selectYear.value);
      const totalProjects = selectedProjects.length;
      const totalProjectsRejected = selectedProjects.filter(project => project.status === 'Rejected').length;
      return `${totalProjectsRejected} / ${totalProjects}`;
    });

    const formattedProgress = computed(() => _replaceDotWithComma(selectProgress.value));

    return {
      user,
      projects,
      _replaceDotWithComma,
      uniqueYears,
      selectYear,
      selectStatus,
      selectProgress,
      selectName,
      filteredProjects,
      selectLeader,
      progressValueCreated, progressTextCreated,
      progressValueSubmitted, progressTextSubmitted,
      progressValueOnProgress, progressTextOnProgress,
      progressValueCompleted, progressTextCompleted,
      progressValueOverdue, progressTextOverdue,
      progressValueRejected, progressTextRejected,
      formattedProgress
    }
  },
  mounted() {
    this.setDefaultDropdownYear()
  },
  methods: {
    setDefaultDropdownYear() {
      if (this.uniqueYears && this.uniqueYears.length > 0) {
        const latestYear = this.uniqueYears[0].year;
        this.selectYear = latestYear;
        this.setDefaultDropdownStatus();
        this.setDefaultDropdownName();
      }
    },
    setDefaultDropdownStatus() {
      this.selectStatus = "On progress"; 
    },
    setDefaultDropdownName() {
      if (this.filteredProjects && this.filteredProjects.length > 0) {
        // Mengurutkan proyek berdasarkan ID dari terbesar ke terkecil
        const sortedProjects = this.filteredProjects.sort((a, b) => b.id - a.id);
        // Mengambil project dengan ID terbesar (terbaru)
        const latestProject = sortedProjects[0];
        // Mengatur nilai default untuk selectName
        this.selectName = latestProject.id;
        // Memanggil fetchChartData dengan ID proyek terbaru
        this.fetchChartData({ value: latestProject.id });
      }
    },
    async fetchChartData(item) {
      try {
        const year = this.selectYear;
        const status = this.selectStatus;
        const code_workplans = this.selectName; 

        // Bar Chart Dept
        const resBarChartDept = await axios.get(`/chart_data_dept_1`);
        this.barChartSeriesDept = resBarChartDept.data.dataSets.map(dataSet => ({
          name: dataSet.nama,
          data: dataSet.data,
        }));

        // Line Chart Dept
        const resLineChartDept = await axios.get(`/chart_data_dept_2`);
        this.lineChartSeriesDept = resLineChartDept.data.dataSets.map(dataSet => ({
          name: dataSet.nama,
          data: dataSet.data,
        }));

        // Bar Chart
        const resBarChart = await axios.get(`/chart_data_1`, { params: { year, status, code_workplans } });
        this.barChartSeries = resBarChart.data.dataSets.map(dataSet => ({
          name: dataSet.nama,
          data: dataSet.data,
        }));

        // Line Chart
        const resLineChart = await axios.get(`/chart_data_2`, { params: { year, status, code_workplans } });
        this.lineChartSeries = resLineChart.data.dataSets.map(dataSet => ({
          name: dataSet.nama,
          data: dataSet.data,
        }));

        // Update Grafik
        this.$refs.barChartDept.updateOptions({
          xaxis: {
            categories: resBarChartDept.data.categories
          }
        });
        this.$refs.lineChartDept.updateOptions({
          xaxis: {
            categories: resLineChartDept.data.categories
          }
        });
        this.$refs.barChart.updateOptions({
          xaxis: {
            categories: resBarChart.data.categories
          }
        });
        this.$refs.lineChart.updateOptions({
          xaxis: {
            categories: resLineChart.data.categories
          }
        });

        // Mengambil Data Progress
        const resProgress = await axios.get(`/dashboard/progress_projects`, { params: { code_workplans } });
        this.selectProgress = resProgress.data.progress; 
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },
    onSelectYearChange() {
      this.setDefaultDropdownName();
      this.fetchChartData();
    },
    onSelectStatusChange() {
      this.fetchChartData();
    },
    onSelectNameChange() {
      this.fetchChartData();
    }
  },
}
</script>

<style>
.progress-bar-created .p-progressbar-value {
  background-color: #6c757d; 
}
.progress-bar-submit .p-progressbar-value {
  background-color: #ffc107; 
}
.progress-bar-completed .p-progressbar-value {
  background-color: #28a745; 
}
.progress-bar-overdue .p-progressbar-value {
  background-color: #dc3545; 
}
.progress-bar-rejected .p-progressbar-value {
  background-color: #343a40; 
}
</style>