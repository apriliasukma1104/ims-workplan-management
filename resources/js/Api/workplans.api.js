import httpClient from './httpClient';

const pageListWorkplans = (params) => httpClient.get('/workplans/list_workplans', {params});
const updateWorkplan = (params) => httpClient.post('/workplans/list/update_workplan', params);
const updateStatusProject = (params) => httpClient.post('/workplans/list/update_status_project', params);

// Task Plans
const storeTaskPlans = (params) => httpClient.post('/workplans/list/view_workplan/store_task_plans', params);
const listTaskPlans = (params) => httpClient.get('/workplans/list/view_workplan/list_task_plans', {params});
const pageListTaskPlans = (params) => httpClient.get('/task_plans', {params});
const updateTaskPlan = (params) => httpClient.post('/workplans/list/view_workplan/update_task_plan', params);
// Weight
const storeWeightTaskPlans = (params) => httpClient.post('/workplans/list/view_workplan/store_weight_task_plans', params);
const listWeightTaskPlans = (params) => httpClient.get('/workplans/list/view_workplan/list_weight_task_plans', {params});
const pageListWeightTaskPlans = (params) => httpClient.get('/weight_task_plans', {params});
const updateWeightTaskPlan = (params) => httpClient.post('/workplans/list/view_workplan/update_weight_task_plan', params);

// Task Realizations
const storeTaskRealizations = (params) => httpClient.post('/workplans/list/view_workplan/store_task_realizations', params);
const listTaskRealizations = (params) => httpClient.get('/workplans/list/view_workplan/list_task_realizations', {params});
const pageListTaskRealizations = (params) => httpClient.get('/task_realizations', {params});
const updateTaskRealization = (params) => httpClient.post('/workplans/list/view_workplan/update_task_realization', params);

export {
    pageListWorkplans,
    updateWorkplan,
    updateStatusProject,

    // Task Plans
    storeTaskPlans,
    listTaskPlans,
    pageListTaskPlans,
    updateTaskPlan,
    // Weight
    storeWeightTaskPlans,
    listWeightTaskPlans,
    pageListWeightTaskPlans,
    updateWeightTaskPlan,

    // Task Realizations
    storeTaskRealizations,
    listTaskRealizations,
    pageListTaskRealizations,
    updateTaskRealization
}