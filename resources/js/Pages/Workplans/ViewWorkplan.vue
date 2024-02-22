<template>
    <layout title="View Work Plan">
    <Toast position="top-center" />

      <Dialog header="Header" v-model:visible="dataTaskPlan.display" :style="{ width: '40vw' }" >
        <template #header>
                <label>Task (Plan)</label>
                </template>
                <div class="p-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="p-field">
                                    <label for="type">Type<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskPlan.type" :options="type" placeholder="Select a Type" />
                                </div>
                                <div class="p-field">
                                    <InputText v-model="formTaskPlan.id" type="text" hidden />
                                    <InputText v-model="formTaskPlan.name_member" type="text" :value="user.name" hidden />
                                    <label for="task">Task<span style="color:red;">*</span></label>
                                    <InputText v-model="formTaskPlan.task" type="text" />
                                </div>
                                <div class="p-field">
                                    <label for="description">Description<span style="color:red;">*</span></label>
                                    <Area v-model="formTaskPlan.description"  class="form-control" style="height: 42px;"></Area>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="p-field">
                                    <label for="start_month">Start Month<span style="color:red;">*</span></label> 
                                    <InputText v-model="formTaskPlan.start_month" type="date" />                               
                                </div>
                                <div class="p-field">
                                    <label for="end_month">End Month<span style="color:red;">*</span></label>  
                                    <InputText v-model="formTaskPlan.end_month" type="date" />                               
                                </div>
                                <div class="p-field">
                                    <label for="tot_weight_plan">Weight<span style="color:red;">*</span></label>
                                    <InputText v-model="formTaskPlan.tot_weight_plan" type="text" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <template #footer>
                    <Button @click="simpanTaskPlan" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                </template>
      </Dialog>

      <Dialog header="Header" v-model:visible="dataWeightTaskPlan.display" :style="{ width: '25vw' }" >
        <template #header>
                <label>Weight Task (Plan)</label>
                </template>
                <div class="p-fluid">
                    <div class="form-group">
                                <div class="p-field">
                                    <InputText v-model="formWeightTaskPlan.id" type="text" hidden/>
                                    <label for="id_task_plan">Task<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formWeightTaskPlan.id_task_plan" :options="task_plans" optionLabel="task" optionValue="id" placeholder="Select a Task" />
                                </div>
                                <div class="p-field">
                                    <label for="month_plan">Month<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formWeightTaskPlan.month_plan" :options="filteredMonths" placeholder="Select a Month" />
                                </div>
                                <div class="p-field">
                                    <label for="weight_plan">Weight<span style="color:red;">*</span></label>
                                    <InputText v-model="formWeightTaskPlan.weight_plan" type="text" />
                                </div>
                            </div>
                </div>
                <template #footer>
                    <Button @click="simpanWeightTaskPlan" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                </template>
      </Dialog>

      <Dialog header="Header" v-model:visible="dataTaskRealization.display" :style="{ width: '25vw' }" >
        <template #header>
                <label>Task (Realization)</label>
                </template>
                <div class="p-fluid">
                    <div class="form-group">
                                <div class="p-field">
                                    <label for="type">Type<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskRealization.type" :options="type" placeholder="Select a Type" />
                                </div>
                                <div class="p-field">
                                    <InputText v-model="formTaskRealization.id" type="text" hidden/>
                                    <InputText type="text" :value="formData.name" hidden/>
                                    <label for="task">Task<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskRealization.id_task_plan" :options="task_plans" optionLabel="task" optionValue="id" placeholder="Select a Task" />
                                </div>
                                <div class="p-field">
                                    <label for="month">Month<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskRealization.month" :options="month" placeholder="Select a Month" />
                                </div>
                                <div class="p-field">
                                    <label for="weight">Weight<span style="color:red;">*</span></label>
                                    <InputText v-model="formTaskRealization.weight" type="text" />
                                </div>
                            </div>
                </div>
                <template #footer>
                    <Button @click="simpanTaskRealization" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                </template>
      </Dialog>
    
      <form>
        <div class="callout callout">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mt-3 ml-3">
                    <label class="control-label">Work Plan</label>
                    <input type="text" name="id" class="form-control form-control-sm" required v-model="formData.id" hidden>
                    <div>{{ formData.name }}</div>
                </div>
                <div class="form-group mt-3 ml-3">
                    <label class="control-label">Description</label>
                    <div>{{ formData.description }}</div>
                </div>
                <div class="form-group mt-3 ml-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">Type</label>
                    <div>{{ formData.project_type }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-3 mr-3">
                    <label class="control-label" style="display: block; margin-top: 1rem;">PIC</label>
                    <div>{{ formData.team_leader.name }}</div>
                </div>
                <div class="form-group mt-3 mr-3">
                    <label class="control-label">Year</label>
                    <div>{{ formData.year }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-3 mr-3">
                    <label class="control-label">Team Member/s</label>
                    <div style="display: flex;">
                        <div v-for="members in formData.team_members" style="margin-right: 5px; width: 100px;">
                            <div style="text-align: center;">
                                <img
                                    :src="'/images/user.png'"
                                    width="40"
                                    height="40"
                                />
                                <br>
                                <span style="font-size: 14px;">{{ members.name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <hr>

        <div class="row" v-if="dataShowTask.showTaskPlan">
            <div class="col">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <span><b>Task (Plan):</b></span>
                        <div class="card-tools">
                            <Button label="New Task" icon="pi pi-plus" class="p-button-secondary p-button-sm"  @click="newTaskPlan"/>
                            <!-- <Button label="Add Weight" icon="pi pi-plus" class="p-button-success p-button-sm"  @click="addWeightTaskPlan" style="margin-left: 5px;"/> -->
                            <Button label="Close" icon="pi pi-times" class="p-button-sm"  @click="closeTaskPlan" style="margin-left: 5px;"/>
                        </div>
                    </div>
                    <div class="card-body p-0 ml-2 mr-2">
                        <DataTable :value="dataTaskPlan.task_plan" :lazy="true" :rows="totalData" ref="dt" :loading="loading" 
                            responsiveLayout="scroll" v-model:expandedRows="expandedRows">
                            <Column expander style="width: 2rem" />
                            <Column field="" header="No">
                              <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                              </template>
                            </Column>
                            <!-- <Column field="id" header="Id"></Column> -->
                            <Column field="type" header="Type"></Column>
                            <Column field="task" header="Task"></Column>
                            <Column field="description" header="Description"></Column>
                            <Column field="" header="Start Month">
                                <template #body="slotProps">
                                    {{ getMonth(slotProps.data.start_month) }}
                                </template>
                            </Column>
                            <Column field="" header="End Month">
                                <template #body="slotProps">
                                    {{ getMonth(slotProps.data.end_month) }}
                                </template>
                            </Column>
                            <Column field="tot_weight_plan" header="Weight">
                            </Column>
                            <Column :exportable="false" header="Action">
                                <template #body="slotProps">
                                    <Button @click="onEditTaskPlan(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" />
                                    <Button @click="addWeightTaskPlan" icon="pi pi-plus" class="p-button-rounded p-button-success" style="margin-left: 5px;" />
                                </template>
                            </Column>
                            <template #empty>
                                No records found
                            </template>
                            <template #expansion="slotProps">
                                <div class="p-3">
                                    <h6>Weight for {{ slotProps.data.task }}</h6>
                                    <DataTable :value="slotProps.data.weight_task_plans">
                                        <!-- <Column field="id" header="Id"></Column>
                                        <Column field="id_task_plan" header="Id Task Plan"></Column> -->
                                        <Column field="month_plan" header="Month"></Column>
                                        <Column field="weight_plan" header="Weight"></Column>
                                        <Column :exportable="false" header="Action">
                                            <template #body="WeigthPlanProps">
                                                <Button @click="editWeightTaskPlan(WeigthPlanProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" />
                                            </template>
                                        </Column>
                                        <template #empty>
                                            No records found
                                        </template>
                                    </DataTable>
                                </div>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="dataShowTask.showTaskRealization">
            <div class="col">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <span><b>Task (Realization):</b></span>
                        <div class="card-tools">
                            <Button label="New Task" icon="pi pi-plus" class="p-button-secondary p-button-sm"  @click="newTaskRealization"/>
                            <Button label="Close" icon="pi pi-times" class="p-button-sm"  @click="closeTaskRealization" style="margin-left: 5px;"/>
                        </div>
                    </div>
                    <div class="card-body p-0 ml-2 mr-2">
                        <DataTable :value="dataTaskRealization.task_realization" :lazy="true" :rows="totalData" ref="dt" :loading="loading" responsiveLayout="scroll" >
                            <Column field="" header="No">
                              <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                              </template>
                            </Column>
                            <Column field="id_task_plan" header="Task" hidden></Column>
                            <Column field="name_member" header="From"></Column>
                            <Column field="type" header="Type"></Column>
                            <Column field="task_name" header="Task"></Column>
                            <Column field="month" header="Month"></Column>
                            <Column field="weight" header="Weight"></Column>
                            <Column :exportable="false" header="Action">
                                <template #body="slotProps">
                                    <Button @click="onEditTaskRealizations(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" v-if="user && user.name === slotProps.data.name_member" />
                                </template>
                            </Column>
                            <template #empty>
                                No records found
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <Toolbar class="p-mb-4">
                    <template #right>
                        <span>
                            <!-- <InputText placeholder="Search..." v-model="search" style="font-size: 13px;" />
                            <Button icon="pi pi-search" iconPos="right"  class="p-button-sm"  @click="onSearch"/> -->
                            <Button label="Task (Plan)" class="p-button-secondary p-button-sm"  @click="showTaskPlan" style="margin-left: 5px;"/>
                            <Button label="Task (Realization)" class="p-button-secondary p-button-sm"  @click="showTaskRealization" style="margin-left: 5px;"/>
                        </span>
                    </template>
                </Toolbar>
                <DataTable :value="task" showGridlines :lazy="true" :rows="totalData" ref="dt" :loading="loading" responsiveLayout="scroll" rowGroupMode="rowspan" groupRowsBy="task">
                    <!-- <Column header="No">
                        <template #body="slotProps">
                            {{ ((lazyParams.page - 1) * dataPerPage) + slotProps.index + 1 }}
                        </template>
                    </Column> -->
                    <ColumnGroup type="header">
                        <Row>
                            <Column header="Task Description" :rowspan="2" />
                            <Column header="Type" :rowspan="2" />
                            <Column header="Duration (month)" :rowspan="2" />
                            <Column header="Weight" :rowspan="2" />
                            <Column header="Progress" :colspan="2" headerStyle="text-align:center"/>
                            <Column header="JAN" :rowspan="2" />
                            <Column header="FEB" :rowspan="2" />
                            <Column header="MAR" :rowspan="2" />
                            <Column header="APR" :rowspan="2" />
                            <Column header="MAY" :rowspan="2" />
                            <Column header="JUN" :rowspan="2" />
                            <Column header="JUL" :rowspan="2" />
                            <Column header="AUG" :rowspan="2" />
                            <Column header="SEP" :rowspan="2" />
                            <Column header="OCT" :rowspan="2" />
                            <Column header="NOV" :rowspan="2" />
                            <Column header="DEC" :rowspan="2" />
                        </Row>
                        <Row>
                            <Column header="Weight" />
                            <Column header="Percentage" />
                        </Row>
                    </ColumnGroup>
                    <Column field="task" ></Column>
                    <Column field="flag"></Column>
                    <Column>
                        <template #body="slotProps">
                            {{ slotProps.data.flag === 'Realization' ? '' : slotProps.data.duration }}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{ slotProps.data.flag === 'Realization' ? '' : slotProps.data.tot_weight_plan }}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{ slotProps.data.flag === 'Plan' ? '' : slotProps.data.tot_weight_realization }}
                        </template>
                    </Column>
                    <Column>
                        <template  #body="slotProps">
                            {{ slotProps.data.flag === 'Plan' ? '' : slotProps.data.percentage !== null ? slotProps.data.percentage + '%' : '' }}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.jan > 0 ? slotProps.data.jan : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.feb > 0 ? slotProps.data.feb : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.mar > 0 ? slotProps.data.mar : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.apr > 0 ? slotProps.data.apr : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.may > 0 ? slotProps.data.may : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.jun > 0 ? slotProps.data.jun : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.jul > 0 ? slotProps.data.jul : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.aug > 0 ? slotProps.data.aug : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.sep > 0 ? slotProps.data.sep : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.oct > 0 ? slotProps.data.oct : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.nov > 0 ? slotProps.data.nov : null}}
                        </template>
                    </Column>
                    <Column>
                        <template #body="slotProps">
                            {{slotProps.data.dec > 0 ? slotProps.data.dec : null}}
                        </template>
                    </Column>
                    <!-- <Column :exportable="false" header="Action">
                        <template #body="slotProps">
                            <Button @click="onEdit(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" style="margin-right: 10px;"/>
                            <Button @click="onDelete(slotProps.data)" icon="pi pi-trash" class="p-button-rounded p-button-info" />
                        </template>
                    </Column> -->
                    <ColumnGroup type="footer">
                        <Row>
                            <Column footer="Monthly Plan:" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="jan" />
                            <Column :footer="feb" />
                            <Column :footer="mar" />
                            <Column :footer="apr" />
                            <Column :footer="may" />
                            <Column :footer="jun" />
                            <Column :footer="jul" />
                            <Column :footer="aug" />
                            <Column :footer="sep" />
                            <Column :footer="oct" />
                            <Column :footer="nov" />
                            <Column :footer="dec" />
                        </Row>
                        <Row>
                            <Column footer="Monthly Realization:" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="jan" />
                            <Column :footer="feb" />
                            <Column :footer="mar" />
                            <Column :footer="apr" />
                            <Column :footer="may" />
                            <Column :footer="jun" />
                            <Column :footer="jul" />
                            <Column :footer="aug" />
                            <Column :footer="sep" />
                            <Column :footer="oct" />
                            <Column :footer="nov" />
                            <Column :footer="dec" />
                        </Row>
                        <Row>
                            <Column footer="Deviation:" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="jan" />
                            <Column :footer="feb" />
                            <Column :footer="mar" />
                            <Column :footer="apr" />
                            <Column :footer="may" />
                            <Column :footer="jun" />
                            <Column :footer="jul" />
                            <Column :footer="aug" />
                            <Column :footer="sep" />
                            <Column :footer="oct" />
                            <Column :footer="nov" />
                            <Column :footer="dec" />
                        </Row>
                    </ColumnGroup>
                    <template #empty>
                        No records found
                    </template>
                </DataTable>
            </div>
        </div>
      </form>
    </layout>
  </template>
  
  <script>
  import Layout from "../../Partials/Layout";
  import { usePage } from "@inertiajs/inertia-vue3";
  import { computed, reactive, ref} from "vue";
  import { storeTaskPlans, listTaskPlans, updateTaskPlan,  storeWeightTaskPlans, listWeightTaskPlans, updateWeightTaskPlan, storeTaskRealizations, listTaskRealizations, updateTaskRealization } from '../../Api/workplans.api.js';
  
  export default {
    components: {
        Layout,
    },

    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        const { userType, members } = usePage().props.value;
        const data = usePage().props.value.formData;
        const task_plans = usePage().props.value.task_plans;
        const weight_task_plans = usePage().props.value.weight_task_plans;
        const task_realizations = usePage().props.value.task_realizations;
        const task = usePage().props.value.task;
        // const weight_monthly = usePage().props.value.weight_monthly;
        const formData = reactive({
            id: data.id,
            name: data.name,
            project_type: data.project_type,
            team_leader: data.team_leader,
            team_members: data.team_members,
            year: data.year,
            status: data.status,
            description: data.description,
        });

        const getMonth = (date) => {
            if (date) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const jsDate = new Date(date);
            const month = monthNames[jsDate.getMonth()];
            return month;
            }
            return '';
        };

        const dataShowTask = ref({
            showTaskPlan: false,
            showTaskRealization: false,
        });

        const showTaskPlan = () => {
            dataShowTask.value.showTaskPlan = true;
            dataShowTask.value.showTaskRealization = false;
        };

        const closeTaskPlan = () => {
            dataShowTask.value.showTaskPlan = false;
        };

        const showTaskRealization = () => {
            dataShowTask.value.showTaskRealization = true;
            dataShowTask.value.showTaskPlan = false;
        };

        const closeTaskRealization = () => {
            dataShowTask.value.showTaskRealization = false;
        };

        const formTaskPlan = reactive({
            id: "",
            name_member: user.value.name,
            type: "",
            task: "",
            description: "",
            start_month: "",
            end_month: "",
            tot_weight_plan: ""
        });

        const type = ref(['Plan', 'Realization']);

        var dataTaskPlan = reactive({
            task_plan : [],
            display: false
        });

        dataTaskPlan.task_plan=task_plans;

        const newTaskPlan = () => {
            dataTaskPlan.display = true;
            formTaskPlan.id = '';
            formTaskPlan.type = '';
            formTaskPlan.task = '';
            formTaskPlan.description = '';
            formTaskPlan.start_month = '';
            formTaskPlan.end_month = '';
            formTaskPlan.tot_weight_plan = ''
        };

        async function loadLazyTaskPlan(formTaskPlan) {
            var params = {id_projects:formData.id};
            const result = await listTaskPlans( formTaskPlan );
            dataTaskPlan.task_plan = result.data.data;
        };

        async function simpanTaskPlan() {
            formTaskPlan.id_projects = formData.id;
            try {
                dataTaskPlan.display = false;  
                if (formTaskPlan.id) {
                    await updateTaskPlan(formTaskPlan); 
                    alert("Data Updated Successfully!");
                } else {
                    const response = await storeTaskPlans(formTaskPlan);
                    alert("Data Saved Successfully!");
                }
                loadLazyTaskPlan(formTaskPlan);
            } catch (error) {
                alert("Data Failed to Save!");
            }
        };

        const onEditTaskPlan = (item) => {
            Object.assign(formTaskPlan, item);
            dataTaskPlan.display = true;s
        };

        const expandedRows = ref([]);

        const formWeightTaskPlan = reactive({
            id: "",
            id_task_plan: "",
            month_plan: "",
            weight_plan: "",
        });

        var dataWeightTaskPlan = reactive({
            weight_task_plan : [],
            display: false
        });

        dataWeightTaskPlan.weight_task_plan=weight_task_plans;

        const addWeightTaskPlan = () => {
            dataWeightTaskPlan.display = true;
            formWeightTaskPlan.id = '';
            formWeightTaskPlan.id_task_plan = '';
            formWeightTaskPlan.month_plan = '';
            formWeightTaskPlan.weight_plan = '';
        };

        async function loadLazyWeightTaskPlan(formWeightTaskPlan) {
            var params = {id_projects:formData.id};
            const result = await listWeightTaskPlans( formWeightTaskPlan );
            dataWeightTaskPlan.weight_task_plan = result.data.data;
        };

        async function simpanWeightTaskPlan() {
            formWeightTaskPlan.id_projects = formData.id;
            try {
                dataWeightTaskPlan.display = false;  
                if (formWeightTaskPlan.id) {
                    await updateWeightTaskPlan(formWeightTaskPlan); 
                    alert("Data Updated Successfully!");
                } else {
                    const response = await storeWeightTaskPlans(formWeightTaskPlan);
                    alert("Data Saved Successfully!");
                }
                loadLazyWeightTaskPlan(formWeightTaskPlan);
            } catch (error) {
                alert("Data Failed to Save!");
            }
        };

        const editWeightTaskPlan = (item) => {
            item.id_task_plan = parseInt(item.id_task_plan);
            Object.assign(formWeightTaskPlan, item);
            dataWeightTaskPlan.display = true;
        };

        const formTaskRealization = reactive({
            id: "",
            id_task_plan: "",
            name_member: user.value.name,
            type: "",
            task_name: "",
            month: "",
            weight: "",
        });

        var dataTaskRealization = reactive({
            task_realization : [],
            display: false
        });

        dataTaskRealization.task_realization=task_realizations;

        const newTaskRealization = () => {
            dataTaskRealization.display = true;
            formTaskRealization.id = '';
            formTaskRealization.id_task_plan = '';
            formTaskRealization.name_member = user.value.name;
            formTaskRealization.type = '';
            formTaskRealization.month = '';
            formTaskRealization.weight = '';
        };

        async function loadLazyTaskRealization(formTaskRealization) {
            var params = {id_projects:formData.id};
            const result = await listTaskRealizations( formTaskRealization );
            dataTaskRealization.task_realization = result.data.data;
        };

        async function simpanTaskRealization() {
            formTaskRealization.id_projects = formData.id;
            try {
                dataTaskRealization.display = false;  
                if (formTaskRealization.id) {
                    await updateTaskRealization(formTaskRealization); 
                    alert("Data Updated Successfully!");
                } else {
                    const response = await storeTaskRealizations(formTaskRealization);
                    alert("Data Saved Successfully!");
                }
                loadLazyTaskRealization(formTaskRealization);
            } catch (error) {
                alert("Data Failed to Save!");
            }
        };

        const onEditTaskRealizations = (item) => {
            item.id_task_plan = parseInt(item.id_task_plan);
            Object.assign(formTaskRealization, item);
            // console.log(formTaskRealization, item, tasks)
            dataTaskRealization.display = true;
        };
        
        const month = ref(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']);
        
        const filteredMonths = computed(() => {
            const { start_date, end_date } = formData;
            if (start_date && end_date) {
            const startMonth = new Date(start_date).getMonth();
            const endMonth = new Date(end_date).getMonth();

            // Filter bulan 
            return month.value.filter((_, index) => index >= startMonth && index <= endMonth);
            } else {
            return month.value;
            }
        });

        return {
            // View Workplan
            user,
            userType,
            members,
            task,
            // weight_monthly,
            formData,
            getMonth,

            // Show & Close Task 
            dataShowTask,
            showTaskPlan,
            closeTaskPlan,
            showTaskRealization,
            closeTaskRealization,

            // Task (plan)
            formTaskPlan,
            type,
            task_plans,
            dataTaskPlan,
            newTaskPlan,
            loadLazyTaskPlan,
            simpanTaskPlan,
            onEditTaskPlan,
            expandedRows,
            // Weight
            formWeightTaskPlan,
            weight_task_plans,
            dataWeightTaskPlan,
            addWeightTaskPlan,
            loadLazyWeightTaskPlan,
            simpanWeightTaskPlan,
            editWeightTaskPlan,
            // Task (realization)
            formTaskRealization,
            task_realizations,
            dataTaskRealization,
            newTaskRealization,
            loadLazyTaskRealization,
            simpanTaskRealization,
            onEditTaskRealizations,

            // All Aask
            month,
            filteredMonths
        };
    },
  };
  </script>