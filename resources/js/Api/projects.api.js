import httpClient from './httpClient';

const storeProjects = (params) => httpClient.post('/projects/add/store_projects', params);
const getTeamLeaderName = (params) => httpClient.post('/projects/list/team_leader_name', params);
const deleteProjects = (params) => httpClient.post('/projects/list/delete_projects', params);
const updateProjects = (params) => httpClient.post('/projects/list/update_projects', params);

export {
    storeProjects,
    getTeamLeaderName,
    deleteProjects,
    updateProjects
}