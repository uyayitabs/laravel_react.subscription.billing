import ApiService from '../services/apiService';

const Api = new ApiService(),
  GetAPI = Api.getAPI,
  PostAPI = Api.postAPI;

const apiIncludes = 'relation,plan';

export const GetSubscriptions = (params) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/subscriptions`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetSubscription = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/subscriptions/${id}`, { include: apiIncludes })
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetSubscriptionLine = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/subscription_lines/${id}`, { include: 'product.product-type,subscription-line-price,line-type' })
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetSubscriptionLines = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/subscriptions/${id}/subscription_lines`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetDependenciesSubscriptionLines = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/dependencies/subscription_lines`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlanSubscriptionLineTypes = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plan_subscription_line_types`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddSubscription = (subscription) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/subscriptions`, subscription)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateSubscription = (id, subscription) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/subscriptions/${id}`, subscription)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddSubscriptionLine = (subscription_id, subscription_line) => {
  return new Promise((resolve, reject) => {
    PostAPI(`api/subscription_lines/${subscription_id}`, subscription_line)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateSubscriptionLine = (id, subscription_line) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/subscription_lines/${id}`, subscription_line)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddSubscriptionLinePrice = (params, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/subscription_lines/${id}/subscription_line_prices`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdateSubscriptionLinePrice = (params, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/subscription_line_prices/${id}`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetSubscriptionLinePrices = (params, id, subscription) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/subscriptions/${id}/subscription_lines/${subscription}/subscription_line_prices/`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}