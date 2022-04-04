import ApiService from '../services/apiService';

const Api = new ApiService(),
      GetAPI = Api.getAPI,
      GetAPIwithType = Api.getAPIwithType,
      GetAPIWithError = Api.getAPIWithError,
      PostAPI = Api.postAPI;

export const GetAddressAvailability = (data) => {
return new Promise((resolve, reject) => {
    GetAPI(`/api/l2fiber/availability`, data)
        .then(res => {
            resolve(res)
        })
        .catch(err => {
            reject(err)
        })
})
}

export const Addresses = (data) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/l2fiber/addresses`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const CheckAddressConnectionRegistration = (data) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/l2fiber/connection`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const RegisterAddressConnection = (data) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/l2fiber/connection`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const ActivateAddressOnt = (data) => {
    return new Promise((resolve, reject) => {   
        GetAPIWithError(`/api/l2fiber/ont`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                console.error(JSON.stringify(err));
                reject(err)
            })
    })
}

export const ActivateOnt = (data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/l2fiber/connection`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const GetInvoice = (id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/sales_invoices/${id}`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const GenerateInvoice = (id) => {
    return new Promise((resolve, reject) => {
        GetAPIwithType(`/api/subscriptions/invoice/${id}`, 'blob')
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const SendInvoicEmail = (id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/sales_invoices/email/${id}`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export const AddressTermination = (data) => {
    return new Promise((resolve, reject) => {
        GetAPIWithError(`/api/l2fiber/terminate`, data)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}