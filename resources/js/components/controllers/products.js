import ApiService from '../services/apiService';

const Api = new ApiService(),
  GetAPI = Api.getAPI,
  PostAPI = Api.postAPI;

const apiIncludes = 'serial,stock,product-type,subscription-lines';

export const GetProducts = (params) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/products`, params)
    .then(res => {
      resolve(res)
    })
    .catch(err => {
      reject(err)
    })
  })
}

export const GetProductsCount = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/products/count`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetProductsList = (params) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/products/list`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}


export const AddProduct = (product) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/products`, product)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateProduct = (params, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/products/${id}`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetProductTypes = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/product_types`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetProductTypeList = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/product_types/list`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}