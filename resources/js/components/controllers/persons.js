import ApiService from '../services/apiService';

const Api = new ApiService(),
    GetAPI = Api.getAPI,
    PostAPI = Api.postAPI;

const apiIncludes = 'users,type,relation,relations-person,addresses';

export const GetPersons = (params) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/persons`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const GetPerson = (id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/persons/${id}`, { include: apiIncludes })
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const AddPerson = (person) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/persons`, person)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const UpdatePerson = (id, person) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/persons/${id}`, person)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const GetPersonTypesLists = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/person_types/list`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}