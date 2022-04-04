import React from 'react';
import axios from 'axios';

import AuthService from './authService';

const Auth = new AuthService();

// New Endpoints' Base URL here
// axios.defaults.baseURL = ""

class ApiService extends React.Component {
    constructor() {
        super()
    }

    getAPI = (url, data) => {
        this.checkToken();

        return new Promise((resolve, reject) => {
            axios.get(url, { params: data })
                .then(res => {
                    resolve(res)
                })
                .catch(err => {
                    this.onError(err)

                    reject(err)
                })
        })
    }

    postAPI = (url, data) => {
        this.checkToken();

        return new Promise((resolve, reject) => {
            axios.post(url, data)
                .then(res => {
                    resolve(res)
                })
                .catch(err => {
                    this.onError(err)
                    
                    reject(err)
                })
        })
    }

    getAPIwithType = (url, responseType) => {
        this.checkToken();

        return new Promise((resolve, reject) => {
            axios({
                url,
                method: 'GET',
                responseType
            })
                .then(res => {
                    resolve(res)
                })
                .catch(err => {
                    reject(err)
                })
        })
    }

    getAPIWithError = (url, data) => {
        return new Promise((resolve, reject) => {
            axios.get(url, { params: data })
                .then(res => {
                    resolve(res)
                })
                .catch(err => {
                    let data = err.response.data;
                    let errorMessage = data.message.match(/\{(.*)message(.*)\:\"(.*)\"\}/);
                    if (errorMessage != null && errorMessage.length) {
                        let message = errorMessage[errorMessage.length - 1];
                        alert(message);
                    } else {
                        alert(data.message);
                    }
                    reject(err)
                })
        })
    }

    checkToken() {
        if (Auth.getToken() === null) {
            window.setTimeout(this.checkToken, 1);
        } else {
            axios.defaults.headers.common = {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${Auth.getToken()}`
            };
        }
    }

    onError(err) {
        console.log('apiService')
        console.log(err.response)

        Auth.updateError(true, err.response)

        if (!Auth.loggedIn() || err.response.status === 401) {
            Auth.logout()
            window.location.href = `/#/auth/sign-in`
        }
    }
}

export default ApiService;
