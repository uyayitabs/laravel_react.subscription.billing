import ApiService from '../services/apiService';

const Api = new ApiService(),
  GetAPI = Api.getAPI,
  GetAPIwithType = Api.getAPIwithType,
  PostAPI = Api.postAPI;
  
const apiIncludes = 'tenant,relation,invoice-address,invoice-person,shipping-address,shipping-person,sales-invoice-lines,sales-invoice-lines.subscription-line,sales-invoice-lines.subscription-line.line-type,sales-invoice-lines.plan-line,sales-invoice-lines.plan-line.line-type';

export const GetInvoices = (params) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/sales_invoices`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetInvoicesCount = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/sales_invoices/count`)
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
    GetAPI(`/api/sales_invoices/${id}`, { include: apiIncludes })
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

export const AddInvoice = (invoice) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/sales_invoices`, invoice)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateInvoice = (id, invoice) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/sales_invoices/${id}`, invoice)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}