import httpClient from './httpClient';

const storeProjects = (params) => httpClient.post('/projects/add/store_projects', params);
const deleteProject = (params) => httpClient.post('/projects/list/delete_project', params);
const updateProject = (params) => httpClient.post('/projects/list/update_project', params);
const storeTasks = (params) => httpClient.post('/projects/list/view_project/store_tasks', params);
const listTasks = (params) => httpClient.get('/projects/list/view_project/list_tasks', {params});
const updateTask = (params) => httpClient.post('/projects/list/view_project/update_task', params);
const deleteTask = (params) => httpClient.post('/projects/list/view_project/delete_task', params);

export {
    storeProjects,
    deleteProject,
    updateProject,
    storeTasks,
    listTasks,
    updateTask,
    deleteTask
}