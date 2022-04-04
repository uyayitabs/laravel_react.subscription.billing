import ApiService from '../services/apiService';

const Api = new ApiService(),
    GetAPI = Api.getAPI,
    GetAPIwithType = Api.getAPIwithType,
    PostAPI = Api.postAPI;



export const M7Call = (method, data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/m7/` + method, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const CaptureSubscriber = (data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/m7/CaptureSubscriber`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const ChangePackage = (data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/m7/ChangePackage`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const ChangeAddress = (data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/m7/ChangeAddress`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const Disconnect = (data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/m7/Disconnect`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}