import httpClient from './httpClient';

const pageHistory = (params) => httpClient.get('/history', {params});
const approve = (params) => httpClient.post('/history/approve', params);
const notApprove = (params) => httpClient.post('/history/not_approve', params);

export {
    pageHistory,
    approve,
    notApprove
}