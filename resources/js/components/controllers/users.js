import ApiService from '../services/apiService';

const Api = new ApiService(),
  GetAPI = Api.getAPI,
  PostAPI = Api.postAPI;

export const GetUsers = (params) => {
  return new Promise((resolve, reject) => {    
    GetAPI(`/api/users`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetUser = (id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/users/${id}`, {include: 'tenant'})
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddUser = (user) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/users`, user)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateUser = (user, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/users/${id}`, user)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}