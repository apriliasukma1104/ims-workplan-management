import httpClient from './httpClient';

// Page Work Plans
const pageListWorkplans = (params) => httpClient.get('/workplans/list_workplans', {params});
const updateWorkplan = (params) => httpClient.post('/workplans/list/update_workplan', params);
const deleteWorkplan = (params) => httpClient.post('/workplans/list/delete_workplan', params);
const updateStatusSubmitted = (params) => httpClient.post('/workplans/list/update_status_submitted', params);
const updateStatusCompleted = (params) => httpClient.post('/workplans/list/update_status_completed', params);

// Task Plans
const storeTaskPlans = (params) => httpClient.post('/workplans/list/view_workplan/store_task_plans', params);
const listTaskPlans = (params) => httpClient.get('/workplans/list/view_workplan/list_task_plans', {params});
const pageListTaskPlans = (params) => httpClient.get('/task_plans', {params});
const updateTaskPlan = (params) => httpClient.post('/workplans/list/view_workplan/update_task_plan', params);
const launchTask = (params) => httpClient.post('/workplans/launch_task', params);
// Weight
const storeWeightTaskPlans = (params) => httpClient.post('/workplans/list/view_workplan/store_weight_task_plans', params);
const listWeightTaskPlans = (params) => httpClient.get('/workplans/list/view_workplan/list_weight_task_plans', {params});
const pageListWeightTaskPlans = (params) => httpClient.get('/weight_task_plans', {params});

// Task Realizations
const storeTaskRealizations = (params) => httpClient.post('/workplans/list/view_workplan/store_task_realizations', params);
const listTaskRealizations = (params) => httpClient.get('/workplans/list/view_workplan/list_task_realizations', {params});
const pageListTaskRealizations = (params) => httpClient.get('/task_realizations', {params});
const updateTaskRealization = (params) => httpClient.post('/workplans/list/view_workplan/update_task_realization', params);
const filterTaskPlan = (params) => httpClient.get('/workplans/list/view_workplan/filter_tp', { params });

export {
    // Page Work Plans
    pageListWorkplans,
    updateWorkplan,
    deleteWorkplan,
    updateStatusSubmitted,
    updateStatusCompleted,

    // Task Plans
    storeTaskPlans,
    listTaskPlans,
    pageListTaskPlans,
    updateTaskPlan,
    launchTask,
    // Weight
    storeWeightTaskPlans,
    listWeightTaskPlans,
    pageListWeightTaskPlans,

    // Task Realizations
    storeTaskRealizations,
    listTaskRealizations,
    pageListTaskRealizations,
    updateTaskRealization,
    filterTaskPlan
}