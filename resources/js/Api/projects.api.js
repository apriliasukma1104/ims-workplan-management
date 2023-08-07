import httpClient from './httpClient';

const storeProjects = (params) => httpClient.post('/projects/add/store_projects', params);
const deleteProjects = (params) => httpClient.post('/projects/list/delete_projects', params);
const updateProjects = (params) => httpClient.post('/projects/list/update_projects', params);
const storeTasks = (params) => httpClient.post('/projects/list/view_projects/store_tasks', params);

export {
    storeProjects,
    deleteProjects,
    updateProjects,
    storeTasks
}