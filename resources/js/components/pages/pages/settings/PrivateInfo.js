import React from "react";
import { toastr } from "react-redux-toastr";

import { CardHeader, CardBody, Form, FormGroup, Label, Input, Button } from "reactstrap";

import { GetUser, UpdateUser } from '../../../controllers/users';
import AuthService from '../../../services/authService';

const Auth = new AuthService();

class PrivateInfo extends React.Component {
  constructor() {
    super();
    
    this.state = {
      id: '',
      email: '',
      phone: '',
      mobile: ''
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
      username: this.state.username,
      email: this.state.email,
      phone: this.state.phone,
      mobile: this.state.mobile,
      _method: 'PATCH'
    };

    (async () => {      
      await UpdateUser(this.state.id, user)
        .then(res => {
          this.showToastr(201, 'Success', 'Info has been updated.');
        })
        .catch(err => {
          this.showToastr(404, 'Error', 'Email has already been taken.');
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
    (async () => {      
      const user = Auth.getProfile()
      
      this.setState({ id: user.sub })
      
      await GetUser(user.sub)
        .then(res => {
          const data = res.data.data;

          this.setState({ 
            email: data.email,
            phone: data.phone ? data.phone : '',
            mobile: data.mobile ? data.mobile : '' 
          })
        })
        .catch(err => {
          console.log(err)
        });
    })()
    .catch(err => {
      console.log(err)
    })
  }

  render() {
    return (
      <React.Fragment>
        <CardHeader>
          <h4 className="mb-0">Private Info</h4>
        </CardHeader>
        <CardBody>
          <Form onSubmit={ this.handleSubmit }>
            <FormGroup>
              <Label>Email</Label>
                <Input
                  bsSize="lg"
                  type="email"
                  name="email"
                  value={ this.state.email }
                  onChange={ this.handleChange }
                />
            </FormGroup>
            <FormGroup>
              <Label>Phone</Label>
                <Input
                  bsSize="lg"
                  type="phone"
                  name="phone"
                  value={ this.state.phone }
                  onChange={ this.handleChange }
                />
            </FormGroup>

            <FormGroup>
              <Label>Mobile</Label>
                <Input
                  bsSize="lg"
                  type="phone"
                  name="mobile"
                  value={ this.state.mobile }
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

export default PrivateInfo;