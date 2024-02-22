import httpClient from './httpClient';

const pageListMembers = (params) => httpClient.get('/members/list_members', {params});
const updateMember = (params) => httpClient.post('/members/list/update_member', params);
const updateStatusMember = (params) => httpClient.post('/members/list/update_status_member', params);

export {
    pageListMembers,
    updateMember,
    updateStatusMember
}