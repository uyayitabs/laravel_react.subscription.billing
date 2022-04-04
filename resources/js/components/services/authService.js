import axios from "axios";
import decode from 'jwt-decode';

export default class AuthService {
    isError = false;
    // Initializing important variables
    constructor() {
        this.path = '/api/auth' // API server domain
    }

    login(params) {
        // Get a token from api server using the fetch api
        return new Promise((resolve, reject) => {
            this.fetch(`${ this.path }/login`, params)
                .then(res => {
                    this.setToken(res.data.access_token, res.config.data) // Setting the token in sessionStorage
                    sessionStorage.setItem('hasError', false)
                    sessionStorage.removeItem('error')
                    sessionStorage.setItem('tenant_id', res.data.tenant)

                    resolve(res);
                })
                .catch(err => {
                    reject(err);
                })
        })
    }

    loggedIn() {
        // Checks if there is a saved token and it's still valid
        const token = this.getToken() // Getting token from sessionStorage
        return !!token && !this.isTokenExpired(token) // handwaiving here
    }

    isTokenExpired(token) {
        try {
            const decoded = decode(token);
            if (decoded.exp < Date.now() / 1000) { // Checking if token is expired.
                return true;
            } else {
                return false;
            }
        } catch (err) {
            return false;
        }
    }

    setToken(idToken, data) {
        const profile = JSON.parse(data);
        // sessionStorage.setItem('email', profile.email)
        sessionStorage.setItem('id_token', idToken)
    }

    getToken() {
        // Retrieves the user token from sessionStorage
        return sessionStorage.getItem('id_token')
    }

    getEmail() {
        // Retrieves the user token from sessionStorage
        return sessionStorage.getItem('email')
    }

    logout() {
        // Clear user token and profile data from sessionStorage
        sessionStorage.clear()
    }

    getProfile() {
        // Using jwt-decode npm package to decode the token
        let decoded = decode(this.getToken())
        // decoded.email = this.getEmail()
        return decoded
    }

    fetch(url, user) {
        return new Promise((resolve, reject) => {
            axios.post(url, user)
                .then(this._checkStatus)
                .then(res => {
                    resolve(res)
                })
                .catch(err => {
                    reject(err.response)
                })
        })
    }

    _checkStatus(response) {
        // raises an error in case response status is not a success
        if (response.status >= 200 && response.status < 300) { // Success status lies between 200 to 300
            return response
        } else {
            var error = new Error(response.statusText)
            error.response = response
            throw error
        }
    }

    updateError(val, res) {
        sessionStorage.setItem('hasError', val)
        if (!val) {
            sessionStorage.removeItem('error')
        } else if (val) {
            const error = JSON.stringify({
                status: res.status,
                statusText: res.statusText
            })
            
            sessionStorage.setItem('error', error)
        }
    }

    checkIsError() {
        return sessionStorage.getItem('hasError') === 'true'
    }
}
