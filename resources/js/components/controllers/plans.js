import ApiService from '../services/apiService';

const Api = new ApiService(),
  GetAPI = Api.getAPI,
  PostAPI = Api.postAPI;

const apiIncludes = 'parent';

export const GetPlans = (params) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plans`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlanList = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plans/list`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlan = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plans/${id}`, { include: apiIncludes})
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetDependenciesPlans = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/dependencies/plans`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddPlan = (plan) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/plans`, plan)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdatePlan = (id, plan) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/plans/${id}`, plan)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlanLine = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plan_lines/${id}`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlanLines = (params, id) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plan/${id}/plan_lines`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetDependenciesPlanLines = () => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/dependencies/plan_lines`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddPlanLine = (plan_id, plan_line) => {
  return new Promise((resolve, reject) => {
    PostAPI(`api/plan_lines/${plan_id}`, plan_line)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdatePlanLine = (id, plan_line) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/plan_lines/${id}`, plan_line)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const AddPlanLinePrices = (params, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/plan_lines/${id}/plan_line_prices`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const UpdatePlanLinePrices = (params, id) => {
  return new Promise((resolve, reject) => {
    PostAPI(`/api/plan_line_prices/${id}`, params)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}

export const GetPlanLinePrices = (params, id, plan) => {
  return new Promise((resolve, reject) => {
    GetAPI(`/api/plans/${id}/plan_lines/${plan}/plan_line_prices/`)
      .then(res => {
        resolve(res)
      })
      .catch(err => {
        reject(err)
      })
  })
}