import httpClient from './httpClient';

const pageQueue = (params) => httpClient.get('/queue', {params});
const updateQueue = (params) => httpClient.post('/queue/update_queue', params);

export {
    pageQueue,
    updateQueue
}