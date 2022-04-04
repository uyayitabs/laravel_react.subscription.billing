import React from "react";
import { toastr } from "react-redux-toastr";

import { CardHeader, CardBody, Form, FormGroup, Label, Input, Button } from "reactstrap";

import { UpdateUser } from '../../../controllers/users';
import AuthService from '../../../services/authService';

const Auth = new AuthService();

class Security extends React.Component {
  constructor() {
    super();
    
    this.state = {
      id: '',
      password: '',
      password_confirmation: ''
    };

    this.handleChange = this.handleChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  handleChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  handleSubmit(e) {    
    e.preventDefault();
     
    let user = {
      _method: 'PATCH'
    };

    (async () => {
      if (this.state.password !== '' || this.state.password_confirmation !== '') {
        user.password = this.state.password;
        user.password_confirmation = this.state.password_confirmation;

        if (user.password !== user.password_confirmation) {
          return this.showToastr(404, 'Error', 'Passwords do not match.');
        }
      }
      
      await UpdateUser(this.state.id, user)
        .then(res => {
          this.showToastr(201, 'Success', 'Info has been updated.');
          this.setState({
            password: '',
            password_confirmation: ''
          })
        })
        .catch(err => {
          console.log(err.response)
          // this.showToastr(404, 'Error', 'Email has already been taken.');
        }); 
    })()
    .catch(err => {
      console.log(err.response)
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

    toastrInstance( title, msg, opts );
  }

  componentDidMount() {
    const user = Auth.getProfile()
    
    this.setState({ id: user.sub })
  }

  render() {
    return (
      <React.Fragment>
        <CardHeader>
          <h4 className="mb-0">Security</h4>
        </CardHeader>
        <CardBody>
          <Form onSubmit={ this.handleSubmit }>
            <FormGroup>
              <Label>New Password</Label>
                <Input
                  bsSize="lg"
                  type="password"
                  name="password"
                  value={ this.state.password }
                  onChange={ this.handleChange }
                />
            </FormGroup>
            <FormGroup>
              <Label>Verify Password</Label>
                <Input
                  bsSize="lg"
                  type="password"
                  name="password_confirmation"
                  value={ this.state.password_confirmation }
                  onChange={ this.handleChange }
                />
            </FormGroup>
            <FormGroup>
              <Button type="submit" color="primary">Save</Button>
            </FormGroup>
          </Form>
        </CardBody>
      </React.Fragment>    
    );
  }
}

export default Security;