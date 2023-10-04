import httpClient from './httpClient';

const pageListProjects = (params) => httpClient.get('/projects/list_projects', {params});
const deleteProject = (params) => httpClient.post('/projects/list/delete_project', params);
const storeProject = (params) => httpClient.post('/projects/list/store_project', params);
const updateProject = (params) => httpClient.post('/projects/list/update_project', params);
const validation = (params) => httpClient.post('/projects/list/validation', params);
const storeTasks = (params) => httpClient.post('/projects/list/view_project/store_tasks', params);
const listTasks = (params) => httpClient.get('/projects/list/view_project/list_tasks', {params});
const pageListTasks = (params) => httpClient.get('/tasks', {params});
const updateTask = (params) => httpClient.post('/projects/list/view_project/update_task', params);
const deleteTask = (params) => httpClient.post('/projects/list/view_project/delete_task', params);

export {
    pageListProjects,
    deleteProject,
    storeProject,
    updateProject,
    validation,
    storeTasks,
    listTasks,
    pageListTasks,
    updateTask,
    deleteTask
}