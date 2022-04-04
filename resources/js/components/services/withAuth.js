import React, { Component } from 'react';
import AuthService from './authService';

export default function withAuth(AuthComponent) {
    const Auth = new AuthService('/api/auth');

    return class AuthWrapped extends Component {
        constructor(props) {
            super(props);

            this.state = {
                user: null
            }

            this.logout = this.logout.bind(this);
        }

        logout() {
            Auth.logout()
            this.props.history.replace('/auth/sign-in')
        }

        componentDidMount() {
            if (!Auth.loggedIn()) {
                this.logout()
            } else {
                try {
                    const user = Auth.getProfile();

                    this.setState({ user })
                } catch (err) {
                    this.logout()
                }
            }
        }

        render() {
            if (this.state.user) {
                return <AuthComponent history={this.props.history} user={this.state.user} children={this.props.children} />
            } else {
                return null
            }
        }
    }
}