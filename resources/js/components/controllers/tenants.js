import ApiService from "../services/apiService";

const Api = new ApiService(),
    GetAPI = Api.getAPI,
    PostAPI = Api.postAPI;

const GetMyTenants = (params) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/mytenants`, params)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetTenantsCount = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/tenants/count`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetTenantLists = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/tenants/list`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetTenant = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/tenants/${id}`, params)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const AddTenant = data => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/tenants`, data)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const UpdateTenant = (id, data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/tenants/${id}`, data)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const SwitchTenant = id => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/tenants/${id}/switch`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetGroups = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`api/tenants/${id}/groups`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const AddGroup = (data) => {
    return new Promise((resolve, reject) => {
      PostAPI(`/api/tenants`, data)
        .then(res => {
          resolve(res)
        })
        .catch(err => {
          reject(err)
        })
    })
}

const UpdateGroup = (id, data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/groups/${id}`, data)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

export {
    GetMyTenants,
    GetTenantsCount,
    GetTenant,
    AddTenant,
    UpdateTenant,
    GetTenantLists,
    SwitchTenant,
    GetGroups,
    AddGroup,
    UpdateGroup
};
