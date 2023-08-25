import httpClient from './httpClient';

const pageListReports = (params) => httpClient.get('/reports', {params});

export {
    pageListReports
}