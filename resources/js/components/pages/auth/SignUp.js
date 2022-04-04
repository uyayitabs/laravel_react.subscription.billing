import React from "react";
import axios from "axios";
import { toastr } from "react-redux-toastr";

import { Button, Card, CardBody, Form, FormGroup, Label, Input } from "reactstrap";

class SignUp extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      username: '',
      email: '',
      password: '',
      password_confirmation: ''
    }

    this.handleChange = this.handleChange.bind(this);
    this.handleFormSubmit = this.handleFormSubmit.bind(this);
  }  

  handleChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  handleFormSubmit(e) {
    e.preventDefault();

    let getState = this.state;

    axios.post('/api/auth/signup', getState)
      .then(res => {
        this.showToastr(201, 'Successfully Registered', res.data.message);
        this.props.history.push('/auth/sign-in');
      })
      .catch(err => {
        let msg = '';

        if (err.response.data.errors) {
          if (err.response.data.errors.email) {
            msg = err.response.data.errors.email[0];
          } else {
            msg = err.response.data.errors.password[0];
          }
        } else {
          msg = 'The username has already been taken.';
        }

        this.showToastr(404, 'Error', msg);
      })
  }

  showToastr(type, title, msg) {
    const opts = {
      timeOut: 5000,
      showCloseButton: false,
      progressBar: false
    };

    const toastrInstance =
      type === 201  ? toastr.success
      : toastr.error;

    toastrInstance( title, msg, opts );
  }

  render() {
    return(
      <React.Fragment>
        <div className="text-center mt-4">
          <h1 className="h2">Get started</h1>
          <p className="lead">
            Start creating the best possible user experience for you customers.
          </p>
        </div>

        <Card>
          <CardBody>
            <div className="m-sm-4">
              <Form onSubmit={ this.handleFormSubmit }>
                <FormGroup>
                  <Label>Username</Label>
                  <Input
                    bsSize="lg"
                    type="text"
                    name="username"
                    placeholder="Enter your username"
                    value={ this.username }
                    onChange={ this.handleChange }
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Email</Label>
                  <Input
                    bsSize="lg"
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value={ this.email }
                    onChange={ this.handleChange }
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Password</Label>
                  <Input
                    bsSize="lg"
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    value={ this.password }
                    onChange={ this.handleChange }
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Password Confirmation</Label>
                  <Input
                    bsSize="lg"
                    type="password"
                    name="password_confirmation"
                    placeholder="Re-enter your password"
                    value={ this.password_confirmation }
                    onChange={ this.handleChange }
                  />
                </FormGroup>
                <div className="text-center mt-3">
                  <Button color="primary" size="lg">Sign up</Button>
                </div>
              </Form>
            </div>
          </CardBody>
        </Card>
      </React.Fragment>
    );
  }
}

export default SignUp;
