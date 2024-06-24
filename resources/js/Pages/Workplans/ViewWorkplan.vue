<template>
    <layout title="View Work Plan">
    <Toast position="top-center" />
        
      <!-- Dialog Task Plan -->
      <Dialog header="Header" v-model:visible="dataTaskPlan.display" :style="{ width: '40vw' }" >
        <template #header>
            <label>Task (Plan) in {{ formData.year }}</label>
        </template>
        <div class="p-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="p-field">
                            <InputText v-model="formTaskPlan.id" type="text" hidden />
                            <InputText v-model="formTaskPlan.type" type="text" hidden />
                            <label for="task">Task<span style="color:red;" :class="error && error.task ? 'p-invalid' : ''">*</span></label>
                            <InputText v-model="formTaskPlan.task" type="text" />
                        </div>
                        <div class="p-field">
                            <label for="description">Description<span style="color:red;">*</span></label>
                            <Area v-model="formTaskPlan.description"  class="form-control" style="height: 115px;"></Area>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="p-field">
                            <label for="start_month">Start Month<span style="color:red;">*</span></label> 
                            <Dropdown v-model="formTaskPlan.start_month" :options="months" optionLabel="label" optionValue="value" placeholder="Select a month" /> 
                        </div>
                        <div class="p-field">
                            <label for="end_month">End Month<span style="color:red;">*</span></label>  
                            <Dropdown v-model="formTaskPlan.end_month" :options="months" optionLabel="label" optionValue="value" placeholder="Select a month" /> 
                        </div>
                        <div class="p-field">
                            <label for="tot_weight_plan">Weight<span style="color:red;">* </span><span style="font-weight: normal;"> 
                                (Current weight: {{ _replaceDotWithComma(calculateWeight) }}/100)</span></label>
                            <InputText v-model="formTaskPlan.tot_weight_plan" placeholder="00.00" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <template #footer>
            <Button @click="simpanTaskPlan" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
        </template>
      </Dialog>

      <!-- Dialog Task Realization -->
      <Dialog header="Header" v-model:visible="dataTaskRealization.display" :style="{ width: '25vw' }" >
        <template #header>
                <label>Task (Realization) in {{ formData.year }}</label>
                </template>
                <div class="p-fluid">
                    <div class="form-group">
                                <div class="p-field">
                                    <InputText v-model="formTaskRealization.id" type="text" hidden/>
                                    <InputText v-model="formTaskRealization.type" type="text" hidden />
                                </div>
                                <div class="p-field" v-if="!formTaskRealization.id">
                                    <label for="task">Task<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskRealization.id_task_plans" :options="filter_tp" optionLabel="task" optionValue="id" placeholder="Select a Task" />
                                </div>
                                <div class="p-field" hidden>
                                    <label for="month">Month<span style="color:red;">*</span></label>
                                    <Dropdown v-model="formTaskRealization.month" :options="months" optionLabel="label" optionValue="value" placeholder="Select a month" /> 
                                </div>
                                <div class="p-field">
                                    <label for="weight">Weight<span style="color:red;">*</span></label>
                                    <InputText v-model="formTaskRealization.weight" placeholder="00.00" />
                                </div>
                            </div>
                </div>
                <template #footer>
                    <Button @click="simpanTaskRealization" label="Save" icon="pi pi-check" autofocus class="p-button-sm" />
                </template>
      </Dialog>
    
      <!-- Detail View Work Plans -->
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
                <div class="form-group mt-3 mr-3">
                    <label class="control-label">Budget</label>
                    <div>{{ _formatRupiah(formData._budget) }}</div>
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
                        <span><b>Task (Plan) in {{ formData.year }}:</b></span>
                        <div class="card-tools">
                            <Button @click="newTaskPlan" v-if="!parseInt(formData.launched)" label="New Task" icon="pi pi-plus" class="p-button-secondary p-button-sm" />
                            <Button @click="launchTaskPlan" v-if="totalWeightTP == 100 && !parseInt(formData.launched) && user.name === formData.team_leader.name" 
                                label="Launch" icon="fas fa-rocket" class="p-button-success p-button-sm" style="margin-left: 5px;" />
                            <Button label="Close" icon="pi pi-times" class="p-button-sm"  @click="closeTaskPlan" style="margin-left: 5px;" />
                        </div>
                    </div>
                    <div class="card-body p-0 ml-2 mr-2">
                        <DataTable :value="dataTaskPlan.task_plan" :lazy="true" :rows="totalData" ref="dt" :loading="loading" 
                            responsiveLayout="scroll" v-model:expandedRows="expandedRows">
                            <Column expander style="width: 2rem" />
                            <Column header="No">
                              <template #body="slotProps">
                                {{ slotProps.index + 1 }}
                              </template>
                            </Column>
                            <Column field="name_member" header="From"></Column>
                            <Column field="task" header="Task"></Column>
                            <Column field="description" header="Description"></Column>
                            <Column header="Start Month">
                                <template #body="slotProps">
                                    {{ slotProps.data.start_month }}
                                </template>
                            </Column>
                            <Column header="End Month">
                                <template #body="slotProps">
                                    {{ slotProps.data.end_month }}
                                </template>
                            </Column>
                            <Column header="Weight">
                                <template #body="slotProps">
                                    {{ _replaceDotWithComma(slotProps.data.tot_weight_plan) }}
                                </template>
                            </Column>
                            <Column :exportable="false" header="Action" v-if="!parseInt(formData.launched)">
                                <template #body="slotProps">
                                    <Button @click="onEditTaskPlan(slotProps.data)" icon="pi pi-pencil" class="p-button-rounded p-button-primary" :title="'Edit'" 
                                        v-if="user.name === slotProps.data.name_member" />
                                </template>
                            </Column>
                            <template #empty>
                                No records found
                            </template>
                            <template #expansion="slotProps">
                                <div class="p-3">
                                    <h6>Weight for {{ slotProps.data.task }}</h6>
                                    <DataTable :value="slotProps.data.weight_task_plans">
                                        <Column field="month_plan" header="Month"></Column>
                                        <Column header="Weight">
                                            <template #body="slotProps">
                                                {{ _replaceDotWithComma(slotProps.data.weight_plan) }}
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
                        <span><b>Task (Realization) in {{ formData.year }}:</b></span>
                        <div class="card-tools">
                            <Button label="Select Task" icon="pi pi-plus" class="p-button-secondary p-button-sm"  @click="newTaskRealization"/>
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
                            <Column field="name_member" header="From"></Column>
                            <Column field="task_name" header="Task"></Column>
                            <Column field="month" header="Month"></Column>
                            <Column header="Weight">
                                <template #body="slotProps">
                                    {{ _replaceDotWithComma(slotProps.data.weight) }}
                                </template>
                            </Column>
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
                            <Button label="Task (Plan)" class="p-button-secondary p-button-sm"  @click="showTaskPlan" style="margin-left: 5px;" 
                                v-if="user.name === formData.team_leader.name || formData.team_members.some(member => member.name === user.name)" />
                            <Button label="Task (Realization)" class="p-button-secondary p-button-sm"  @click="showTaskRealization" style="margin-left: 5px;" 
                                v-if="(user.name === formData.team_leader.name || formData.team_members.some(member => member.name === user.name)) && today >= firstDay && today <= secondDay"/>
                        </span>
                    </template>
                </Toolbar>
                <DataTable :value="task" showGridlines :lazy="true" :rows="totalData" ref="dt" :loading="loading" responsiveLayout="scroll" rowGroupMode="rowspan" groupRowsBy="task">
                    <ColumnGroup type="header">
                        <Row>
                            <Column header="Task" :rowspan="2" />
                            <Column header="Type" :rowspan="2" />
                            <Column header="Duration (month)" :rowspan="2" style="padding: 5px; text-align: center;" />
                            <Column header="Weight" :rowspan="2" style="padding: 5px; text-align: center;" />
                            <Column header="Progress" :colspan="2" style="padding: 5px;" />
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
                            <Column header="Weight" style="padding: 5px; text-align: center;" />
                            <Column header="Percentage" style="padding: 5px; text-align: center;" />
                        </Row>
                    </ColumnGroup>
                    <Column field="task" style="padding: 5px; text-align: center;"></Column>
                    <Column field="type" style="padding: 5px; text-align: center;"></Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.type === 'Realization' ? '' : slotProps.data.duration }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.type === 'Realization' ? '' :  _replaceDotWithComma(slotProps.data.tot_weight_plan) }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.type === 'Plan' ? '' : slotProps.data.tot_weight_realization !== null ?  _replaceDotWithComma(slotProps.data.tot_weight_realization) : '0' }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template  #body="slotProps">
                            {{ slotProps.data.type === 'Plan' ? '' : slotProps.data.percentage !== null ?  _replaceDotWithComma(slotProps.data.percentage) + '%' : '0' + '%' }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.jan > 0 ? _replaceDotWithComma(slotProps.data.jan) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.feb > 0 ? _replaceDotWithComma(slotProps.data.feb) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.mar > 0 ? _replaceDotWithComma(slotProps.data.mar) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.apr > 0 ? _replaceDotWithComma(slotProps.data.apr) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.may > 0 ? _replaceDotWithComma(slotProps.data.may) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.jun > 0 ? _replaceDotWithComma(slotProps.data.jun) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.jul > 0 ? _replaceDotWithComma(slotProps.data.jul) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.aug > 0 ? _replaceDotWithComma(slotProps.data.aug) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.sep > 0 ? _replaceDotWithComma(slotProps.data.sep) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.oct > 0 ? _replaceDotWithComma(slotProps.data.oct) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.nov > 0 ? _replaceDotWithComma(slotProps.data.nov) : null }}
                        </template>
                    </Column>
                    <Column style="text-align: center;">
                        <template #body="slotProps">
                            {{ slotProps.data.dec > 0 ? _replaceDotWithComma(slotProps.data.dec) : null }}
                        </template>
                    </Column>
                    <ColumnGroup type="footer">
                        <Row>
                            <Column footer="Plan (Monthly):" :colspan="6" footerStyle="text-align:right" />
                            <Column :footer="vw_weight_monthly[0]?.jan != null ? _replaceDotWithComma(vw_weight_monthly[0].jan) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.feb != null ? _replaceDotWithComma(vw_weight_monthly[0].feb) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.mar != null ? _replaceDotWithComma(vw_weight_monthly[0].mar) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.apr != null ? _replaceDotWithComma(vw_weight_monthly[0].apr) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.may != null ? _replaceDotWithComma(vw_weight_monthly[0].may) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.jun != null ? _replaceDotWithComma(vw_weight_monthly[0].jun) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.jul != null ? _replaceDotWithComma(vw_weight_monthly[0].jul) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.aug != null ? _replaceDotWithComma(vw_weight_monthly[0].aug) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.sep != null ? _replaceDotWithComma(vw_weight_monthly[0].sep) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.oct != null ? _replaceDotWithComma(vw_weight_monthly[0].oct) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.nov != null ? _replaceDotWithComma(vw_weight_monthly[0].nov) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[0]?.dec != null ? _replaceDotWithComma(vw_weight_monthly[0].dec) : null" style="text-align: center;" />
                        </Row>
                        <Row>
                            <Column footer="Realization (Monthly):" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="vw_weight_monthly[1]?.jan != null ? _replaceDotWithComma(vw_weight_monthly[1].jan) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.feb != null ? _replaceDotWithComma(vw_weight_monthly[1].feb) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.mar != null ? _replaceDotWithComma(vw_weight_monthly[1].mar) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.apr != null ? _replaceDotWithComma(vw_weight_monthly[1].apr) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.may != null ? _replaceDotWithComma(vw_weight_monthly[1].may) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.jun != null ? _replaceDotWithComma(vw_weight_monthly[1].jun) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.jul != null ? _replaceDotWithComma(vw_weight_monthly[1].jul) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.aug != null ? _replaceDotWithComma(vw_weight_monthly[1].aug) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.sep != null ? _replaceDotWithComma(vw_weight_monthly[1].sep) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.oct != null ? _replaceDotWithComma(vw_weight_monthly[1].oct) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.nov != null ? _replaceDotWithComma(vw_weight_monthly[1].nov) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_monthly[1]?.dec != null ? _replaceDotWithComma(vw_weight_monthly[1].dec) : null" style="text-align: center;" />
                        </Row>
                        <Row>
                            <Column footer="Plan (Cumulative):" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="vw_weight_cumulative[0]?.jan_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].jan_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.feb_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].feb_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.mar_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].mar_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.apr_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].apr_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.may_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].may_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.jun_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].jun_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.jul_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].jul_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.aug_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].aug_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.sep_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].sep_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.oct_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].oct_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.nov_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].nov_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[0]?.dec_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[0].dec_cumulative) : null" style="text-align: center;" />
                        </Row>
                        <Row>
                            <Column footer="Realization (Cumulative):" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="vw_weight_cumulative[1]?.jan_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].jan_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.feb_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].feb_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.mar_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].mar_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.apr_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].apr_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.may_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].may_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.jun_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].jun_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.jul_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].jul_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.aug_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].aug_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.sep_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].sep_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.oct_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].oct_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.nov_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].nov_cumulative) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_cumulative[1]?.dec_cumulative != null ? _replaceDotWithComma(vw_weight_cumulative[1].dec_cumulative) : null" style="text-align: center;" />
                        </Row>
                        <Row>
                            <Column footer="Gap:" :colspan="6" footerStyle="text-align:right"/>
                            <Column :footer="vw_weight_gap[0]?.jan_gap != null ? _replaceDotWithComma(vw_weight_gap[0].jan_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.feb_gap != null ? _replaceDotWithComma(vw_weight_gap[0].feb_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.mar_gap != null ? _replaceDotWithComma(vw_weight_gap[0].mar_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.apr_gap != null ? _replaceDotWithComma(vw_weight_gap[0].apr_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.may_gap != null ? _replaceDotWithComma(vw_weight_gap[0].may_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.jun_gap != null ? _replaceDotWithComma(vw_weight_gap[0].jun_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.jul_gap != null ? _replaceDotWithComma(vw_weight_gap[0].jul_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.aug_gap != null ? _replaceDotWithComma(vw_weight_gap[0].aug_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.sep_gap != null ? _replaceDotWithComma(vw_weight_gap[0].sep_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.oct_gap != null ? _replaceDotWithComma(vw_weight_gap[0].oct_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.nov_gap != null ? _replaceDotWithComma(vw_weight_gap[0].nov_gap) : null" style="text-align: center;" />
                            <Column :footer="vw_weight_gap[0]?.dec_gap != null ? _replaceDotWithComma(vw_weight_gap[0].dec_gap) : null" style="text-align: center;" />
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
  import { useToast } from 'primevue/usetoast';
  import { useConfirm } from 'primevue/useconfirm';
  import { replaceDotWithComma, formatRupiah } from "../../utils/index.js";
  import { storeTaskPlans, listTaskPlans, updateTaskPlan,  storeWeightTaskPlans, listWeightTaskPlans, launchTask, 
    storeTaskRealizations, listTaskRealizations, updateTaskRealization, filterTaskPlan, updateStatusCompleted } from '../../Api/workplans.api.js';

  export default {
    components: {
        Layout,
    },

    setup() {
        const user = computed(() => usePage().props.value.auth.user);
        const { userType, members } = usePage().props.value;
        const data = usePage().props.value.formData;
        const task_plans = usePage().props.value.task_plans;
        const totalWeightTP = ref(usePage().props.value.totalWeightTP);
        const calculateWeight = ref(usePage().props.value.calculateWeight);
        const weight_task_plans = usePage().props.value.weight_task_plans;
        const task_realizations = usePage().props.value.task_realizations;
        const filter_tp = ref(usePage().props.value.filter_tp);
        const task = usePage().props.value.task;
        const vw_weight_monthly = usePage().props.value.vw_weight_monthly;
        const vw_weight_cumulative = usePage().props.value.vw_weight_cumulative;
        const vw_weight_gap = usePage().props.value.vw_weight_gap;

        const toast = useToast();
        const confirm = useConfirm();

        const formData = reactive({
            ...data
        });

        const _replaceDotWithComma = (val) => {
            return replaceDotWithComma(val);
        };

        const _formatRupiah = (val) => {
            return formatRupiah(val);
        };

        // Waktu Pelaporan (Open/Close Button Task Realization)
        const d = new Date();
        var today = d;
        console.log("Today:" + today);
        if (today.getDate() >= 25) {
            var firstDay = new Date(d.getFullYear(), d.getMonth(), 25);
            var secondDay = new Date(d.getFullYear(), d.getMonth() + 1, 6);
        } else if (today.getDate() <= 5) {
            var firstDay = new Date(d.getFullYear(), d.getMonth() - 1, 25);
            var secondDay = new Date(d.getFullYear(), d.getMonth(), 6);
        }

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

        var dataTaskPlan = reactive({
            task_plan : task_plans,
            display: false
        });

        const newTaskPlan = () => {
            dataTaskPlan.display = true;
            formTaskPlan.id = '';
            formTaskPlan.type = 'Plan';
            formTaskPlan.task = '';
            formTaskPlan.description = '';
            formTaskPlan.start_month = '';
            formTaskPlan.end_month = '';
            formTaskPlan.tot_weight_plan = ''
        };

        const launchTaskPlan = () => {
            confirm.require({
                message: "Are you sure? If you continue, the task plan will be locked and changes can't be made again.",
                header: 'Confirm Launch',
                icon: 'pi pi-exclamation-triangle',
                rejectClass: 'p-button-danger',
                acceptLabel: 'Yes',
                acceptClass: 'p-button-success',
                accept: async () => {
                    await launchTask({id:formData.id});
                    formData.launched = 1;
                }
            });
        };
        
        const months = [
            { label: 'January', value: 1 },
            { label: 'February', value: 2 },
            { label: 'March', value: 3 },
            { label: 'April', value: 4 },
            { label: 'May', value: 5 },
            { label: 'June', value: 6 },
            { label: 'July', value: 7 },
            { label: 'August', value: 8 },
            { label: 'September', value: 9 },
            { label: 'October', value: 10 },
            { label: 'November', value: 11 },
            { label: 'December', value: 12 }
        ];

        async function loadLazyTaskPlan(formTaskPlan) {
            var params = {id_projects:formData.id};
            const result = await listTaskPlans( formTaskPlan );

            let calculateWeightTask = 0;
            result.data.data.map((item)=>{
                calculateWeightTask+=parseInt(item.tot_weight_plan);
            })

            calculateWeight.value = calculateWeightTask;
            totalWeightTP.value = calculateWeightTask; 
            dataTaskPlan.task_plan = result.data.data;
        }; 

        async function simpanTaskPlan() {
            formTaskPlan.id_projects = formData.id;
            try {
                dataTaskPlan.display = false;
                let response;
                if (formTaskPlan.id) {
                    response = await updateTaskPlan(formTaskPlan);
                    toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Updated Successfully!', life: 3000 });
                    loadLazyTaskPlan(formTaskPlan);
                } else {
                    response = await storeTaskPlans(formTaskPlan);
                    formTaskPlan.id = response.data.id; 
                    toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Saved Successfully!', life: 3000 });
                    loadLazyTaskPlan(formTaskPlan);
                }
                
                var formWeightTaskPlan = [];
                if (response) {
                    const { start_month, end_month, tot_weight_plan } = formTaskPlan;
                    const duration = end_month - start_month + 1;
                    const weight_per_month = (tot_weight_plan / duration).toFixed(2);

                    const breakdown = [];
                    for (let i = start_month; i <= end_month; i++) {
                        const month = months.find(month => month.value === i).label;
                        breakdown.push({
                            month_plan: month,
                            weight_plan: weight_per_month
                        });
                    }
                    
                    // Simpan breakdown weight ke database
                    await Promise.all(breakdown.map(async (item) => {
                        formWeightTaskPlan.push({
                            id_projects: formData.id,
                            id_task_plans: formTaskPlan.id,
                            month_plan: item.month_plan,
                            weight_plan: item.weight_plan
                        });
                    }));
                    
                    await storeWeightTaskPlans({ id_task_plans:formTaskPlan.id, data : formWeightTaskPlan });
                    loadLazyTaskPlan(formTaskPlan);
                    loadLazyWeightTaskPlan(formWeightTaskPlan);
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    toast.add({ severity: 'error', summary: 'Error!', detail: error.response.data.error, life: 3000 });
                } else {
                    toast.add({ severity: 'error', summary: 'Error!', detail:'Data Failed to Save!', life: 3000 });
                }
            }
        };

        const onEditTaskPlan = (data) => {
            Object.assign(formTaskPlan, data);
            dataTaskPlan.display = true;

            formTaskPlan.start_month = months.find(month => month.label === formTaskPlan.start_month)?.value;
            formTaskPlan.end_month = months.find(month => month.label === formTaskPlan.end_month)?.value;
        };

        const expandedRows = ref([]);
        const formWeightTaskPlan = reactive({});

        async function loadLazyWeightTaskPlan(formWeightTaskPlan) {
            var params = {id_projects:formData.id};
            const result = await listWeightTaskPlans( formWeightTaskPlan );
        };

        const formTaskRealization = reactive({
            id: "",
            id_task_plans: "",
            name_member: user.value.name,
            type: "",
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
            formTaskRealization.id_task_plans = '';
            formTaskRealization.type = 'Realization';
            formTaskRealization.month = today.getMonth() + 1;
            formTaskRealization.weight = '';
        };

        async function loadLazyTaskRealization(formTaskRealization, formTaskPlan) {
            var params = {id_projects: formData.id};
            const result = await listTaskRealizations(formTaskRealization);
            dataTaskRealization.task_realization = result.data.data;
        };

        async function simpanTaskRealization() {
            formTaskRealization.id_projects = formData.id;
            try {
                dataTaskRealization.display = false;  
                if (formTaskRealization.id) {
                    await updateTaskRealization(formTaskRealization); 
                    toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Updated Successfully!', life: 3000 });
                } else {
                    const response = await storeTaskRealizations(formTaskRealization);
                    toast.add({ severity: 'success', summary: 'Information!', detail: 'Data Saved Successfully!', life: 3000 });
                }
                loadLazyTaskRealization(formTaskRealization);

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    toast.add({ severity: 'error', summary: 'Error!', detail: error.response.data.error, life: 3000 });
                } else {
                    toast.add({ severity: 'error', summary: 'Error!', detail:'Data Failed to Save!', life: 3000 });
                }
            }        
        // Setting Filter Task Plan
        const dataFilterTp = await filterTaskPlan({id_project : formData.id});
        filter_tp.value = dataFilterTp.data.data;

        // Setting Update Status => Completed
        await updateStatusCompleted(formData);
        };

        const onEditTaskRealizations = (data) => {
            data.id_task_plans = parseInt(data.id_task_plans);
            
            Object.assign(formTaskRealization, data);
            dataTaskRealization.display = true;

            formTaskRealization.month = months.find(month_ => month_.label === formTaskRealization.month)?.value;
        };

        return {
            // View Workplan
            user,
            userType,
            members,
            formData,
            _replaceDotWithComma,
            _formatRupiah,

            // Show & Close Task 
            firstDay,
            secondDay,
            today,
            dataShowTask,
            showTaskPlan,
            closeTaskPlan,
            showTaskRealization,
            closeTaskRealization,

            // Task (plan)
            task_plans,
            formTaskPlan,
            totalWeightTP,
            calculateWeight,
            dataTaskPlan,
            newTaskPlan,
            launchTaskPlan,
            months,
            loadLazyTaskPlan,
            simpanTaskPlan,
            onEditTaskPlan,
            expandedRows,
            formWeightTaskPlan,
            weight_task_plans,
            loadLazyWeightTaskPlan,

            // Task (realization)
            task_realizations,
            filter_tp,
            formTaskRealization,
            dataTaskRealization,
            newTaskRealization,
            loadLazyTaskRealization,
            simpanTaskRealization,
            onEditTaskRealizations,

            // All Task
            task,
            vw_weight_monthly,
            vw_weight_cumulative,
            vw_weight_gap
        };
    },
  };
  </script>