import ApiService from "../services/apiService";

const Api = new ApiService(),
    GetAPI = Api.getAPI,
    PostAPI = Api.postAPI;

const GetMyNumberRanges =(params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/mynumberranges/${id}`, params)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetNumberRangesCount = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/number_ranges/count`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetNumberRangeLists = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/number_ranges`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const GetNumberRange = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/number_ranges/${id}`, params)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const AddNumberRange = data => {
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

const UpdateNumberRange = (id, data) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/number_ranges/${id}`, data)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

const SwitchNumberRange = id => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/number_ranges/${id}/switch`)
            .then(res => {
                resolve(res);
            })
            .catch(err => {
                reject(err);
            });
    });
};

export {
    GetMyNumberRanges,
    GetNumberRangesCount,
    GetNumberRange,
    AddNumberRange,
    UpdateNumberRange,
    GetNumberRangeLists,
    SwitchNumberRange
};
