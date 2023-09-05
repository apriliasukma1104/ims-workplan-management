import httpClient from './httpClient';

const pageListDashboard = (params) => httpClient.get('/dashboard', {params});

export {
    pageListDashboard
}