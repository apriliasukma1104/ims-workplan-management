import httpClient from './httpClient';
// import httpClientMultipart from './httpClientMultipart';

const pageListMembers = (params) => httpClient.get('/members/list_members', {params});
// const storeMembers = (params) => httpClientMultipart.post('/members/add/store_members', params);
const deleteMember = (params) => httpClient.post('/members/list/delete_member', params);
const updateMember = (params) => httpClient.post('/members/list/update_member', params);
const manageMember = (params) => httpClient.post('/members/update_manage_member', params);

export {
    pageListMembers,
    // storeMembers,
    deleteMember,
    updateMember,
    manageMember
}