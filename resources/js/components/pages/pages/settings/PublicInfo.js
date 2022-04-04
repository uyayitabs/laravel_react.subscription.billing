import React from "react";
import Select from "react-select";
import { toastr } from "react-redux-toastr";

import { CardHeader, CardBody, Form, FormGroup, Label, Input, Button } from "reactstrap";

import { GetUser, UpdateUser } from '../../../controllers/users';
import AuthService from '../../../services/authService';

const Auth = new AuthService();

const genderOpts = [
  { value: "Male", label: "Male" },
  { value: "Female", label: "Female" }
];

const titleOpts = [
  { value: "Ms", label: "Ms" },
  { value: "Mr", label: "Mr" },
  { value: "Mrs", label: "Mrs" },
  { value: "Dr", label: "Dr" },
  { value: "Atty", label: "Atty" },
  { value: "Engr", label: "Engr" }
];

class PublicInfo extends React.Component {
  constructor() {
    super();
    
    this.state = {
      id: '',
      title: null,
      username: '',
      gender: null,
      linkedin: '',
      facebook: ''
    };

    this.handleChange = this.handleChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  handleChange(e) {
    this.setState({ [e.target.name]: e.target.value });
  }

  handleSelectChange = (name, value) => {
    let change = {};
    change[name] = value;
    this.setState(change);
  }

  handleSubmit(e) {    
    e.preventDefault();
     
    const user = {
      title: this.state.title.value,
      username: this.state.username,
      gender: this.state.gender.value,
      _method: 'PATCH'
    };

    (async () => {
      await UpdateUser(this.state.id, user)
        .then(res => {
          this.showToastr(201, 'Success', 'Info has been updated.');
        })
        .catch(err => {
          this.showToastr(404, 'Error', 'Username has already been taken.');
        }); 
    })()
    .catch(err => {
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

    toastrInstance( title, msg, opts );
  }

  componentDidMount() {
    (async () => {      
      const user = Auth.getProfile()
      
      this.setState({ id: user.sub })
      
      await GetUser(user.sub)
        .then(res => {
          const data = res.data.data;

          this.setState({ username: data.username })
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
          <h4 className="mb-0">Public Info</h4>
        </CardHeader>
        <CardBody>
          <Form onSubmit={ this.handleSubmit }>
            <FormGroup>
              <Label>Title</Label>
              <Select
                className="react-select-container react-select-lg"
                classNamePrefix="react-select"
                options={ titleOpts }
                value={ this.state.title }
                onChange={ this.handleSelectChange.bind( this, 'title' ) }
                maxMenuHeight="100"
              />
            </FormGroup>
            <FormGroup>
              <Label>Username</Label>
                <Input
                  bsSize="lg"
                  type="text"
                  name="username"
                  value={ this.state.username }
                  onChange={ this.handleChange }
                />
            </FormGroup>
            <FormGroup>
              <Label>Gender</Label>
              <Select
                className="react-select-container react-select-lg"
                classNamePrefix="react-select"
                options={ genderOpts }
                value={ this.state.gender }
                onChange={this.handleSelectChange.bind( this, 'gender' )}
                maxMenuHeight="100"
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

export default PublicInfo;