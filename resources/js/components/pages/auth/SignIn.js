import React from "react";
import { Link } from "react-router-dom";
import { toastr } from "react-redux-toastr";

import { Button, Card, CardBody, Form, FormGroup, Label, Input } from "reactstrap";

import AuthService from '../../services/authService';

class SignIn extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            email: '',
            password: ''
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleFormSubmit = this.handleFormSubmit.bind(this);

        this.Auth = new AuthService();
    }

    handleChange(e) {
        this.setState({ [e.target.name]: e.target.value });
    }

    handleFormSubmit(e) {
        e.preventDefault();

        const { email, password } = this.state

        const params = {
            email,
            password 
        };

        (async () => {
            await this.Auth.login(params)
                .then(res => {
                    this.showToastr(201, 'Success', 'Logged-in');
                    let checkSession = () => {
                        if (!sessionStorage.getItem('id_token') && !sessionStorage.getItem('email')) {
                            window.setTimeout(checkToken, 1);
                        } else {
                            this.props.history.replace('/');
                        }
                    }

                    checkSession();
                })
                .catch(err => {
                    let { data } = err;
                    this.showToastr(err.status, 'Error', data.message);
                })
        })().catch(err => {
            console.log(err)
        })


    }

    showToastr(type, title, msg) {
        const opts = {
            timeOut: 5000,
            showCloseButton: false,
            progressBar: false
        };

        const toastrInstance =
            type === 201 ? toastr.success
                : toastr.error;

        toastrInstance(title, msg, opts);
    }

    componentDidMount() {
        if (this.Auth.loggedIn()) {
            this.props.history.replace('/');
        }
    }

    render() {
        return (
            <React.Fragment>
                <Card>
                    <CardBody>
                        <div className="m-sm-4">
                            <Form onSubmit={this.handleFormSubmit}>
                                <FormGroup>
                                    <Label>Email</Label>
                                    <Input
                                        bsSize="lg"
                                        type="email"
                                        name="email"
                                        value={this.state.email}
                                        placeholder="Enter your email"
                                        onChange={this.handleChange}
                                    />
                                </FormGroup>
                                <FormGroup>
                                    <Label>Password</Label>
                                    <Input
                                        bsSize="lg"
                                        type="password"
                                        name="password"
                                        value={this.state.password}
                                        placeholder="Enter your password"
                                        onChange={this.handleChange}
                                    />
                                    <small>
                                        <Link to="/auth/reset-password">Forgot password?</Link>
                                    </small>
                                </FormGroup>
                                {/* <div>
                                    <CustomInput
                                        type="checkbox"
                                        id="rememberMe"
                                        label="Remember me next time"
                                        defaultChecked
                                    />
                                </div> */}
                                <div className="text-center mt-3">
                                    <Button color="primary" size="lg">Sign in</Button>
                                </div>
                            </Form>
                        </div>
                    </CardBody>
                </Card>
            </React.Fragment>
        )
    }
}

export default SignIn;
