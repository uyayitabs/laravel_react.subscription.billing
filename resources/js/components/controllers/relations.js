import ApiService from '../services/apiService';

const Api = new ApiService(),
    GetAPI = Api.getAPI,
    PostAPI = Api.postAPI;

const apiIncludes = 'persons,addresses';

const GetRelations = (params) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/relations?include=${apiIncludes}`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetRelationList = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/relations/list`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetRelation = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/relations/${id}`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetRelationsDependencies = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/dependencies/relations`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const AddRelation = (relation) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/relations`, relation)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const UpdateRelation = (id, relation) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/relations/${id}`, relation)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetPersons = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/relations/${id}/persons`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetPersonDependencies = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/dependencies/persons`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const AddPerson = (person) => {
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

const UpdatePerson = (id, person) => {
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

const GetAddress = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/relations/${id}/addresses`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetAddressDependencies = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/dependencies/address`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const AddAddress = (relation_id, address) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/addresses/${relation_id}`, address)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const UpdateAddress = (id, address) => {
    return new Promise((resolve, reject) => {
        PostAPI(`/api/addresses/${id}`, address)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetCountries = () => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/countries/list`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetStates = (id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/country/${id}/states`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetCities = (id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`/api/state/${id}/cities`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetSubscriptions = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`api/relations/${id}/subscriptions`, params)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

const GetInvoices = (params, id) => {
    return new Promise((resolve, reject) => {
        GetAPI(`api/relations/${id}/invoices`)
            .then(res => {
                resolve(res)
            })
            .catch(err => {
                reject(err)
            })
    })
}

export {
    // Relations
    GetRelations,
    GetRelationList,
    GetRelation,
    GetRelationsDependencies,
    AddRelation,
    UpdateRelation,

    // Persons
    GetPersonDependencies,
    AddPerson,
    UpdatePerson,
    GetPersons,

    // Addresses
    GetAddress,
    GetAddressDependencies,
    AddAddress,
    UpdateAddress,
    GetCountries,
    GetStates,
    GetCities,

    // Subscriptions
    GetSubscriptions,

    // Invoices
    GetInvoices
}