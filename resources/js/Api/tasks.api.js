import httpClient from './httpClient';

const pageTasks = (params) => httpClient.get('/tasks', {params});

export {
    pageTasks
}