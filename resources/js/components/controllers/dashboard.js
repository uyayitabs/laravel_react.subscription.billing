import ApiService from '../services/apiService';

const Api = new ApiService(),
    GetAPI = Api.getAPI;

const GetLatestSubscriptions = (params) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/subscriptions/latest`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetSubscriptionsCount = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/subscriptions/count`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetPersonsCount = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/persons/count`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}


export { GetLatestSubscriptions, GetSubscriptionsCount, GetPersonsCount }