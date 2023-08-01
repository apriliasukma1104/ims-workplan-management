import httpClient from './httpClient';
import httpClientMultipart from './httpClientMultipart';

const storeMembers = (params) => httpClientMultipart.post('/members/add/store_members', params);
const deleteMembers = (params) => httpClient.post('/members/list/delete_members', params);
const updateMembers = (params) => httpClient.post('/members/list/update_members', params);

export {
    storeMembers,
    deleteMembers,
    updateMembers
}